<?php

namespace Tests\Feature\Portal;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class NewPagesTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_job_seeker_recommended_and_interviews_pages_render(): void
    {
        $seeker = $this->jobSeeker();
        $employer = $this->employer();
        $interviewJob = $this->job($employer);
        $recommendedJob = $this->job($employer);
        $this->application($seeker, $interviewJob, ['status' => ApplicationStatus::INTERVIEW]);

        $this->actingAs($seeker)
            ->get(route('jobseeker.recommended'))
            ->assertOk()
            ->assertSee($recommendedJob->title)
            ->assertDontSee($interviewJob->title);

        $this->actingAs($seeker)
            ->get(route('jobseeker.interviews'))
            ->assertOk()
            ->assertSee($interviewJob->title);
    }

    public function test_saved_job_toggle_adds_and_removes_job(): void
    {
        $seeker = $this->jobSeeker();
        $job = $this->job($this->employer());

        $this->actingAs($seeker)
            ->post(route('jobs.save', $job))
            ->assertRedirect();

        $this->assertDatabaseHas('saved_jobs', [
            'user_id' => $seeker->id,
            'job_id' => $job->id,
        ]);

        $this->actingAs($seeker)
            ->delete(route('saved-jobs.destroy', $job))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_jobs', [
            'user_id' => $seeker->id,
            'job_id' => $job->id,
        ]);
    }

    public function test_employer_dashboard_renders_subscription_and_jobs(): void
    {
        $employer = $this->employerWithPlan();
        $job = $this->job($employer);

        $this->actingAs($employer)
            ->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee($job->title)
            ->assertSee('Active');
    }

    public function test_admin_dashboard_lists_pending_jobs_and_plans(): void
    {
        $admin = $this->admin();
        $employer = $this->employer();
        $pending = $this->job($employer, ['status' => 'pending', 'published_at' => null]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($pending->title);
    }

    public function test_admin_subscription_plans_page_renders(): void
    {
        $admin = $this->admin();
        $plan = $this->plan('admin-view-plan', 'job_seeker');

        $this->actingAs($admin)
            ->get(route('admin.subscription-plans.index'))
            ->assertOk()
            ->assertSee($plan->name);
    }

    public function test_billing_page_renders_plans_and_payments(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('free-jobseeker-'.$seeker->id, 'job_seeker');

        $this->actingAs($seeker)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee($plan->name);
    }

    public function test_paid_plan_is_created_pending_until_payment(): void
    {
        $seeker = $this->jobSeeker();
        $plan = $this->plan('paid-jobseeker-'.$seeker->id, 'job_seeker', false);

        $this->actingAs($seeker)
            ->post(route('subscriptions.activate', $plan))
            ->assertRedirect(route('subscriptions.plans'));

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $seeker->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
        ]);
    }

    public function test_settings_page_shows_phone_and_account_information(): void
    {
        $seeker = $this->jobSeeker(['phone' => '08012345678']);

        $this->actingAs($seeker)
            ->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('08012345678')
            ->assertSee('Account');
    }
}
