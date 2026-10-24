<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = auth()->user()->jobSeekerProfile ?? new JobSeekerProfile();
        return view('jobseeker.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'skills' => 'nullable|array',
            'education_level' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|max:255',
            'preferred_categories' => 'nullable|array',
            'preferred_locations' => 'nullable|array',
            'employment_preference' => 'nullable|in:full_time,part_time,contract,temporary,domestic,any',
            'available_for_work' => 'boolean',
        ]);

        $profile = auth()->user()->jobSeekerProfile;
        if (!$profile) {
            $profile = new JobSeekerProfile(['user_id' => auth()->id()]);
        }
        $profile->fill($validated);
        $profile->save();

        return redirect()->route('jobseeker.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
