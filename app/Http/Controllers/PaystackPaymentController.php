<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Notifications\SystemNotification;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackPaymentController extends Controller
{
    public function initialize(Request $request, SubscriptionPlan $plan, PaystackService $paystack)
    {
        $this->ensurePlanIsAvailable($request->user(), $plan);

        if ($plan->is_free || (float) $plan->price <= 0) {
            return back()->with('error', 'Free plans do not require payment.');
        }

        if (! $paystack->isConfigured()) {
            return back()->with('error', 'Paystack is not configured yet. Please contact support.');
        }

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'subscription_plan_id' => $plan->id,
            'reference' => 'GLOW-'.Str::ulid(),
            'amount' => $plan->price,
            'currency' => strtoupper($plan->currency ?: 'NGN'),
            'status' => 'pending',
            'gateway' => 'paystack',
        ]);

        try {
            $data = $paystack->initializeTransaction([
                'email' => $request->user()->email,
                'amount' => $this->amountInSubunit($payment),
                'currency' => $payment->currency,
                'reference' => $payment->reference,
                'callback_url' => route('payments.paystack.callback'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $request->user()->id,
                    'plan_id' => $plan->id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            $payment->update([
                'status' => 'failed',
                'gateway_response' => ['message' => 'Unable to initialize payment.'],
            ]);

            return back()->with('error', 'We could not start the Paystack payment. Please try again.');
        }

        $payment->update(['gateway_response' => $data]);

        return redirect()->away($data['authorization_url']);
    }

    public function callback(Request $request, PaystackService $paystack)
    {
        $reference = (string) ($request->query('reference') ?: $request->query('trxref'));

        if ($reference === '') {
            return redirect()->route('billing.index')->with('error', 'The payment reference is missing.');
        }

        $payment = Payment::query()
            ->where('reference', $reference)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($payment->status === 'success') {
            return redirect()->route('billing.index')->with('success', 'This payment has already been confirmed.');
        }

        try {
            $data = $paystack->verifyTransaction($reference);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('billing.index')->with('error', 'We could not verify the Paystack payment. Please contact support if you were charged.');
        }

        if (($data['status'] ?? null) !== 'success') {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $data,
            ]);

            return redirect()->route('billing.index')->with('error', 'The Paystack payment was not successful.');
        }

        $this->completePayment($payment, $data);
        $payment->refresh();

        if ($payment->status === 'success') {
            return redirect()->route('billing.index')->with('success', 'Payment confirmed. Your plan is now active.');
        }

        return redirect()->route('billing.index')->with('error', 'We could not activate your plan. Please contact support.');
    }

    public function webhook(Request $request, PaystackService $paystack): JsonResponse
    {
        if (! $paystack->isConfigured()) {
            return response()->json(['message' => 'Paystack is not configured.'], 503);
        }

        if (! $paystack->hasValidWebhookSignature($request->getContent(), $request->header('x-paystack-signature'))) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        if ($request->input('event') !== 'charge.success') {
            return response()->json(['status' => 'ignored']);
        }

        $data = (array) $request->input('data', []);
        $reference = $data['reference'] ?? null;

        if (! $reference || ($data['status'] ?? null) !== 'success') {
            return response()->json(['status' => 'ignored']);
        }

        $payment = Payment::query()->where('reference', $reference)->first();

        if (! $payment) {
            return response()->json(['status' => 'ignored']);
        }

        if ($payment->status !== 'success') {
            $this->completePayment($payment, $data);
        }

        return response()->json(['status' => 'success']);
    }

    private function completePayment(Payment $payment, array $data): bool
    {
        $completed = DB::transaction(function () use ($payment, $data): bool {
            $locked = Payment::query()
                ->with(['user', 'plan', 'subscription'])
                ->lockForUpdate()
                ->find($payment->id);

            if (! $locked || $locked->status === 'success') {
                return false;
            }

            if (! $this->matchesPayment($locked, $data)) {
                Log::warning('Paystack payment amount or currency mismatch.', [
                    'payment_id' => $locked->id,
                    'reference' => $locked->reference,
                ]);

                $locked->update([
                    'status' => 'failed',
                    'gateway_response' => $data,
                ]);

                return false;
            }

            $plan = $locked->plan;

            if (! $plan) {
                $locked->update([
                    'status' => 'failed',
                    'gateway_response' => $data,
                ]);

                return false;
            }

            $subscription = $locked->subscription
                ?? $locked->user->subscriptions()->latest()->first();

            $attributes = [
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $plan->endsAt(),
                'cancelled_at' => null,
            ];

            if ($subscription) {
                $subscription->update($attributes);
            } else {
                $subscription = $locked->user->subscriptions()->create($attributes);
            }

            $locked->update([
                'subscription_id' => $subscription->id,
                'status' => 'success',
                'paid_at' => now(),
                'gateway_response' => $data,
            ]);

            try {
                AuditLog::record('payment.success', $locked);
                AuditLog::record('subscription.activated', $subscription);
            } catch (\Throwable $e) {
            }

            return true;
        });

        if ($completed) {
            $payment->refresh()->loadMissing(['user', 'plan']);

            $payment->user?->notify(new SystemNotification(
                'Payment successful',
                'Your '.($payment->plan?->name ?? 'subscription').' plan is now active.',
                route('billing.index', absolute: false),
                'success',
            ));
        }

        return $completed;
    }

    private function matchesPayment(Payment $payment, array $data): bool
    {
        return (int) ($data['amount'] ?? -1) === $this->amountInSubunit($payment)
            && strtoupper((string) ($data['currency'] ?? '')) === strtoupper($payment->currency);
    }

    private function amountInSubunit(Payment $payment): int
    {
        return (int) round((float) $payment->amount * 100);
    }

    private function ensurePlanIsAvailable($user, SubscriptionPlan $plan): void
    {
        abort_unless($plan->is_active, 404);

        $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        abort_if($plan->target_role && $plan->target_role !== $role, 403);
    }
}
