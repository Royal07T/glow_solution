<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\PaymentReceiptService;
use App\Services\PaymentService;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaystackPaymentController extends Controller
{
    public function initialize(Request $request, SubscriptionPlan $plan, PaystackService $paystack, PaymentService $payments)
    {
        $this->ensurePlanIsAvailable($request->user(), $plan);

        if ($plan->is_free || (float) $plan->price <= 0) {
            return back()->with('error', 'Free plans do not require payment.');
        }

        if ($paidSubscription = $request->user()->activePaidSubscription()) {
            return back()->with(
                'error',
                'Your '.$paidSubscription->plan->name.' plan is active until '.$paidSubscription->ends_at->format('M j, Y').'. Contact support on WhatsApp to change or renew your plan.'
            );
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
                'amount' => $payments->amountInSubunit($payment),
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

        $payment->update(['gateway_response' => $payments->sanitizeResponse($data)]);

        return redirect()->away($data['authorization_url']);
    }

    public function callback(Request $request, PaystackService $paystack, PaymentService $payments)
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
            $payments->markFailed($payment, $data);

            return redirect()->route('billing.index')->with('error', 'The Paystack payment was not successful.');
        }

        $payments->complete($payment, $data);
        $payment->refresh();

        if ($payment->status === 'success') {
            return redirect()->route('billing.index')->with('success', 'Payment confirmed. Your plan is now active.');
        }

        return redirect()->route('billing.index')->with('error', 'We could not activate your plan. Please contact support.');
    }

    public function webhook(Request $request, PaystackService $paystack, PaymentService $payments): JsonResponse
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
            $payments->complete($payment, $data);
        }

        return response()->json(['status' => 'success']);
    }

    public function receipt(Request $request, Payment $payment, PaymentReceiptService $receipts)
    {
        abort_unless($payment->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        abort_unless($payment->status === 'success', 404);

        return response($receipts->pdf($payment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$receipts->filename($payment).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function ensurePlanIsAvailable($user, SubscriptionPlan $plan): void
    {
        abort_unless($plan->is_active, 404);

        $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        abort_if($plan->target_role && $plan->target_role !== $role, 403);
    }
}
