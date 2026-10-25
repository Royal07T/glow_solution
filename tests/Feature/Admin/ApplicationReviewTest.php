<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Models\Cv;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class ApplicationReviewTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_admin_posted_job_lists_its_applicants_for_review(): void
    {
        $admin = $this->admin();
        $job = $this->publishAgencyJob($admin);
        $seeker = $this->jobSeeker(['name' => 'Amina Bello']);
        $application = $this->application($seeker, $job, [
            'cover_letter' => 'I would like to be considered for this role.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.jobs.show', $job))
            ->assertOk()
            ->assertSee('Applications')
            ->assertSee('Amina Bello')
            ->assertSee($seeker->email)
            ->assertSee(route('admin.applications.show', $application), false);
    }

    public function test_admin_can_review_an_application_and_update_its_status(): void
    {
        $admin = $this->admin();
        $job = $this->publishAgencyJob($admin);
        $seeker = $this->jobSeeker(['name' => 'Chidi Okafor']);
        $application = $this->application($seeker, $job, [
            'cover_letter' => 'Five years of private household experience.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.applications.show', $application))
            ->assertOk()
            ->assertSee('Chidi Okafor')
            ->assertSee('Five years of private household experience.')
            ->assertSee('Update status');

        $this->actingAs($admin)
            ->patch(route('admin.applications.update-status', $application), [
                'status' => 'shortlisted',
                'employer_notes' => 'Strong candidate. Invite for interview.',
            ])
            ->assertRedirect();

        $application->refresh();

        $this->assertSame(ApplicationStatus::SHORTLISTED, $application->status);
        $this->assertSame('Strong candidate. Invite for interview.', $application->employer_notes);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_admin_applications_can_be_filtered_by_job_and_status(): void
    {
        $admin = $this->admin();
        $job = $this->publishAgencyJob($admin);
        $otherJob = $this->job($this->employer(), ['title' => 'Other listing']);
        $shortlisted = $this->jobSeeker(['name' => 'Shortlisted Candidate']);
        $submitted = $this->jobSeeker(['name' => 'Submitted Candidate']);

        $this->application($shortlisted, $job, ['status' => ApplicationStatus::SHORTLISTED]);
        $this->application($submitted, $otherJob);

        $this->actingAs($admin)
            ->get(route('admin.applications.index', ['job' => $job->id, 'status' => 'shortlisted']))
            ->assertOk()
            ->assertSee('Shortlisted Candidate')
            ->assertDontSee('Submitted Candidate')
            ->assertSee('Other listing', false);
    }

    public function test_admin_can_download_a_candidates_cv(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('cvs/resume.pdf', 'resume contents');

        $admin = $this->admin();
        $job = $this->publishAgencyJob($admin);
        $seeker = $this->jobSeeker();
        $cv = Cv::create([
            'user_id' => $seeker->id,
            'name' => 'Primary resume',
            'file_name' => 'resume.pdf',
            'file_path' => 'cvs/resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 15,
            'is_primary' => true,
        ]);
        $application = $this->application($seeker, $job, ['cv_id' => $cv->id]);

        $this->actingAs($admin)
            ->get(route('admin.applications.show', $application))
            ->assertOk()
            ->assertSee('Download CV');

        $this->actingAs($admin)
            ->get(route('cvs.download', $cv))
            ->assertOk()
            ->assertDownload('resume.pdf');
    }

    public function test_employer_can_download_a_cv_attached_to_their_application(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('cvs/employer-copy.pdf', 'resume contents');

        $employer = $this->employer();
        $job = $this->job($employer);
        $seeker = $this->jobSeeker();
        $cv = Cv::create([
            'user_id' => $seeker->id,
            'name' => 'Primary resume',
            'file_name' => 'employer-copy.pdf',
            'file_path' => 'cvs/employer-copy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 15,
            'is_primary' => true,
        ]);
        $this->application($seeker, $job, ['cv_id' => $cv->id]);

        $this->actingAs($employer)
            ->get(route('cvs.download', $cv))
            ->assertOk()
            ->assertDownload('employer-copy.pdf');
    }

    public function test_non_admin_users_cannot_access_admin_application_review(): void
    {
        $this->actingAs($this->employerWithPlan())
            ->get(route('admin.applications.index'))
            ->assertForbidden();
    }

    private function publishAgencyJob(User $admin): Job
    {
        $this->actingAs($admin)->post(route('admin.jobs.store'), [
            'title' => 'Agency House Manager',
            'type' => 'domestic',
            'employment_type' => 'live_in',
            'description' => 'Manage a private residence on behalf of the agency.',
            'location' => 'Ikeja GRA',
            'city' => 'Ikeja',
            'state' => 'Lagos State',
            'salary_currency' => 'NGN',
        ])->assertRedirect();

        return Job::where('title', 'Agency House Manager')->latest('id')->firstOrFail();
    }
}
