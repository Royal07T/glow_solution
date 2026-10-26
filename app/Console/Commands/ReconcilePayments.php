<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\PaystackService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcilePayments extends Command
{
    protected $signature = 'payments:reconcile
                            {--days=3 : Only check pending payments created in the last N days}
                            {--limit=100 : Maximum number of payments to check}';

    protected $description = 'Verify pending Paystack payments and recover missed webhooks';

    public function handle(PaystackService $paystack, PaymentService $payments): int
    {
        if (! $paystack->isConfigured()) {
            $this->error('Paystack is not configured.');

            return self::FAILURE;
        }

        $days = max(1, (int) $this->option('days'));
        $limit = max(1, min(500, (int) $this->option('limit')));

        $pending = Payment::query()
            ->where('status', 'pending')
            ->where('gateway', 'paystack')
            ->where('created_at', '>=', now()->subDays($days))
            ->oldest()
            ->limit($limit)
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending Paystack payments to reconcile.');

            return self::SUCCESS;
        }

        $confirmed = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($pending as $payment) {
            try {
                $data = $paystack->verifyTransaction($payment->reference);
            } catch (\Throwable $e) {
                $skipped++;
                Log::warning('Unable to verify pending Paystack payment.', [
                    'payment_id' => $payment->id,
                    'reference' => $payment->reference,
                ]);

                continue;
            }

            $status = (string) ($data['status'] ?? '');

            if ($status === 'success') {
                if ($payments->complete($payment, $data)) {
                    $confirmed++;
                    $this->line('Confirmed '.$payment->reference);
                } else {
                    $failed++;
                    $this->warn('Could not complete '.$payment->reference);
                }

                continue;
            }

            if (in_array($status, ['failed', 'reversed', 'abandoned'], true)) {
                $payments->markFailed($payment, $data);
                $failed++;
                $this->line('Marked '.$payment->reference.' as failed');

                continue;
            }

            $skipped++;
        }

        $this->info("Reconciliation complete: {$confirmed} confirmed, {$failed} failed, {$skipped} skipped.");

        return self::SUCCESS;
    }
}
