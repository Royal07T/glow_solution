<?php

namespace Tests\Feature\Portal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_admin_can_create_another_admin_account(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), [
                'first_name' => 'New',
                'last_name' => 'Administrator',
                'email' => 'new.admin@example.com',
                'phone' => '+234 801 234 5678',
                'password' => 'Password!2345',
                'password_confirmation' => 'Password!2345',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $admin = User::where('email', 'new.admin@example.com')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertSame('New Administrator', $admin->name);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('Password!2345', $admin->password));
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.created',
            'auditable_type' => $admin->getMorphClass(),
            'auditable_id' => $admin->id,
        ]);
    }

    public function test_admin_can_view_the_create_admin_form(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Create administrator');
    }

    public function test_non_admin_cannot_create_admin_accounts(): void
    {
        $employer = $this->employer();

        $this->actingAs($employer)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($employer)
            ->post(route('admin.users.store'), [
                'first_name' => 'New',
                'last_name' => 'Administrator',
                'email' => 'new.admin@example.com',
                'password' => 'Password!2345',
                'password_confirmation' => 'Password!2345',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'new.admin@example.com']);
    }

    public function test_admin_creation_requires_a_unique_email_and_strong_password(): void
    {
        $existing = $this->admin();

        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), [
                'first_name' => 'New',
                'last_name' => 'Administrator',
                'email' => $existing->email,
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertSame(2, User::where('role', 'admin')->count());
    }
}
