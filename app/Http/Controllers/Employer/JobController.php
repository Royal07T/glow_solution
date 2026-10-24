<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:employer', 'employer.verified']);
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
            'job_category_id' => 'nullable|exists:job_categories,id',
            'type' => 'required|in:domestic,professional',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary,live_in,live_out',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'benefits' => 'nullable|string',
            'location' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'salary_currency' => 'nullable|string|max:3',
            'salary_negotiable' => 'boolean',
            'application_deadline' => 'nullable|date',
            'positions_available' => 'nullable|integer|min:1',
        ]);

        $profile = auth()->user()->employerProfile;
        $validated['employer_profile_id'] = $profile->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['status'] = 'pending';
        $validated['salary_negotiable'] = $request->has('salary_negotiable');

        Job::create($validated);

        return redirect()->route('employer.jobs.index')->with('success', 'Job posted successfully and is awaiting admin approval.');
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
            'job_category_id' => 'nullable|exists:job_categories,id',
            'type' => 'required|in:domestic,professional',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary,live_in,live_out',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'benefits' => 'nullable|string',
            'location' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'salary_currency' => 'nullable|string|max:3',
            'salary_negotiable' => 'boolean',
            'application_deadline' => 'nullable|date',
            'positions_available' => 'nullable|integer|min:1',
        ]);

        $validated['salary_negotiable'] = $request->has('salary_negotiable');
        $job->update($validated);

        return redirect()->route('employer.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);
        $job->delete();
        return redirect()->route('employer.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
