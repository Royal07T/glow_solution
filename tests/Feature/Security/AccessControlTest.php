<?php

namespace Tests\Feature\Security;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_unverified_users_are_sent_to_the_verification_notice(): void
    {
        $user = User::factory()->create([
            'role' => 'job_seeker',
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('jobseeker.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_suspended_users_are_logged_out_on_their_next_request(): void
    {
        $user = $this->jobSeeker(['is_suspended' => true]);

        $this->actingAs($user)
            ->get(route('jobseeker.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_employer_cannot_edit_another_employers_job(): void
    {
        $owner = $this->employerWithPlan();
        $other = $this->employerWithPlan();
        $job = $this->job($owner);

        $this->actingAs($other)
            ->get(route('employer.jobs.edit', $job))
            ->assertForbidden();
    }

    public function test_employer_cannot_view_another_employers_application(): void
    {
        $owner = $this->employer();
        $other = $this->employer();
        $seeker = $this->jobSeeker();
        $application = $this->application($seeker, $this->job($owner));

        $this->actingAs($other)
            ->get(route('employer.applications.show', $application))
            ->assertForbidden();
    }

    public function test_job_seeker_cannot_view_another_job_seekers_application(): void
    {
        $owner = $this->employer();
        $applicant = $this->jobSeeker();
        $intruder = $this->jobSeeker();
        $application = $this->application($applicant, $this->job($owner));

        $this->actingAs($intruder)
            ->get(route('applications.show', $application))
            ->assertForbidden();
    }

    public function test_cv_download_is_restricted_to_the_owner(): void
    {
        Storage::fake('private');

        $seeker = $this->jobSeeker();
        $intruder = $this->jobSeeker();

        Storage::disk('private')->put('cvs/test.pdf', 'private cv');

        $cv = Cv::create([
            'user_id' => $seeker->id,
            'name' => 'My CV',
            'file_name' => 'cv.pdf',
            'file_path' => 'cvs/test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 100,
        ]);

        $this->actingAs($seeker)->get(route('cvs.download', $cv))->assertOk();
        $this->actingAs($intruder)->get(route('cvs.download', $cv))->assertForbidden();
    }

    public function test_non_admin_cannot_reach_admin_routes(): void
    {
        $this->actingAs($this->employer())
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($this->jobSeeker())
            ->get(route('admin.verifications.index'))
            ->assertForbidden();
    }

    public function test_pending_jobs_cannot_be_saved_by_job_seekers(): void
    {
        $job = $this->job($this->employer(), ['status' => 'pending', 'published_at' => null]);

        $this->actingAs($this->jobSeeker())
            ->post(route('jobs.save', $job))
            ->assertNotFound();
    }

    public function test_job_seeker_can_withdraw_an_application(): void
    {
        $seeker = $this->jobSeeker();
        $application = $this->application($seeker, $this->job($this->employer()));

        $this->actingAs($seeker)
            ->post(route('applications.withdraw', $application))
            ->assertRedirect(route('applications.index'));

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'withdrawn',
        ]);
    }
}
