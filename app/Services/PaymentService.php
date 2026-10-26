<?php

namespace App\Services;

use App\Mail\PaymentReceiptMail;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Notifications\SystemNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function complete(Payment $payment, array $data): bool
    {
        $completed = DB::transaction(function () use ($payment, $data): bool {
            $locked = Payment::query()
                ->with(['user', 'plan', 'subscription'])
                ->lockForUpdate()
                ->find($payment->id);

            if (! $locked || $locked->status === 'success') {
                return false;
            }

            if (($data['status'] ?? null) !== 'success' || ! $this->matches($locked, $data)) {
                Log::warning('Paystack payment verification mismatch.', [
                    'payment_id' => $locked->id,
                    'reference' => $locked->reference,
                ]);

                $this->markFailed($locked, $data);

                return false;
            }

            $plan = $locked->plan;

            if (! $plan) {
                $this->markFailed($locked, $data);

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
                'gateway_response' => $this->sanitizeResponse($data),
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

            $this->sendConfirmation($payment);
        }

        return $completed;
    }

    public function markFailed(Payment $payment, array $data): void
    {
        if ($payment->status === 'success') {
            return;
        }

        $payment->update([
            'status' => 'failed',
            'gateway_response' => $this->sanitizeResponse($data),
        ]);
    }

    public function matches(Payment $payment, array $data): bool
    {
        return hash_equals($payment->reference, (string) ($data['reference'] ?? ''))
            && (int) ($data['amount'] ?? -1) === $this->amountInSubunit($payment)
            && strtoupper((string) ($data['currency'] ?? '')) === strtoupper($payment->currency);
    }

    public function sanitizeResponse(array $data): array
    {
        return array_filter(
            Arr::only($data, [
                'id',
                'status',
                'reference',
                'amount',
                'currency',
                'channel',
                'paid_at',
                'created_at',
                'transaction_date',
                'gateway_response',
                'message',
            ]),
            fn ($value) => $value !== null,
        );
    }

    public function amountInSubunit(Payment $payment): int
    {
        return (int) round((float) $payment->amount * 100);
    }

    private function sendConfirmation(Payment $payment): void
    {
        $user = $payment->user;

        if (! $user) {
            return;
        }

        try {
            $user->notify(new SystemNotification(
                'Payment successful',
                'Your '.($payment->plan?->name ?? 'subscription').' plan is now active.',
                route('billing.index', absolute: false),
                'success',
            ));
        } catch (\Throwable $e) {
            Log::warning('Payment notification failed.', ['payment_id' => $payment->id]);
        }

        if (! filled($user->email)) {
            return;
        }

        try {
            Mail::to($user)->send(new PaymentReceiptMail($payment));
        } catch (\Throwable $e) {
            Log::warning('Payment receipt email failed.', ['payment_id' => $payment->id]);
        }
    }
}
