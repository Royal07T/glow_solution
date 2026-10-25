<?php

namespace Tests\Feature\Portal;

use App\Models\EmployerVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_job_seeker_portal_pages_render(): void
    {
        $seeker = $this->jobSeeker();
        $employer = $this->employer();
        $job = $this->job($employer);
        $openJob = $this->job($employer);
        $application = $this->application($seeker, $job);

        $this->actingAs($seeker);

        $this->get(route('jobseeker.dashboard'))->assertOk();
        $this->get(route('applications.index'))->assertOk();
        $this->get(route('applications.show', $application))->assertOk();
        $this->get(route('applications.create', $openJob))->assertOk();
        $this->get(route('saved-jobs.index'))->assertOk();
        $this->get(route('cvs.index'))->assertOk();
        $this->get(route('jobseeker.profile.edit'))->assertOk();
        $this->get(route('subscriptions.plans'))->assertOk();
        $this->get(route('settings.edit'))->assertOk();
    }

    public function test_employer_portal_pages_render(): void
    {
        $employer = $this->employerWithPlan();
        $seeker = $this->jobSeeker();
        $job = $this->job($employer);
        $application = $this->application($seeker, $job);

        $this->actingAs($employer);

        $this->get(route('employer.dashboard'))->assertOk();
        $this->get(route('employer.jobs.index'))->assertOk();
        $this->get(route('employer.jobs.create'))->assertOk();
        $this->get(route('employer.jobs.show', $job))->assertOk()->assertSee($seeker->name);
        $this->get(route('employer.jobs.edit', $job))->assertOk();
        $this->get(route('employer.applications.index'))->assertOk();
        $this->get(route('employer.applications.show', $application))->assertOk();
        $this->get(route('employer.profile.edit'))->assertOk();
        $this->get(route('employer.verification'))->assertOk();
        $this->get(route('subscriptions.plans'))->assertOk();
        $this->get(route('settings.edit'))->assertOk();
    }

    public function test_admin_portal_pages_render(): void
    {
        $admin = $this->admin();
        $employer = $this->employer();
        $job = $this->job($employer);

        $verification = EmployerVerification::create([
            'employer_profile_id' => $employer->employerProfile->id,
            'document_type' => 'business_registration',
            'document_path' => 'verification-documents/test.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.show', $employer))->assertOk();
        $this->get(route('admin.jobs.index'))->assertOk();
        $this->get(route('admin.jobs.show', $job))->assertOk();
        $this->get(route('admin.verifications.index'))->assertOk();
        $this->get(route('admin.verifications.show', $verification))->assertOk();
        $this->get(route('admin.audit-logs.index'))->assertOk();
        $this->get(route('settings.edit'))->assertOk();
    }
}
