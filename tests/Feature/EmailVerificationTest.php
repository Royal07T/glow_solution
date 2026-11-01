<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_unverified_users_can_view_the_email_verification_notice(): void
    {
        $user = $this->jobSeeker(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Verify your email address')
            ->assertSee($user->email)
            ->assertSee('Resend verification email');
    }

    public function test_signed_verification_link_verifies_email_and_shows_confirmation_page(): void
    {
        $user = $this->jobSeeker(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('verification.verified').'?verified=1');

        $this->assertNotNull($user->fresh()->email_verified_at);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertRedirect(route('verification.verified'));

        $this->actingAs($user)
            ->get(route('verification.verified'))
            ->assertOk()
            ->assertSee('Email verified')
            ->assertSee($user->email)
            ->assertSee(route('jobseeker.dashboard'), false);
    }

    public function test_fortify_home_path_redirects_users_to_their_dashboard(): void
    {
        $user = $this->jobSeeker();

        $this->actingAs($user)
            ->get(route('home.redirect'))
            ->assertRedirect(route('jobseeker.dashboard'));
    }
}
