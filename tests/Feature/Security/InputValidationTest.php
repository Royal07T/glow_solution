<?php

namespace Tests\Feature\Security;

use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class InputValidationTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_html_tags_and_surrounding_whitespace_are_removed_from_text_input(): void
    {
        $admin = $this->admin();
        $category = JobCategory::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.jobs.store'), [
                'title' => '  <script>alert(1)</script>Housekeeper  ',
                'job_category_id' => $category->id,
                'type' => 'domestic',
                'employment_type' => 'full_time',
                'description' => '  <b>Clean</b> rooms  ',
                'location' => 'Lagos',
            ])
            ->assertRedirect();

        $job = Job::firstOrFail();

        $this->assertSame('Housekeeper', $job->title);
        $this->assertSame('Clean rooms', $job->description);
    }

    public function test_invalid_job_filter_values_are_rejected(): void
    {
        $this->get(route('jobs.index', [
            'type' => 'not-a-type',
            'employment_type' => 'not-an-employment-type',
            'category' => 'not-an-id',
        ]))->assertSessionHasErrors(['type', 'employment_type', 'category']);
    }

    public function test_invalid_admin_filters_are_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.users.index', [
                'role' => 'superuser',
                'status' => 'deleted',
                'identity_status' => 'maybe',
            ]))
            ->assertSessionHasErrors(['role', 'status', 'identity_status']);
    }

    public function test_like_wildcards_in_search_terms_are_neutralised(): void
    {
        $this->job($this->employer(), [
            'title' => 'Alpha',
            'status' => 'approved',
            'published_at' => now()->subHour(),
        ]);

        $this->get(route('jobs.index', ['keyword' => 'A%a']))
            ->assertOk()
            ->assertDontSee('Alpha');
    }

    public function test_admin_job_approval_notes_are_length_validated(): void
    {
        $admin = $this->admin();
        $job = $this->job($this->employer(), ['status' => 'pending', 'published_at' => null]);

        $this->actingAs($admin)
            ->post(route('admin.jobs.approve', $job), ['admin_notes' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('admin_notes');

        $this->assertSame('pending', $job->fresh()->status);
    }

    public function test_application_status_filter_must_be_a_known_status(): void
    {
        $this->actingAs($this->jobSeeker())
            ->get(route('applications.index', ['status' => 'hacked']))
            ->assertSessionHasErrors('status');
    }
}
