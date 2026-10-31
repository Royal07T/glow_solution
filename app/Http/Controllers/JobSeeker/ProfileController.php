<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobSeekerProfile;
use App\Support\ProfileCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->jobSeekerProfile ?? new JobSeekerProfile;
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get();
        $completion = ProfileCompletion::for($user);

        return view('jobseeker.profile.edit', compact('user', 'profile', 'categories', 'completion'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]{7,20}$/',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:2000',
            'skills_text' => 'nullable|string|max:1000',
            'education_level' => 'nullable|string|max:100',
            'experience_level' => 'nullable|string|max:100',
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'integer|exists:job_categories,id',
            'preferred_locations_text' => 'nullable|string|max:500',
            'employment_preference' => 'nullable|in:full_time,part_time,contract,temporary,domestic,any',
            'available_for_work' => 'boolean',
            'identification_type' => ['nullable', 'string', Rule::in(array_keys(JobSeekerProfile::IDENTIFICATION_TYPES))],
            'identification_number' => ['nullable', 'string', 'max:50'],
            'identification_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'guarantor_name' => ['nullable', 'string', 'max:150'],
            'guarantor_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'guarantor_address' => ['nullable', 'string', 'max:500'],
        ]);

        $skills = collect(explode(',', (string) ($validated['skills_text'] ?? '')))
            ->map(fn ($skill) => trim($skill))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $locations = collect(explode(',', (string) ($validated['preferred_locations_text'] ?? '')))
            ->map(fn ($location) => trim($location))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $profile = $request->user()->jobSeekerProfile;

        if (! $profile) {
            $profile = new JobSeekerProfile(['user_id' => $request->user()->id]);
        }

        $oldPhotoPath = $profile->photo_path;
        $oldIdentificationType = $profile->identification_type;
        $oldIdentificationNumber = $profile->identification_number;
        $oldDocumentPath = $profile->identification_document_path;
        $oldGuarantorName = $profile->guarantor_name;
        $oldGuarantorPhone = $profile->guarantor_phone;
        $oldGuarantorAddress = $profile->guarantor_address;

        $identificationType = filled($validated['identification_type'] ?? null)
            ? $validated['identification_type']
            : null;
        $identificationNumber = filled($validated['identification_number'] ?? null)
            ? trim($validated['identification_number'])
            : null;
        $guarantorName = filled($validated['guarantor_name'] ?? null)
            ? trim($validated['guarantor_name'])
            : null;
        $guarantorPhone = filled($validated['guarantor_phone'] ?? null)
            ? trim($validated['guarantor_phone'])
            : null;
        $guarantorAddress = filled($validated['guarantor_address'] ?? null)
            ? trim($validated['guarantor_address'])
            : null;
        $newDocumentPath = $oldDocumentPath;

        if ($request->hasFile('identification_file')) {
            $newDocumentPath = $request->file('identification_file')->store('job-seeker/identification', 'private');
        }

        $isClearingIdentity = blank($identificationType)
            && blank($identificationNumber)
            && blank($guarantorName)
            && blank($guarantorPhone)
            && blank($guarantorAddress)
            && ! $request->hasFile('identification_file')
            && (filled($oldIdentificationType)
                || filled($oldIdentificationNumber)
                || filled($oldDocumentPath)
                || filled($oldGuarantorName)
                || filled($oldGuarantorPhone)
                || filled($oldGuarantorAddress));

        if ($isClearingIdentity) {
            $newDocumentPath = null;
        }

        $hasDocument = filled($newDocumentPath) && Storage::disk('private')->exists($newDocumentPath);
        $hasAnyIdentityInput = filled($identificationType)
            || filled($identificationNumber)
            || filled($guarantorName)
            || filled($guarantorPhone)
            || filled($guarantorAddress)
            || $request->hasFile('identification_file');
        $hasCompleteGuarantor = filled($guarantorName)
            && filled($guarantorPhone)
            && filled($guarantorAddress);
        $hasCompleteIdentitySubmission = filled($identificationType)
            && filled($identificationNumber)
            && $hasDocument
            && $hasCompleteGuarantor;

        if ($hasAnyIdentityInput && ! $hasCompleteIdentitySubmission) {
            if ($newDocumentPath && $newDocumentPath !== $oldDocumentPath) {
                Storage::disk('private')->delete($newDocumentPath);
            }

            $identityErrors = [];

            if (! filled($identificationType)) {
                $identityErrors['identification_type'] = 'Select the type of identification you are submitting.';
            }

            if (! filled($identificationNumber)) {
                $identityErrors['identification_number'] = 'Enter your identification number.';
            }

            if (! $hasDocument) {
                $identityErrors['identification_file'] = 'Upload a clear copy of your identification document.';
            }

            if (! filled($guarantorName)) {
                $identityErrors['guarantor_name'] = 'Enter your guarantor\'s full name.';
            }

            if (! filled($guarantorPhone)) {
                $identityErrors['guarantor_phone'] = 'Enter your guarantor\'s phone number.';
            }

            if (! filled($guarantorAddress)) {
                $identityErrors['guarantor_address'] = 'Enter your guarantor\'s address.';
            }

            throw ValidationException::withMessages($identityErrors);
        }

        if ($request->hasFile('photo')) {
            $profile->photo_path = $request->file('photo')->store('job-seeker/photos', 'private');
        }

        $profile->fill([
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'skills' => $skills,
            'education_level' => $validated['education_level'] ?? null,
            'experience_level' => $validated['experience_level'] ?? null,
            'preferred_categories' => $validated['preferred_categories'] ?? [],
            'preferred_locations' => $locations,
            'employment_preference' => $validated['employment_preference'] ?? null,
            'available_for_work' => $request->boolean('available_for_work'),
            'identification_type' => $identificationType,
            'identification_number' => $identificationNumber,
            'identification_document_path' => $newDocumentPath,
            'guarantor_name' => $guarantorName,
            'guarantor_phone' => $guarantorPhone,
            'guarantor_address' => $guarantorAddress,
        ]);

        $identityChanged = $oldIdentificationType !== $identificationType
            || $oldIdentificationNumber !== $identificationNumber
            || $oldDocumentPath !== $newDocumentPath
            || $oldGuarantorName !== $guarantorName
            || $oldGuarantorPhone !== $guarantorPhone
            || $oldGuarantorAddress !== $guarantorAddress;

        if ($identityChanged) {
            if ($hasCompleteIdentitySubmission) {
                $profile->forceFill([
                    'identity_verification_status' => 'pending',
                    'identity_submitted_at' => now(),
                    'identity_reviewed_by' => null,
                    'identity_reviewed_at' => null,
                    'identity_verification_notes' => null,
                ]);
            } else {
                $profile->forceFill([
                    'identity_verification_status' => 'unsubmitted',
                    'identity_submitted_at' => null,
                    'identity_reviewed_by' => null,
                    'identity_reviewed_at' => null,
                    'identity_verification_notes' => null,
                ]);
            }
        }

        $profile->save();

        if ($oldPhotoPath && $oldPhotoPath !== $profile->photo_path) {
            Storage::disk('private')->delete($oldPhotoPath);
        }

        if ($oldDocumentPath && $oldDocumentPath !== $profile->identification_document_path) {
            Storage::disk('private')->delete($oldDocumentPath);
        }

        $request->user()->update(['phone' => $validated['phone'] ?? null]);

        return redirect()->route('jobseeker.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
