<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Job::with(['employerProfile', 'category'])
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('job_category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $jobs = $query->latest('published_at')->paginate(12);
        $categories = JobCategory::where('is_active', true)->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    public function show(Job $job)
    {
        if ($job->status !== 'approved' || !$job->published_at || $job->published_at->isFuture() || $job->isExpired()) {
            abort(404);
        }

        $job->load(['employerProfile', 'category']);
        $relatedJobs = Job::where('status', 'approved')
            ->where('id', '!=', $job->id)
            ->where(function($q) use ($job) {
                $q->where('job_category_id', $job->job_category_id)
                  ->orWhere('type', $job->type);
            })
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('jobs.show', compact('job', 'relatedJobs'));
    }
}
