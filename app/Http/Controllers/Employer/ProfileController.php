<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\EmployerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = $request->user()->employerProfile ?? new EmployerProfile;

        return view('employer.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = $request->user()->employerProfile;

        if (! $profile) {
            abort(404, 'Employer profile missing.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:household,business'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'business_registration_number' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $old = $profile->only(['company_name', 'contact_person', 'business_registration_number']);
        $profile->update($validated);

        AuditLog::record('employer.profile.updated', $profile, $old, [
            'company_name' => $profile->company_name,
            'contact_person' => $profile->contact_person,
            'business_registration_number' => $profile->business_registration_number,
        ]);

        return redirect()->route('employer.profile.edit')->with('success', 'Company profile updated.');
    }
}
