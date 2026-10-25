<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobSeekerProfile;
use App\Support\ProfileCompletion;
use Illuminate\Http\Request;

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
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
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

        $profile = auth()->user()->jobSeekerProfile;

        if (! $profile) {
            $profile = new JobSeekerProfile(['user_id' => auth()->id()]);
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
        ]);

        $profile->save();

        return redirect()->route('jobseeker.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
