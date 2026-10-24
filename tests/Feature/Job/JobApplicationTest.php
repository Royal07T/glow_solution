<?php

namespace Tests\Feature\Job;

use App\Models\User;
use App\Models\Job;
use App\Models\EmployerProfile;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_seeker_can_view_job_details(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $profile = EmployerProfile::factory()->create(['user_id' => $employer->id, 'verification_status' => 'approved']);
        $category = JobCategory::factory()->create();
        $job = Job::factory()->create([
            'employer_profile_id' => $profile->id,
            'job_category_id' => $category->id,
            'status' => 'approved',
            'published_at' => now(),
        ]);

        $response = $this->get(route('jobs.show', $job));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_apply_to_job(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $profile = EmployerProfile::factory()->create(['user_id' => $employer->id]);
        $job = Job::factory()->create(['employer_profile_id' => $profile->id, 'status' => 'approved']);

        $response = $this->get(route('applications.create', $job));
        $response->assertRedirect(route('login'));
    }
}
