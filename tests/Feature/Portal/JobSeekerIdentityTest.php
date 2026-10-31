<?php

namespace Tests\Feature\Portal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\PortalFixtures;
use Tests\TestCase;

class JobSeekerIdentityTest extends TestCase
{
    use PortalFixtures;
    use RefreshDatabase;

    public function test_identity_submission_stores_private_files_and_sets_pending_review(): void
    {
        Storage::fake('private');
        $this->freezeSecond();

        $seeker = $this->jobSeeker();
        $photo = UploadedFile::fake()
            ->createWithContent('avatar.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='))
            ->mimeType('image/png');
        $document = UploadedFile::fake()->create('nin.pdf', 120, 'application/pdf');

        $this->actingAs($seeker)
            ->put(route('jobseeker.profile.update'), [
                'first_name' => 'Test',
                'last_name' => 'Seeker',
                'phone' => '08031234567',
                'available_for_work' => '1',
                'identification_type' => 'nin',
                'identification_number' => '12345678901',
                'identification_file' => $document,
                'guarantor_name' => 'Grace Adeyemi',
                'guarantor_phone' => '08035551234',
                'guarantor_address' => '12 Allen Avenue, Ikeja, Lagos',
                'photo' => $photo,
            ])
            ->assertRedirect(route('jobseeker.profile.edit'))
            ->assertSessionHasNoErrors();

        $profile = $seeker->fresh()->jobSeekerProfile;

        $this->assertSame('pending', $profile->identity_verification_status);
        $this->assertSame('nin', $profile->identification_type);
        $this->assertSame('12345678901', $profile->identification_number);
        $this->assertSame('Grace Adeyemi', $profile->guarantor_name);
        $this->assertSame('08035551234', $profile->guarantor_phone);
        $this->assertSame('12 Allen Avenue, Ikeja, Lagos', $profile->guarantor_address);
        $this->assertTrue($profile->identity_submitted_at->equalTo(now()));
        $this->assertNull($profile->identity_reviewed_at);
        $this->assertDatabaseMissing('job_seeker_profiles', [
            'identification_number' => '12345678901',
        ]);
        Storage::disk('private')->assertExists($profile->photo_path);
        Storage::disk('private')->assertExists($profile->identification_document_path);
    }

    public function test_identity_submission_rejects_incomplete_identification_details(): void
    {
        Storage::fake('private');

        $seeker = $this->jobSeeker();

        $this->actingAs($seeker)
            ->put(route('jobseeker.profile.update'), [
                'identification_type' => 'nin',
                'identification_number' => '12345678901',
                'guarantor_name' => 'Grace Adeyemi',
                'guarantor_phone' => '08035551234',
            ])
            ->assertSessionHasErrors([
                'identification_file' => 'Upload a clear copy of your identification document.',
                'guarantor_address' => 'Enter your guarantor\'s address.',
            ]);

        $profile = $seeker->fresh()->jobSeekerProfile;

        $this->assertSame('unsubmitted', $profile->identity_verification_status);
        $this->assertNull($profile->identification_document_path);
        $this->assertEmpty(Storage::disk('private')->allFiles());
    }

    public function test_rejected_identity_resubmission_returns_to_pending_review(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('job-seeker/identification/old.pdf', 'old document');

        $seeker = $this->jobSeeker();
        $reviewer = $this->admin();

        $seeker->jobSeekerProfile->forceFill([
            'identification_type' => 'nin',
            'identification_number' => '12345678901',
            'identification_document_path' => 'job-seeker/identification/old.pdf',
            'identity_verification_status' => 'rejected',
            'identity_submitted_at' => now()->subDay(),
            'identity_reviewed_by' => $reviewer->id,
            'identity_reviewed_at' => now()->subHour(),
            'identity_verification_notes' => 'The document was blurry.',
            'guarantor_name' => 'Grace Adeyemi',
            'guarantor_phone' => '08035551234',
            'guarantor_address' => '12 Allen Avenue, Ikeja, Lagos',
        ])->save();

        $this->actingAs($seeker)
            ->put(route('jobseeker.profile.update'), [
                'first_name' => 'Test',
                'last_name' => 'Seeker',
                'identification_type' => 'nin',
                'identification_number' => '98765432109',
                'guarantor_name' => 'Grace Adeyemi',
                'guarantor_phone' => '08035551235',
                'guarantor_address' => '12 Allen Avenue, Ikeja, Lagos',
            ])
            ->assertSessionHasNoErrors();

        $profile = $seeker->fresh()->jobSeekerProfile;

        $this->assertSame('pending', $profile->identity_verification_status);
        $this->assertSame('98765432109', $profile->identification_number);
        $this->assertNull($profile->identity_reviewed_by);
        $this->assertNull($profile->identity_reviewed_at);
        $this->assertNull($profile->identity_verification_notes);
        $this->assertSame('08035551235', $profile->guarantor_phone);
        $this->assertNotNull($profile->identity_submitted_at);
    }

    public function test_owner_and_admin_can_view_private_identity_files(): void
    {
        $seeker = $this->jobSeekerWithIdentityFiles();

        $this->actingAs($seeker)
            ->get(route('jobseekers.photo', $seeker))
            ->assertOk();

        $this->actingAs($seeker)
            ->get(route('jobseekers.identification', $seeker))
            ->assertOk();

        $this->actingAs($this->admin())
            ->get(route('jobseekers.photo', $seeker))
            ->assertOk();

        $this->actingAs($this->admin())
            ->get(route('jobseekers.identification', $seeker))
            ->assertOk();
    }

    public static function nonOwnerRoles(): array
    {
        return [
            'job seeker' => ['job_seeker'],
            'employer' => ['employer'],
        ];
    }

    #[DataProvider('nonOwnerRoles')]
    public function test_non_owner_cannot_view_private_identity_files(string $role): void
    {
        $seeker = $this->jobSeekerWithIdentityFiles();
        $viewer = $role === 'employer' ? $this->employer() : $this->jobSeeker();

        $this->actingAs($viewer)
            ->get(route('jobseekers.photo', $seeker))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->get(route('jobseekers.identification', $seeker))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_private_identity_files(): void
    {
        $seeker = $this->jobSeekerWithIdentityFiles();

        $this->get(route('jobseekers.identification', $seeker))
            ->assertRedirect(route('login'));
    }

    private function jobSeekerWithIdentityFiles(): User
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
