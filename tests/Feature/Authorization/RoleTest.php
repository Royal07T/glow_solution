<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_seeker_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'job_seeker', 'email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_employer_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'employer', 'email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_job_seeker_cannot_access_employer_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'job_seeker', 'email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/employer/dashboard');
        $response->assertStatus(403);
    }
}
