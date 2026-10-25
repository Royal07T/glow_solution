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

    public function test_password_fields_have_visibility_toggles(): void
    {
        $seeker = $this->jobSeeker();

        $this->actingAs($seeker)
            ->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('data-password-toggle="current_password"', false)
            ->assertSee('data-password-toggle="password"', false)
            ->assertSee('data-password-toggle="password_confirmation"', false);
    }

    public function test_profile_information_updates_first_and_last_name(): void
    {
        $seeker = $this->jobSeeker();

        $this->actingAs($seeker)
            ->put(route('user-profile-information.update'), [
                'first_name' => 'Ada',
                'last_name' => 'Okafor',
                'email' => $seeker->email,
            ])
            ->assertSessionHasNoErrors();

        $seeker->refresh();

        $this->assertSame('Ada', $seeker->first_name);
        $this->assertSame('Okafor', $seeker->last_name);
        $this->assertSame('Ada Okafor', $seeker->name);
    }

    public function test_admin_user_detail_renders_for_job_seeker_with_applications(): void
    {
        $admin = $this->admin();
        $seeker = $this->jobSeeker();
        $job = $this->job($this->employer());
        $this->application($seeker, $job);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $seeker))
            ->assertOk()
            ->assertSee($seeker->email);
    }

    public function test_job_seeker_profile_displays_and_updates_phone_number(): void
    {
        $seeker = $this->jobSeeker(['phone' => '08031234567']);

        $this->actingAs($seeker)
            ->get(route('jobseeker.profile.edit'))
            ->assertOk()
            ->assertSee('Phone number')
            ->assertSee('08031234567');

        $this->actingAs($seeker)
            ->put(route('jobseeker.profile.update'), [
                'first_name' => 'Test',
                'last_name' => 'Seeker',
                'phone' => '0812 345 6789',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('0812 345 6789', $seeker->fresh()->phone);
    }

    public function test_job_seeker_profile_rejects_invalid_phone_number(): void
    {
        $seeker = $this->jobSeeker();

        $this->actingAs($seeker)
            ->put(route('jobseeker.profile.update'), ['phone' => 'not-a-phone'])
            ->assertSessionHasErrors('phone');
    }
}
