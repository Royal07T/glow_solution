<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:employer', 'employer.verified']);
    }

    public function index()
    {
        $profile = auth()->user()->employerProfile;
        $applications = Application::whereHas('job', function($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->with(['job', 'jobSeeker', 'cv'])->latest()->paginate(15);

        return view('employer.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);
        $application->load(['job', 'jobSeeker.jobSeekerProfile', 'cv']);
        return view('employer.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $this->authorize('update', $application);

        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,shortlisted,interview,accepted,rejected,withdrawn',
            'employer_notes' => 'nullable|string',
        ]);

        $application->update([
            'status' => $validated['status'],
            'employer_notes' => $validated['employer_notes'] ?? $application->employer_notes,
            'status_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }
}
