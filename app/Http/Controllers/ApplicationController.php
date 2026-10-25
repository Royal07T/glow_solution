<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Job;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = auth()->user()->applications()
            ->with(['job.employerProfile'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = auth()->user()->applications()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = ApplicationStatus::cases();

        return view('applications.index', compact('applications', 'counts', 'statuses'));
    }

    public function interviews()
    {
        $applications = auth()->user()->applications()
            ->with(['job.employerProfile'])
            ->where('status', ApplicationStatus::INTERVIEW->value)
            ->latest('status_updated_at')
            ->paginate(10);

        return view('jobseeker.interviews', compact('applications'));
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);
        $application->load(['job.employerProfile', 'cv']);

        return view('jobseeker.applications.show', compact('application'));
    }

    public function create(Job $job)
    {
        $this->authorize('create', Application::class);
        $this->ensureJobIsOpen($job);

        $existing = Application::where('job_id', $job->id)
            ->where('job_seeker_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->route('jobs.show', $job)->with('error', 'You have already applied for this job.');
        }

        $cvs = auth()->user()->cvs;

        return view('applications.create', compact('job', 'cvs'));
    }

    public function store(Request $request, Job $job)
    {
        $this->authorize('create', Application::class);
        $this->ensureJobIsOpen($job);

        $validated = $request->validate([
            'cv_id' => 'nullable|exists:cvs,id',
            'cover_letter' => 'nullable|string|max:2000',
        ]);

        if ($request->cv_id) {
            $cv = auth()->user()->cvs()->find($request->cv_id);
            if (! $cv) {
                abort(403);
            }
        }

        $existing = Application::where('job_id', $job->id)
            ->where('job_seeker_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->route('jobs.show', $job)->with('error', 'You have already applied for this job.');
        }

        $application = Application::create([
            'job_id' => $job->id,
            'job_seeker_id' => auth()->id(),
            'cv_id' => $validated['cv_id'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'status' => 'submitted',
            'applied_at' => now(),
        ]);

        $job->loadMissing('employerProfile.user');
        $job->employerProfile?->user?->notify(new SystemNotification(
            'New application received',
            auth()->user()->name.' applied for "'.$job->title.'".',
            route('employer.applications.show', $application, false),
            'info',
        ));

        return redirect()->route('applications.index')->with('success', 'Application submitted successfully.');
    }

    private function ensureJobIsOpen(Job $job): void
    {
        $deadlinePassed = $job->application_deadline && $job->application_deadline->isPast();

        if (! $job->isApproved() || $job->isExpired() || $deadlinePassed) {
            abort(404);
        }
    }

    public function withdraw(Application $application)
    {
        $this->authorize('withdraw', $application);

        $application->update([
            'status' => ApplicationStatus::WITHDRAWN,
            'status_updated_at' => now(),
        ]);

        $application->loadMissing(['job.employerProfile.user']);
        $application->job?->employerProfile?->user?->notify(new SystemNotification(
            'Application withdrawn',
            auth()->user()->name.' withdrew their application for "'.$application->job?->title.'".',
            route('employer.applications.show', $application, false),
            'warning',
        ));

        return redirect()->route('applications.index')->with('success', 'Application withdrawn.');
    }
}
