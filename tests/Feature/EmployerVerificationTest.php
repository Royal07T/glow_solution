<?php

namespace Tests\Feature;

use App\Models\EmployerVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class EmployerVerificationTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_employer_can_submit_a_cac_certificate_for_review(): void
    {
        Storage::fake('private');

        $employer = $this->employer(false);

        $this->actingAs($employer)
            ->post(route('employer.verification.store'), [
                'cac_certificate' => UploadedFile::fake()->create('cac-certificate.pdf', 200, 'application/pdf'),
                'notes' => 'Registered in 2024.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $verification = EmployerVerification::firstOrFail();

        $this->assertSame('cac_certificate', $verification->document_type);
        $this->assertSame('pending', $verification->status);
        $this->assertSame($employer->employerProfile->id, $verification->employer_profile_id);
        $this->assertSame('Registered in 2024.', $verification->notes);
        $this->assertSame('submitted', $employer->employerProfile->fresh()->verification_status);

        Storage::disk('private')->assertExists($verification->document_path);
        $this->assertStringStartsWith('verification-documents/', $verification->document_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'employer.verification.submitted',
            'auditable_id' => $verification->id,
        ]);
    }

    public function test_cac_certificate_is_required(): void
    {
        $employer = $this->employer(false);

        $this->actingAs($employer)
            ->post(route('employer.verification.store'), [])
            ->assertSessionHasErrors('cac_certificate');

        $this->assertDatabaseCount('employer_verifications', 0);
        $this->assertSame('pending', $employer->employerProfile->fresh()->verification_status);
    }

    public function test_cac_certificate_must_be_an_allowed_document_type(): void
    {
        $employer = $this->employer(false);

        $this->actingAs($employer)
            ->post(route('employer.verification.store'), [
                'cac_certificate' => UploadedFile::fake()->create('certificate.exe', 100, 'application/octet-stream'),
            ])
            ->assertSessionHasErrors('cac_certificate');

        $this->assertDatabaseCount('employer_verifications', 0);
    }

    public function test_employer_cannot_submit_a_duplicate_pending_verification(): void
    {
        Storage::fake('private');

        $employer = $this->employer(false);

        EmployerVerification::create([
            'employer_profile_id' => $employer->employerProfile->id,
            'document_type' => 'cac_certificate',
            'document_path' => 'verification-documents/existing.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($employer)
            ->post(route('employer.verification.store'), [
                'cac_certificate' => UploadedFile::fake()->create('cac-certificate.pdf', 200, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('employer_verifications', 1);
    }

    public function test_rejected_employer_can_resubmit_a_cac_certificate(): void
    {
        Storage::fake('private');

        $employer = $this->employer(false);
        $employer->employerProfile->update(['verification_status' => 'rejected']);

        EmployerVerification::create([
            'employer_profile_id' => $employer->employerProfile->id,
            'document_type' => 'cac_certificate',
            'document_path' => 'verification-documents/rejected.pdf',
            'status' => 'rejected',
            'admin_notes' => 'The certificate was blurry.',
        ]);

        $this->actingAs($employer)
            ->post(route('employer.verification.store'), [
                'cac_certificate' => UploadedFile::fake()->create('cac-certificate.pdf', 200, 'application/pdf'),
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('employer_verifications', 2);
        $this->assertSame('submitted', $employer->employerProfile->fresh()->verification_status);
        $this->assertSame(1, EmployerVerification::where('status', 'pending')->count());
    }

    public function test_verified_employer_cannot_submit_another_verification(): void
    {
        Storage::fake('private');

        $employer = $this->employer(true);

        $this->actingAs($employer)
            ->post(route('employer.verification.store'), [
                'cac_certificate' => UploadedFile::fake()->create('cac-certificate.pdf', 200, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('employer_verifications', 0);
    }

    public function test_admin_can_download_the_uploaded_cac_certificate(): void
    {
        Storage::fake('private');

        $employer = $this->employer(false);
        $admin = $this->admin();

        Storage::disk('private')->put('verification-documents/cac.pdf', 'certificate');

        $verification = EmployerVerification::create([
            'employer_profile_id' => $employer->employerProfile->id,
            'document_type' => 'cac_certificate',
            'document_path' => 'verification-documents/cac.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.verifications.document', $verification))
            ->assertOk()
            ->assertDownload('verification-'.$verification->id.'.pdf');
    }
}
