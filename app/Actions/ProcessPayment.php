<?php

namespace App\Actions;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ProcessPayment
{
    public function execute(array $payload): void
    {
        DB::transaction(function () use ($payload) {
            $reference = $payload['data']['reference'] ?? null;
            $eventId = $payload['event'] . '_' . ($payload['data']['id'] ?? $reference);
            
            // Simple idempotency check via payment record
            $payment = Payment::where('reference', $reference)->lockForUpdate()->first();
            
            if (!$payment) {
                return;
            }
            
            if ($payment->status === 'success') {
                return; // Already processed
            }
            
            if ($payload['event'] === 'charge.success') {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'gateway_response' => $payload,
                ]);
            }
        });
    }
}
