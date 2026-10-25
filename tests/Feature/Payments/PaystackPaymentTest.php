<?php

namespace Tests\Feature\Payments;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class PaystackPaymentTest extends TestCase
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
    }

    public function test_paid_plan_initializes_a_paystack_transaction(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-test', 'job_seeker', false);
        $amount = (int) round((float) $plan->price * 100);

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-checkout',
                    'access_code' => 'test-access-code',
                    'reference' => 'test-reference',
                ],
            ]),
        ]);

        $this->actingAs($seeker)
            ->post(route('payments.paystack.initialize', $plan))
            ->assertRedirect('https://checkout.paystack.com/test-checkout');

        $payment = Payment::firstOrFail();

        $this->assertSame($seeker->id, $payment->user_id);
        $this->assertSame($plan->id, $payment->subscription_plan_id);
        $this->assertSame('pending', $payment->status);
        $this->assertSame('NGN', $payment->currency);
        $this->assertStringStartsWith('GLOW-', $payment->reference);

        Http::assertSent(function (ClientRequest $request) use ($amount, $seeker, $plan, $payment) {
            $data = $request->data();

            return $request->url() === 'https://api.paystack.co/transaction/initialize'
                && $data['email'] === $seeker->email
                && $data['amount'] === $amount
                && $data['currency'] === 'NGN'
                && $data['reference'] === $payment->reference
                && $data['callback_url'] === route('payments.paystack.callback')
                && $data['metadata']['plan_id'] === $plan->id;
        });
    }

    public function test_paystack_callback_activates_the_subscription_once(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-callback', 'job_seeker', false);
        $payment = $this->pendingPayment($seeker, $plan);

        Http::fake([
            'https://api.paystack.co/transaction/verify/'.$payment->reference => Http::response([
                'status' => true,
                'data' => $this->successfulCharge($payment),
            ]),
        ]);

        $this->actingAs($seeker)
            ->get(route('payments.paystack.callback', ['reference' => $payment->reference]))
            ->assertRedirect(route('billing.index'))
            ->assertSessionHas('success');

        $payment->refresh();

        $this->assertSame('success', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertNotNull($payment->subscription_id);

        $subscription = $payment->subscription;

        $this->assertSame('active', $subscription->status);
        $this->assertSame($plan->id, $subscription->subscription_plan_id);
        $this->assertTrue($subscription->ends_at->isFuture());
        $this->assertDatabaseCount('subscriptions', 1);

        $this->actingAs($seeker)
            ->get(route('payments.paystack.callback', ['reference' => $payment->reference]))
            ->assertRedirect(route('billing.index'));

        $this->assertDatabaseCount('subscriptions', 1);
        $this->assertSame(1, Payment::where('status', 'success')->count());
    }

    public function test_paystack_callback_rejects_a_mismatched_amount(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-mismatch', 'job_seeker', false);
        $payment = $this->pendingPayment($seeker, $plan);
        $charge = $this->successfulCharge($payment);
        $charge['amount'] = 1;

        Http::fake([
            'https://api.paystack.co/transaction/verify/'.$payment->reference => Http::response([
                'status' => true,
                'data' => $charge,
            ]),
        ]);

        $this->actingAs($seeker)
            ->get(route('payments.paystack.callback', ['reference' => $payment->reference]))
            ->assertRedirect(route('billing.index'))
            ->assertSessionHas('error');

        $this->assertSame('failed', $payment->fresh()->status);
        $this->assertDatabaseCount('subscriptions', 0);
    }

    public function test_valid_paystack_webhook_activates_the_subscription(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-webhook', 'job_seeker', false);
        $payment = $this->pendingPayment($seeker, $plan);
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => $this->successfulCharge($payment),
        ]);

        $this->call(
            'POST',
            route('payments.paystack.webhook'),
            [],
            [],
            [],
            [
                'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, config('services.paystack.secret_key')),
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $payload,
        )->assertOk()->assertJson(['status' => 'success']);

        $this->assertSame('success', $payment->fresh()->status);
        $this->assertSame('active', $payment->fresh()->subscription->status);
    }

    public function test_invalid_paystack_webhook_signature_is_rejected(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-invalid-signature', 'job_seeker', false);
        $payment = $this->pendingPayment($seeker, $plan);
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => $this->successfulCharge($payment),
        ]);

        $this->call(
            'POST',
            route('payments.paystack.webhook'),
            [],
            [],
            [],
            [
                'HTTP_X_PAYSTACK_SIGNATURE' => 'invalid-signature',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $payload,
        )->assertStatus(401);

        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertDatabaseCount('subscriptions', 0);
    }

    public function test_user_cannot_pay_for_a_plan_for_another_role(): void
    {
        $employer = $this->employer();
        $plan = $this->plan('premium-jobseeker-role-check', 'job_seeker', false);

        $this->actingAs($employer)
            ->post(route('payments.paystack.initialize', $plan))
            ->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_billing_page_shows_paystack_checkout_for_paid_plans(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-button', 'job_seeker', false);

        $this->actingAs($seeker)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee($plan->name)
            ->assertSee('with Paystack')
            ->assertSee(route('payments.paystack.initialize', $plan), false);
    }

    public function test_paystack_initialization_failure_marks_the_payment_failed(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-init-failure', 'job_seeker', false);

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'No active channel to process transaction. Please contact merchant',
            ], 400),
        ]);

        $this->actingAs($seeker)
            ->from(route('billing.index'))
            ->post(route('payments.paystack.initialize', $plan))
            ->assertRedirect(route('billing.index'))
            ->assertSessionHas('error');

        $this->assertSame('failed', Payment::firstOrFail()->status);
        $this->assertDatabaseCount('subscriptions', 0);
    }

    private function pendingPayment(User $user, SubscriptionPlan $plan): Payment
    {
        return Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'reference' => 'GLOW-TEST-'.strtoupper(fake()->unique()->bothify('????####')),
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
