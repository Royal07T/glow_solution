<?php

namespace Tests\Feature\Portal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class SidebarTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_job_seeker_dashboard_shows_job_seeker_links_only(): void
    {
        $response = $this->actingAs($this->jobSeeker())->get(route('jobseeker.dashboard'));

        $response->assertOk()
            ->assertSee(route('applications.index'), false)
            ->assertSee(route('saved-jobs.index'), false)
            ->assertSee(route('cvs.index'), false)
            ->assertSee(route('jobseeker.profile.edit'), false)
            ->assertSee(route('subscriptions.plans'), false)
            ->assertSee(route('settings.edit'), false)
            ->assertDontSee(route('employer.jobs.index'), false)
            ->assertDontSee(route('admin.audit-logs.index'), false);
    }

    public function test_employer_dashboard_shows_hiring_links_only(): void
    {
        $employer = $this->employerWithPlan();

        $response = $this->actingAs($employer)->get(route('employer.dashboard'));

        $response->assertOk()
            ->assertSee(route('employer.jobs.index'), false)
            ->assertSee(route('employer.jobs.create'), false)
            ->assertSee(route('employer.applications.index'), false)
            ->assertSee(route('employer.profile.edit'), false)
            ->assertSee(route('employer.verification'), false)
            ->assertSee(route('subscriptions.plans'), false)
            ->assertSee(route('settings.edit'), false)
            ->assertDontSee(route('applications.index'), false)
            ->assertDontSee(route('admin.jobs.index'), false);
    }

    public function test_admin_dashboard_shows_oversight_links_only(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.users.index'), false)
            ->assertSee(route('admin.jobs.index'), false)
            ->assertSee(route('admin.verifications.index'), false)
            ->assertSee(route('admin.audit-logs.index'), false)
            ->assertSee(route('settings.edit'), false)
            ->assertDontSee(route('employer.dashboard'), false)
            ->assertDontSee(route('jobseeker.dashboard'), false);
    }
}
