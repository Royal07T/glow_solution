<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = auth()->user()->applications()->with(['job.employerProfile'])->latest()->paginate(10);
        return view('applications.index', compact('applications'));
    }

    public function create(Job $job)
    {
        $this->authorize('create', Application::class);

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

        $validated = $request->validate([
            'cv_id' => 'nullable|exists:cvs,id',
            'cover_letter' => 'nullable|string',
        ]);

        if ($request->cv_id) {
            $cv = auth()->user()->cvs()->find($request->cv_id);
            if (!$cv) {
                abort(403);
            }
        }

        $existing = Application::where('job_id', $job->id)
            ->where('job_seeker_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->route('jobs.show', $job)->with('error', 'You have already applied for this job.');
        }

        Application::create([
            'job_id' => $job->id,
            'job_seeker_id' => auth()->id(),
            'cv_id' => $validated['cv_id'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'status' => 'submitted',
            'applied_at' => now(),
        ]);

        return redirect()->route('applications.index')->with('success', 'Application submitted successfully.');
    }

    public function withdraw(Application $application)
    {
        $this->authorize('withdraw', $application);

        $application->update([
            'status' => 'withdrawn',
            'status_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Application withdrawn.');
    }
}
