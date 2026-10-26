<?php

namespace Tests\Feature\Security;

use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class BillingAndJobPostingTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_employer_can_activate_a_free_plan_for_their_role(): void
    {
        $employer = $this->employer();
        $plan = $this->plan('starter-employer', 'employer', true, ['active_job_listings' => 1]);

        $this->actingAs($employer)
            ->post(route('subscriptions.activate', $plan))
            ->assertRedirect();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $employer->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'subscription.activated',
            'user_id' => $employer->id,
        ]);
    }

    public function test_paid_plans_cannot_be_activated_without_payment(): void
    {
        $employer = $this->employer();
        $plan = $this->plan('premium-employer', 'employer', false);

        $this->actingAs($employer)
            ->post(route('subscriptions.activate', $plan))
            ->assertForbidden();

        $this->assertDatabaseCount('subscriptions', 0);
    }

    public function test_employer_cannot_activate_a_job_seeker_plan(): void
    {
        $employer = $this->employer();
        $plan = $this->plan('free-jobseeker', 'job_seeker', true);

        $this->actingAs($employer)
            ->post(route('subscriptions.activate', $plan))
            ->assertForbidden();
    }

    public function test_job_seeker_with_an_active_paid_plan_cannot_switch_to_a_free_plan_early(): void
    {
        $seeker = $this->jobSeeker();
        $paidPlan = $this->plan('premium-jobseeker-active', 'job_seeker', false);
        $freePlan = $this->plan('free-jobseeker-active', 'job_seeker', true);
        $subscription = $this->subscribe($seeker, $paidPlan);

        $this->actingAs($seeker)
            ->post(route('subscriptions.activate', $freePlan))
            ->assertRedirect(route('billing.index'))
            ->assertSessionHas('error');

        $subscription->refresh();

        $this->assertSame($paidPlan->id, $subscription->subscription_plan_id);
        $this->assertSame('active', $subscription->status);
        $this->assertDatabaseCount('subscriptions', 1);
    }

    public function test_job_seeker_can_switch_to_a_free_plan_after_the_paid_plan_expires(): void
    {
        $seeker = $this->jobSeeker();
        $paidPlan = $this->plan('premium-jobseeker-expired', 'job_seeker', false);
        $freePlan = $this->plan('free-jobseeker-expired', 'job_seeker', true);
        $subscription = $this->subscribe($seeker, $paidPlan);
        $subscription->update(['ends_at' => now()->subDay()]);

        $this->actingAs($seeker)
            ->post(route('subscriptions.activate', $freePlan))
            ->assertRedirect(route('jobseeker.dashboard'))
            ->assertSessionHas('success');

        $this->assertSame($freePlan->id, $subscription->fresh()->subscription_plan_id);
        $this->assertSame('active', $subscription->fresh()->status);
    }

    public function test_billing_hides_free_activation_while_a_paid_plan_is_active(): void
    {
        $seeker = $this->jobSeeker();
        $paidPlan = $this->plan('premium-jobseeker-billing', 'job_seeker', false);
        $freePlan = $this->plan('free-jobseeker-billing', 'job_seeker', true);
        $this->subscribe($seeker, $paidPlan);

        $this->actingAs($seeker)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee('Paid plan active')
            ->assertDontSee(route('subscriptions.activate', $freePlan), false);
    }

    public function test_employer_without_a_subscription_is_redirected_to_billing(): void
    {
        $employer = $this->employer();

        $this->actingAs($employer)
            ->get(route('employer.jobs.create'))
            ->assertRedirect(route('subscriptions.plans'));

        $this->actingAs($employer)
            ->post(route('employer.jobs.store'), $this->jobPayload())
            ->assertRedirect(route('subscriptions.plans'));

        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_employer_with_a_plan_can_submit_a_job_for_moderation(): void
    {
        $employer = $this->employerWithPlan();

        $this->actingAs($employer)
            ->post(route('employer.jobs.store'), $this->jobPayload())
            ->assertRedirect(route('employer.jobs.index'));

        $this->assertDatabaseHas('jobs', [
            'employer_profile_id' => $employer->employerProfile->id,
            'title' => 'Live-in Housekeeper',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('audit_logs', ['action' => 'job.created']);
    }

    public function test_job_payload_rejects_an_inverted_salary_range(): void
    {
        $employer = $this->employerWithPlan();

        $this->actingAs($employer)
            ->post(route('employer.jobs.store'), $this->jobPayload([
                'salary_min' => 500000,
                'salary_max' => 100000,
            ]))
            ->assertSessionHasErrors('salary_max');

        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_editing_a_live_job_sends_it_back_for_moderation(): void
    {
        $employer = $this->employerWithPlan();
        $job = $this->job($employer);

        $this->actingAs($employer)
            ->put(route('employer.jobs.update', $job), $this->jobPayload(['title' => 'Updated Housekeeper']))
            ->assertRedirect(route('employer.jobs.index'));

        $job->refresh();

        $this->assertSame('pending', $job->status);
        $this->assertNull($job->published_at);
    }

    private function jobPayload(array $overrides = []): array
    {
        $category = JobCategory::first() ?? JobCategory::factory()->create();

        return array_merge([
            'title' => 'Live-in Housekeeper',
            'type' => 'domestic',
            'job_category_id' => $category->id,
            'employment_type' => 'live_in',
            'description' => 'Manage a private residence in Lagos.',
            'requirements' => 'Experience required',
            'location' => 'Ikoyi, Lagos',
            'salary_min' => 180000,
            'salary_max' => 250000,
            'salary_currency' => 'NGN',
            'positions_available' => 1,
        ], $overrides);
    }
}
