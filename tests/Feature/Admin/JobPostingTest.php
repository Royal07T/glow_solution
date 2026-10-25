<?php

namespace Tests\Feature\Admin;

use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_admin_can_open_the_post_job_form(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.jobs.create'))
            ->assertOk()
            ->assertSee('Publish job')
            ->assertSee('Post a job');
    }

    public function test_admin_can_publish_a_job_as_the_agency(): void
    {
        $admin = $this->admin();
        $category = JobCategory::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.jobs.store'), $this->jobPayload($category))
            ->assertRedirect();

        $job = Job::where('title', 'Agency House Manager')->firstOrFail();

        $this->assertSame('approved', $job->status);
        $this->assertNotNull($job->published_at);
        $this->assertSame('Ikeja', $job->city);
        $this->assertSame('Lagos State', $job->state);
        $this->assertSame('Glow Recruitment Agency', $job->employerProfile->company_name);
        $this->assertSame($admin->id, $job->employerProfile->user_id);

        $this->assertDatabaseHas('employer_profiles', [
            'user_id' => $admin->id,
            'company_name' => 'Glow Recruitment Agency',
            'verification_status' => 'approved',
        ]);

        $this->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Agency House Manager')
            ->assertSee('Glow Recruitment Agency');
    }

    public function test_admin_can_edit_a_published_job_without_unpublishing_it(): void
    {
        $admin = $this->admin();
        $category = JobCategory::factory()->create(['is_active' => true]);
        $job = $this->job($this->employer(), ['job_category_id' => $category->id]);

        $publishedAt = $job->published_at;

        $this->actingAs($admin)
            ->put(route('admin.jobs.update', $job), $this->jobPayload($category, [
                'title' => 'Updated House Manager',
            ]))
            ->assertRedirect(route('admin.jobs.show', $job));

        $job->refresh();

        $this->assertSame('Updated House Manager', $job->title);
        $this->assertSame('approved', $job->status);
        $this->assertTrue($job->published_at->equalTo($publishedAt));
    }

    public function test_non_admin_users_cannot_create_admin_jobs(): void
    {
        $employer = $this->employerWithPlan();

        $this->actingAs($employer)
            ->get(route('admin.jobs.create'))
            ->assertForbidden();

        $this->actingAs($employer)
            ->post(route('admin.jobs.store'), [
                'title' => 'Unauthorised listing',
                'type' => 'professional',
                'employment_type' => 'full_time',
                'description' => 'Should not be created.',
                'location' => 'Lagos',
            ])
            ->assertForbidden();
    }

    private function jobPayload(JobCategory $category, array $overrides = []): array
    {
        return array_merge([
            'title' => 'Agency House Manager',
            'job_category_id' => $category->id,
            'type' => 'domestic',
            'employment_type' => 'live_in',
            'description' => 'Manage a private residence on behalf of the agency.',
            'responsibilities' => 'Coordinate household staff.',
            'qualifications' => 'Previous household management experience.',
            'benefits' => 'Accommodation provided.',
            'location' => 'Ikeja GRA',
            'city' => 'Ikeja',
            'state' => 'Lagos State',
            'salary_min' => 250000,
            'salary_max' => 350000,
            'salary_currency' => 'NGN',
            'salary_negotiable' => '1',
            'positions_available' => 1,
        ], $overrides);
    }
}
