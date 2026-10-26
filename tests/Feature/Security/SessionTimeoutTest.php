<?php

namespace Tests\Feature\Security;

use App\Http\Middleware\EnforceSessionTimeout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_expired_session_redirects_to_login_and_logs_out(): void
    {
        $user = $this->jobSeeker();

        $response = $this->actingAs($user)
            ->withSession([
                EnforceSessionTimeout::STARTED_AT_KEY => now()->subMinutes(61)->timestamp,
            ])
            ->get(route('jobseeker.dashboard'));

        $response->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Your session expired for security reasons. Please sign in again.');

        $this->assertGuest();
    }

    public function test_expired_session_returns_401_for_json_requests(): void
    {
        $response = $this->actingAs($this->jobSeeker())
            ->withSession([
                EnforceSessionTimeout::STARTED_AT_KEY => now()->subMinutes(61)->timestamp,
            ])
            ->getJson(route('jobseeker.dashboard'));

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Your session has expired. Please sign in again.');

        $this->assertGuest();
    }

    public function test_session_within_absolute_lifetime_remains_authenticated(): void
    {
        $user = $this->jobSeeker();

        $response = $this->actingAs($user)
            ->withSession([
                EnforceSessionTimeout::STARTED_AT_KEY => now()->subMinutes(59)->timestamp,
            ])
            ->get(route('jobseeker.dashboard'));

        $response->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_missing_start_time_is_initialized_for_existing_sessions(): void
    {
        $this->travelTo('2026-01-01 12:00:00');

        $this->actingAs($this->jobSeeker())
            ->get(route('jobseeker.dashboard'))
            ->assertOk()
            ->assertSessionHas(EnforceSessionTimeout::STARTED_AT_KEY, now()->timestamp);
    }

    public function test_authenticated_pages_include_the_session_timeout_countdown(): void
    {
        $this->travelTo('2026-01-01 12:00:00');

        $this->actingAs($this->jobSeeker())
            ->withSession([
                EnforceSessionTimeout::STARTED_AT_KEY => now()->subMinutes(58)->timestamp,
            ])
            ->get(route('jobseeker.dashboard'))
            ->assertOk()
            ->assertSee('data-session-timeout', false)
            ->assertSee('data-remaining="120"', false)
            ->assertSee('data-warning="300"', false);
    }

    public function test_login_records_the_session_start_time(): void
    {
        $this->travelTo('2026-01-01 12:00:00');
        $user = $this->jobSeeker();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('jobseeker.dashboard'))
            ->assertSessionHas(EnforceSessionTimeout::STARTED_AT_KEY, now()->timestamp);

        $this->assertAuthenticatedAs($user);
    }

    public function test_timeout_logout_redirects_to_login_with_an_error(): void
    {
        $this->actingAs($this->jobSeeker())
            ->post(route('logout'), ['reason' => 'timeout'])
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'You have been signed out for security reasons.');

        $this->assertGuest();
    }

    public function test_regular_logout_redirects_to_the_home_page(): void
    {
        $this->actingAs($this->jobSeeker())
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
