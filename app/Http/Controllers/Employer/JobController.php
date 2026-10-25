<?php

namespace App\Http\Controllers\Employer;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:employer', 'employer.verified', 'subscription']);
    }

    public function index()
    {
        $profile = auth()->user()->employerProfile;
        $jobs = $profile->jobs()->withCount('applications')->latest()->paginate(10);

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $this->authorize('create', Job::class);
        $categories = JobCategory::where('is_active', true)->get();

        return view('employer.jobs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_category_id' => 'required|exists:job_categories,id',
            'type' => 'nullable|in:full_time,part_time,contract,internship,domestic',
            'employment_type' => 'nullable|in:full_time,part_time,contract,temporary,live_in,live_out',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'location' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'benefits' => 'nullable|string',
            'is_remote' => 'nullable|boolean',
        ]);

        $profile = auth()->user()->employerProfile;

        $employmentType = $validated['employment_type'] ?? ($validated['type'] === 'domestic' ? 'live_in' : ($validated['type'] ?? 'full_time'));
        if (isset($validated['type']) && in_array($validated['type'], ['full_time', 'part_time', 'contract'])) {
            $employmentType = $validated['type'];
        }

        $job = $profile->jobs()->create([
            'title' => $validated['title'],
            'job_category_id' => $validated['job_category_id'],
            'employment_type' => $employmentType,
            'type' => $validated['type'] ?? $employmentType,
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'responsibilities' => $validated['responsibilities'] ?? null,
            'location' => $validated['location'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'application_deadline' => $validated['application_deadline'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'slug' => Str::slug($validated['title'].'-'.Str::random(6)),
            'status' => JobStatus::PENDING,
            'is_remote' => $request->boolean('is_remote'),
        ]);

        try {
            AuditLog::record('job.created', $job);
        } catch (\Throwable $e) {
        }

        return redirect()->route('employer.jobs.index')->with('status', 'Job submitted for moderation.');
    }

    public function show(Job $job)
    {
        $this->authorize('view', $job);

        return view('employer.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $this->authorize('update', $job);
        $categories = JobCategory::where('is_active', true)->get();

        return view('employer.jobs.edit', compact('job', 'categories'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_category_id' => 'required|exists:job_categories,id',
            'type' => 'nullable|in:full_time,part_time,contract,internship,domestic',
            'employment_type' => 'nullable|in:full_time,part_time,contract,temporary,live_in,live_out',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'location' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'benefits' => 'nullable|string',
            'is_remote' => 'nullable|boolean',
        ]);

        $employmentType = $validated['employment_type'] ?? ($validated['type'] === 'domestic' ? 'live_in' : ($validated['type'] ?? $job->employment_type ?? 'full_time'));
        if (isset($validated['type']) && in_array($validated['type'], ['full_time', 'part_time', 'contract'])) {
            $employmentType = $validated['type'];
        }

        $job->update([
            'title' => $validated['title'],
            'job_category_id' => $validated['job_category_id'],
            'employment_type' => $employmentType,
            'type' => $validated['type'] ?? $employmentType,
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'responsibilities' => $validated['responsibilities'] ?? null,
            'location' => $validated['location'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'application_deadline' => $validated['application_deadline'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'is_remote' => $request->boolean('is_remote'),
            'status' => JobStatus::PENDING,
            'published_at' => null,
        ]);

        return redirect()->route('employer.jobs.index')->with('status', 'Job updated and resubmitted for moderation.');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);
        $job->delete();

        return redirect()->route('employer.jobs.index')->with('status', 'Job deleted.');
    }
}
