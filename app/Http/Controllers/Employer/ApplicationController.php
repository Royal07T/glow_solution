<?php

namespace App\Http\Controllers\Employer;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:employer', 'employer.verified']);
    }

    public function index(Request $request)
    {
        $profile = auth()->user()->employerProfile;
        $jobs = $profile->jobs()->latest()->get();
        $applications = Application::whereHas('job', function ($q) use ($profile, $request) {
            $q->where('employer_profile_id', $profile->id);
            if ($request->filled('job')) {
                $q->where('id', $request->job);
            }
        })->with(['job', 'jobSeeker', 'cv'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()->paginate(15)->withQueryString();

        return view('employer.applications.index', compact('applications', 'jobs'));
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

        $previousStatus = $application->status;

        $application->update([
            'status' => $validated['status'],
            'employer_notes' => $validated['employer_notes'] ?? $application->employer_notes,
            'status_updated_at' => now(),
        ]);

        if ($previousStatus?->value !== $validated['status']) {
            $application->loadMissing(['job', 'jobSeeker']);
            $status = ApplicationStatus::tryFrom($validated['status']);

            $application->jobSeeker?->notify(new SystemNotification(
                'Application update',
                'Your application for "'.$application->job?->title.'" is now '.($status?->label() ?? $validated['status']).'.',
                route('applications.show', $application, false),
                $status?->variant() ?? 'info',
            ));
        }

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }
}
