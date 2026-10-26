<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_registration_is_throttled_after_repeated_attempts(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->from(route('register'))
                ->post(route('register'), [])
                ->assertRedirect(route('register'))
                ->assertSessionHasErrors();
        }

        $this->from(route('register'))
            ->post(route('register'), [])
            ->assertRedirect(route('register'))
            ->assertSessionHas('error');
    }

    public function test_paystack_initialization_is_throttled_per_user(): void
    {
        config([
            'services.paystack.secret_key' => 'sk_test_fake_secret',
            'services.paystack.payment_url' => 'https://api.paystack.co',
        ]);

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

        $seeker = $this->jobSeeker();
        $plan = $this->plan('premium-jobseeker-throttle', 'job_seeker', false);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->actingAs($seeker)
                ->post(route('payments.paystack.initialize', $plan))
                ->assertRedirect('https://checkout.paystack.com/test-checkout');
        }

        $this->actingAs($seeker)
            ->from(route('billing.index'))
            ->post(route('payments.paystack.initialize', $plan))
            ->assertRedirect(route('billing.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('payments', 5);
    }
}
