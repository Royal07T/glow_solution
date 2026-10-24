<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class AuthHardeningTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_login_is_throttled_after_repeated_failures(): void
    {
        $user = $this->jobSeeker();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_suspended_users_cannot_sign_in(): void
    {
        $user = $this->jobSeeker(['is_suspended' => true]);
        $user->forceFill(['password' => Hash::make('Password12345')])->save();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password12345',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_requires_a_strong_password(): void
    {
        $this->post(route('register'), [
            'name' => 'New Seeker',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'job_seeker',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
    }

    public function test_new_accounts_are_unverified_and_redirected_to_the_notice(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New Seeker',
            'email' => 'new@example.com',
            'password' => 'Password12345',
            'password_confirmation' => 'Password12345',
            'role' => 'job_seeker',
        ]);

        $response->assertRedirect(route('jobseeker.dashboard'));
        $this->assertAuthenticated();

        $this->assertNull(User::where('email', 'new@example.com')->value('email_verified_at'));

        $this->get(route('jobseeker.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_registration_cannot_create_an_admin_account(): void
    {
        $this->post(route('register'), [
            'name' => 'Sneaky Admin',
            'email' => 'sneaky@example.com',
            'password' => 'Password12345',
            'password_confirmation' => 'Password12345',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.com']);
    }
}
