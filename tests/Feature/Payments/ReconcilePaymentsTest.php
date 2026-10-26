<?php

namespace Tests\Feature\Payments;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class ReconcilePaymentsTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.paystack.secret_key' => 'sk_test_fake_secret',
            'services.paystack.payment_url' => 'https://api.paystack.co',
        ]);

        Mail::fake();
    }

    public function test_pending_payment_is_confirmed_when_paystack_reports_success(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-reconcile', 'job_seeker', false);
        $payment = $this->pendingPayment($seeker, $plan);

        Http::fake([
            'https://api.paystack.co/transaction/verify/'.$payment->reference => Http::response([
                'status' => true,
                'data' => $this->successfulCharge($payment),
            ]),
        ]);

        $this->artisan('payments:reconcile')->assertSuccessful();

        $payment->refresh();

        $this->assertSame('success', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertNotNull($payment->subscription_id);
        $this->assertSame('active', $payment->subscription->status);
        $this->assertDatabaseCount('subscriptions', 1);
    }

    public function test_pending_payment_is_marked_failed_when_paystack_reports_failure(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-reconcile-failed', 'job_seeker', false);
        $payment = $this->pendingPayment($seeker, $plan);

        Http::fake([
            'https://api.paystack.co/transaction/verify/'.$payment->reference => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'failed',
                    'reference' => $payment->reference,
                    'amount' => (int) round((float) $payment->amount * 100),
                    'currency' => $payment->currency,
                ],
            ]),
        ]);

        $this->artisan('payments:reconcile')->assertSuccessful();

        $this->assertSame('failed', $payment->fresh()->status);
        $this->assertDatabaseCount('subscriptions', 0);
    }

    public function test_reconciliation_requires_paystack_configuration(): void
    {
        config(['services.paystack.secret_key' => null]);

        $this->artisan('payments:reconcile')
            ->expectsOutput('Paystack is not configured.')
            ->assertFailed();
    }

    private function pendingPayment(User $user, SubscriptionPlan $plan): Payment
    {
        return Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'reference' => 'GLOW-RECONCILE-'.strtoupper(fake()->unique()->bothify('????####')),
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'status' => 'pending',
            'gateway' => 'paystack',
        ]);
    }

    private function successfulCharge(Payment $payment): array
    {
        return [
            'status' => 'success',
            'reference' => $payment->reference,
            'amount' => (int) round((float) $payment->amount * 100),
            'currency' => $payment->currency,
            'metadata' => [
                'payment_id' => $payment->id,
                'plan_id' => $payment->subscription_plan_id,
            ],
        ];
    }
}
