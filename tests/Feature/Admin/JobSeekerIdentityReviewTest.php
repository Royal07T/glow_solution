<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class JobSeekerIdentityReviewTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_admin_can_see_job_seeker_identity_details_and_document(): void
    {
        $seeker = $this->jobSeekerWithPendingIdentity();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.users.show', $seeker))
            ->assertOk()
            ->assertSee('Identity submission')
            ->assertSee('National Identification Number (NIN)')
            ->assertSee('12345678901')
            ->assertSee('Grace Adeyemi')
            ->assertSee('08035551234')
            ->assertSee('12 Allen Avenue, Ikeja, Lagos')
            ->assertSee('********')
            ->assertSee('data-sensitive-toggle', false)
            ->assertSee('aria-label="Show email address"', false)
            ->assertSee('aria-label="Show national ID number"', false)
            ->assertSee('aria-label="Show guarantor phone number"', false)
            ->assertSee('View ID document')
            ->assertSee('View full photo')
            ->assertSee('Open full-size photo')
            ->assertSee(route('jobseekers.photo', $seeker), false)
            ->assertSee('Approve identity');
    }

    public function test_admin_approval_marks_identity_verified_and_notifies_job_seeker(): void
    {
        Notification::fake();

        $seeker = $this->jobSeekerWithPendingIdentity();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.users.identity.approve', $seeker))
            ->assertRedirect()
            ->assertSessionHas('success');

        $profile = $seeker->fresh()->jobSeekerProfile;

        $this->assertSame('approved', $profile->identity_verification_status);
        $this->assertSame($admin->id, $profile->identity_reviewed_by);
        $this->assertNotNull($profile->identity_reviewed_at);
        $this->assertNull($profile->identity_verification_notes);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'job_seeker.identity_approved',
            'user_id' => $admin->id,
            'auditable_id' => $profile->id,
        ]);
        Notification::assertSentTo(
            $seeker,
            SystemNotification::class,
            fn (SystemNotification $notification) => $notification->title === 'Identity verified'
                && $notification->level === 'success',
        );
    }

    public function test_admin_identity_rejection_requires_a_reason(): void
    {
        $seeker = $this->jobSeekerWithPendingIdentity();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.users.identity.reject', $seeker))
            ->assertSessionHasErrors([
                'identity_verification_notes' => 'The identity verification notes field is required.',
            ]);

        $this->assertSame('pending', $seeker->fresh()->jobSeekerProfile->identity_verification_status);
    }

    public function test_admin_can_reject_identity_with_reason_and_notifies_job_seeker(): void
    {
        Notification::fake();

        $seeker = $this->jobSeekerWithPendingIdentity();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.users.identity.reject', $seeker), [
                'identity_verification_notes' => 'The uploaded document is unreadable.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $profile = $seeker->fresh()->jobSeekerProfile;

        $this->assertSame('rejected', $profile->identity_verification_status);
        $this->assertSame('The uploaded document is unreadable.', $profile->identity_verification_notes);
        $this->assertSame($admin->id, $profile->identity_reviewed_by);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'job_seeker.identity_rejected',
            'user_id' => $admin->id,
            'auditable_id' => $profile->id,
        ]);
        Notification::assertSentTo(
            $seeker,
            SystemNotification::class,
            fn (SystemNotification $notification) => $notification->title === 'Identity verification rejected'
                && str_contains($notification->message, 'The uploaded document is unreadable.')
                && $notification->level === 'warning',
        );
    }

    public function test_non_admin_cannot_review_job_seeker_identity(): void
    {
        $seeker = $this->jobSeekerWithPendingIdentity();

        $this->actingAs($this->employer())
            ->post(route('admin.users.identity.approve', $seeker))
            ->assertForbidden();

        $this->assertSame('pending', $seeker->fresh()->jobSeekerProfile->identity_verification_status);
    }

    private function jobSeekerWithPendingIdentity(): User
    {
        Storage::fake('private');
        Storage::disk('private')->put('job-seeker/photos/avatar.png', 'photo');
        Storage::disk('private')->put('job-seeker/identification/id.pdf', 'document');

        $seeker = $this->jobSeeker();

        $seeker->jobSeekerProfile->forceFill([
            'photo_path' => 'job-seeker/photos/avatar.png',
            'identification_type' => 'nin',
            'identification_number' => '12345678901',
            'identification_document_path' => 'job-seeker/identification/id.pdf',
            'identity_verification_status' => 'pending',
            'identity_submitted_at' => now(),
            'guarantor_name' => 'Grace Adeyemi',
            'guarantor_phone' => '08035551234',
            'guarantor_address' => '12 Allen Avenue, Ikeja, Lagos',
        ])->save();

        return $seeker->fresh();
    }
}
