<?php

namespace Tests\Feature\Security;

use App\Models\AuditLog;
use App\Models\EmployerVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class AuditAndUploadTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_cv_upload_persists_the_private_file_and_metadata(): void
    {
        Storage::fake('private');

        $seeker = $this->jobSeeker();

        $this->actingAs($seeker)
            ->post(route('cvs.store'), [
                'name' => 'My latest CV',
                'cv' => UploadedFile::fake()->create('resume.pdf', 120, 'application/pdf'),
                'is_primary' => '1',
            ])
            ->assertRedirect(route('cvs.index'));

        $this->assertDatabaseHas('cvs', [
            'user_id' => $seeker->id,
            'name' => 'My latest CV',
            'file_name' => 'resume.pdf',
            'is_primary' => true,
        ]);

        $cv = $seeker->cvs()->firstOrFail();
        Storage::disk('private')->assertExists($cv->file_path);
        $this->assertStringStartsWith('cvs/', $cv->file_path);
    }

    public function test_admin_job_approval_is_audited(): void
    {
        $admin = $this->admin();
        $job = $this->job($this->employer(), ['status' => 'pending', 'published_at' => null]);

        $this->actingAs($admin)
            ->post(route('admin.jobs.approve', $job), ['admin_notes' => 'Looks good'])
            ->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'job.approved',
            'user_id' => $admin->id,
            'auditable_id' => $job->id,
        ]);

        $this->assertSame('approved', $job->fresh()->status);
    }

    public function test_admin_cannot_suspend_another_admin_and_suspension_is_audited(): void
    {
        $admin = $this->admin();
        $otherAdmin = $this->admin();
        $seeker = $this->jobSeeker();

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $otherAdmin), ['suspension_reason' => 'Nope'])
            ->assertSessionHas('error');

        $this->assertFalse($otherAdmin->fresh()->is_suspended);

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $seeker), ['suspension_reason' => 'Spam applications'])
            ->assertSessionHas('success');

        $this->assertTrue($seeker->fresh()->is_suspended);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.suspended',
            'auditable_id' => $seeker->id,
        ]);
    }

    public function test_employer_verification_approval_marks_profile_verified(): void
    {
        $employer = $this->employer(false);
        $admin = $this->admin();

        $verification = EmployerVerification::create([
            'employer_profile_id' => $employer->employerProfile->id,
            'document_type' => 'business_registration',
            'document_path' => 'verification-documents/test.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.verifications.approve', $verification))
            ->assertSessionHas('success');

        $this->assertSame('approved', $verification->fresh()->status);
        $this->assertSame('approved', $employer->employerProfile->fresh()->verification_status);
        $this->assertSame(1, AuditLog::where('action', 'employer.verification.approved')->count());
    }
}
