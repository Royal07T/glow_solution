<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'job' => ['nullable', 'integer', 'exists:jobs,id'],
            'status' => ['nullable', Rule::enum(ApplicationStatus::class)],
        ]);

        $jobId = $validated['job'] ?? null;
        $status = $validated['status'] ?? null;

        $jobs = Job::query()
            ->whereHas('applications')
            ->with('employerProfile')
            ->orderBy('title')
            ->get(['id', 'title', 'employer_profile_id']);

        $applications = Application::query()
            ->with(['job.employerProfile', 'jobSeeker'])
            ->when(filled($jobId), fn ($query) => $query->where('job_id', $jobId))
            ->when($status !== null, fn ($query) => $query->where('status', $status))
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
            'employer_notes' => 'nullable|string|max:2000',
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
