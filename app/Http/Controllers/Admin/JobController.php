<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with(['employerProfile.user', 'category'])->latest()->paginate(15);
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
            \App\Models\AuditLog::record('job.approved', $job);
        } catch (\Throwable $e) {
        }

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
            \App\Models\AuditLog::record('job.rejected', $job);
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'Job rejected.');
    }
}
