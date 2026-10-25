<?php

namespace App\Http\Controllers;

use App\Models\Job;

class SavedJobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:job_seeker']);
    }

    public function index()
    {
        $savedJobs = auth()->user()->savedJobs()
            ->with(['employerProfile', 'category'])
            ->latest('saved_jobs.created_at')
            ->paginate(10);

        $appliedJobIds = auth()->user()->applications()->pluck('job_id')->all();

        return view('jobseeker.saved-jobs.index', compact('savedJobs', 'appliedJobIds'));
    }

    public function toggle(Job $job)
    {
        if (! $job->isApproved() || $job->isExpired()) {
            abort(404);
        }

        $result = auth()->user()->savedJobs()->toggle($job->id);
        $saved = count($result['attached']) > 0;

        return back()->with('success', $saved ? 'Job saved to your list.' : 'Job removed from your saved jobs.');
    }

    public function destroy(Job $job)
    {
        auth()->user()->savedJobs()->detach($job->id);

        return back()->with('success', 'Job removed from your saved jobs.');
    }
}
