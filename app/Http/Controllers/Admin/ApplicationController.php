<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::query()
            ->whereHas('applications')
            ->with('employerProfile')
            ->orderBy('title')
            ->get(['id', 'title', 'employer_profile_id']);

        $applications = Application::query()
            ->with(['job.employerProfile', 'jobSeeker'])
            ->when($request->filled('job'), fn ($query) => $query->where('job_id', $request->integer('job')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.applications.index', compact('applications', 'jobs'));
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);

        $application->load(['job.employerProfile.user', 'jobSeeker.jobSeekerProfile', 'cv']);

        return view('admin.applications.show', compact('application'));
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
