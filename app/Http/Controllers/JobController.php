<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use App\Support\SearchTerm;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:job_categories,id'],
            'type' => ['nullable', 'in:domestic,professional'],
            'employment_type' => ['nullable', 'in:full_time,part_time,contract,temporary,live_in,live_out'],
            'location' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Job::with(['employerProfile', 'category'])
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        $keyword = SearchTerm::clean($validated['keyword'] ?? null);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%')
                    ->orWhere('qualifications', 'like', '%'.$keyword.'%')
                    ->orWhereHas('employerProfile', function ($employer) use ($keyword) {
                        $employer->where('company_name', 'like', '%'.$keyword.'%');
                    });
            });
        }

        if (filled($validated['category'] ?? null)) {
            $query->where('job_category_id', $validated['category']);
        }

        if (filled($validated['type'] ?? null)) {
            $query->where('type', $validated['type']);
        }

        if (filled($validated['employment_type'] ?? null)) {
            $query->where('employment_type', $validated['employment_type']);
        }

        $location = SearchTerm::clean($validated['location'] ?? null);

        if ($location !== '') {
            $query->where(function ($q) use ($location) {
                $q->where('location', 'like', '%'.$location.'%')
                    ->orWhere('city', 'like', '%'.$location.'%')
                    ->orWhere('state', 'like', '%'.$location.'%');
            });
        }

        $jobs = $query->latest('published_at')->paginate(12)->withQueryString();
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get();

        $savedJobIds = [];
        $appliedJobIds = [];

        if (auth()->check() && auth()->user()->isJobSeeker()) {
            $savedJobIds = auth()->user()->savedJobs()->pluck('jobs.id')->all();
            $appliedJobIds = auth()->user()->applications()->pluck('job_id')->all();
        }

        return view('jobs.index', compact('jobs', 'categories', 'savedJobIds', 'appliedJobIds'));
    }

    public function show(Job $job)
    {
        if (! $job->isApproved() || $job->isExpired()) {
            abort(404);
        }

        $job->load(['employerProfile', 'category']);

        $relatedJobs = Job::with(['employerProfile', 'category'])
            ->where('status', 'approved')
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('job_category_id', $job->job_category_id)
                    ->orWhere('type', $job->type);
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('published_at')
            ->take(4)
            ->get();

        $applied = false;
        $saved = false;

        if (auth()->check() && auth()->user()->isJobSeeker()) {
            $applied = auth()->user()->applications()->where('job_id', $job->id)->exists();
            $saved = auth()->user()->savedJobs()->where('jobs.id', $job->id)->exists();
        }

        $deadlinePassed = $job->application_deadline && $job->application_deadline->isPast();

        return view('jobs.show', compact('job', 'relatedJobs', 'applied', 'saved', 'deadlinePassed'));
    }
}
