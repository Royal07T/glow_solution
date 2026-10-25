<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Job;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::with(['employerProfile.user', 'category'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%'.$request->string('search').'%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = Job::where('status', 'pending')->count();

        return view('admin.jobs.index', compact('jobs', 'pendingCount'));
    }

    public function show(Job $job)
    {
        $job->load(['employerProfile.user', 'category', 'applications']);

        return view('admin.jobs.show', compact('job'));
    }

    public function approve(Request $request, Job $job)
    {
        $job->update([
            'status' => 'approved',
            'published_at' => now(),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        try {
            AuditLog::record('job.approved', $job);
        } catch (\Throwable $e) {
        }

        $job->loadMissing('employerProfile.user');
        $job->employerProfile?->user?->notify(new SystemNotification(
            'Job approved',
            'Your job "'.$job->title.'" is now live and visible to job seekers.',
            route('employer.jobs.show', $job, false),
            'success',
        ));

        return redirect()->back()->with('success', 'Job approved successfully.');
    }

    public function reject(Request $request, Job $job)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $job->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        try {
            AuditLog::record('job.rejected', $job);
        } catch (\Throwable $e) {
        }

        $job->loadMissing('employerProfile.user');
        $job->employerProfile?->user?->notify(new SystemNotification(
            'Job needs attention',
            'Your job "'.$job->title.'" was not approved. Review the feedback and resubmit it for review.',
            route('employer.jobs.edit', $job, false),
            'warning',
        ));

        return redirect()->back()->with('success', 'Job rejected.');
    }
}
