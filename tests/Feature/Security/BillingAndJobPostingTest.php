<?php

namespace Tests\Feature\Security;

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
        return array_merge([
            'title' => 'Live-in Housekeeper',
            'type' => 'domestic',
            'employment_type' => 'live_in',
            'description' => 'Manage a private residence in Lagos.',
            'location' => 'Ikoyi, Lagos',
            'salary_min' => 180000,
            'salary_max' => 250000,
            'salary_currency' => 'NGN',
            'positions_available' => 1,
        ], $overrides);
    }
}
