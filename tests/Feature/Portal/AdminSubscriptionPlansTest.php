<?php

namespace Tests\Feature\Portal;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class AdminSubscriptionPlansTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_admin_can_create_a_priced_plan(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.subscription-plans.store'), [
                'name' => 'Gold Employer',
                'target_role' => 'employer',
                'interval' => 'monthly',
                'price' => 18500.50,
                'currency' => 'ngn',
                'description' => 'Premium hiring plan',
                'entitlements' => '{"active_job_listings": -1}',
                'is_active' => '1',
                'sort_order' => 4,
            ])
            ->assertRedirect(route('admin.subscription-plans.index'))
            ->assertSessionHasNoErrors();

        $plan = SubscriptionPlan::where('name', 'Gold Employer')->firstOrFail();

        $this->assertSame('gold-employer', $plan->slug);
        $this->assertSame('18500.50', $plan->price);
        $this->assertSame('NGN', $plan->currency);
        $this->assertFalse($plan->is_free);
        $this->assertTrue($plan->is_active);
        $this->assertSame(-1, $plan->entitlements['active_job_listings']);
    }

    public function test_admin_can_update_a_plan_price(): void
    {
        $plan = $this->plan('employer-plan-'.uniqid(), 'employer', false);

        $this->actingAs($this->admin())
            ->put(route('admin.subscription-plans.update', $plan), [
                'name' => $plan->name,
                'slug' => $plan->slug,
                'target_role' => $plan->target_role,
                'interval' => 'annual',
                'price' => 0,
                'currency' => 'NGN',
                'entitlements' => '{"active_job_listings": 3}',
                'sort_order' => 2,
            ])
            ->assertRedirect(route('admin.subscription-plans.index'))
            ->assertSessionHasNoErrors();

        $plan->refresh();

        $this->assertSame('0.00', $plan->price);
        $this->assertSame('annual', $plan->interval);
        $this->assertTrue($plan->is_free);
        $this->assertFalse($plan->is_active);
        $this->assertSame(3, $plan->entitlements['active_job_listings']);
    }

    public function test_plan_with_subscribers_cannot_be_deleted(): void
    {
        $employer = $this->employerWithPlan();
        $plan = $employer->subscriptions()->first()->plan;

        $this->actingAs($this->admin())
            ->delete(route('admin.subscription-plans.destroy', $plan))
            ->assertRedirect(route('admin.subscription-plans.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('subscription_plans', ['id' => $plan->id]);
    }

    public function test_unused_plan_can_be_deleted(): void
    {
        $plan = $this->plan('unused-plan', 'job_seeker');

        $this->actingAs($this->admin())
            ->delete(route('admin.subscription-plans.destroy', $plan))
            ->assertRedirect(route('admin.subscription-plans.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('subscription_plans', ['id' => $plan->id]);
    }

    public function test_invalid_entitlements_json_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.subscription-plans.store'), [
                'name' => 'Broken Plan',
                'target_role' => 'employer',
                'interval' => 'monthly',
                'price' => 1000,
                'currency' => 'NGN',
                'entitlements' => '{not valid json',
            ])
            ->assertSessionHasErrors('entitlements');

        $this->assertDatabaseMissing('subscription_plans', ['name' => 'Broken Plan']);
    }

    public function test_non_admin_cannot_manage_plans(): void
    {
        $this->actingAs($this->jobSeeker())
            ->get(route('admin.subscription-plans.create'))
            ->assertForbidden();
    }
}
