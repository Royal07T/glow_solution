<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\EmployerProfile;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function create()
    {
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.jobs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validated = $this->validatedJob($request);
        $profile = $this->agencyProfile($request->user());

        $job = $profile->jobs()->create([
            ...$validated,
            'slug' => Str::slug($validated['title'].'-'.Str::random(6)),
            'salary_currency' => strtoupper($validated['salary_currency'] ?? 'NGN'),
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'positions_available' => $validated['positions_available'] ?? 1,
            'status' => JobStatus::APPROVED->value,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'published_at' => now(),
        ]);

        try {
            AuditLog::record('job.created', $job);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Job published successfully.');
    }

    public function edit(Job $job)
    {
        $this->authorize('update', $job);
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.jobs.edit', compact('job', 'categories'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $this->validatedJob($request);

        $job->update([
            ...$validated,
            'salary_currency' => strtoupper($validated['salary_currency'] ?? 'NGN'),
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'positions_available' => $validated['positions_available'] ?? 1,
        ]);

        try {
            AuditLog::record('job.updated', $job);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Job updated successfully.');
    }

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
        $job->load(['employerProfile.user', 'category'])->loadCount('applications');

        $applications = $job->applications()
            ->with('jobSeeker')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.jobs.show', compact('job', 'applications'));
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
        $owner = $job->employerProfile?->user;

        if ($owner && ! $owner->isAdmin()) {
            $owner->notify(new SystemNotification(
                'Job approved',
                'Your job "'.$job->title.'" is now live and visible to job seekers.',
                route('employer.jobs.show', $job, false),
                'success',
            ));
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
            AuditLog::record('job.rejected', $job);
        } catch (\Throwable $e) {
        }

        $job->loadMissing('employerProfile.user');
        $owner = $job->employerProfile?->user;

        if ($owner && ! $owner->isAdmin()) {
            $owner->notify(new SystemNotification(
                'Job needs attention',
                'Your job "'.$job->title.'" was not approved. Review the feedback and resubmit it for review.',
                route('employer.jobs.edit', $job, false),
                'warning',
            ));
        }

        return redirect()->back()->with('success', 'Job rejected.');
    }

    private function validatedJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'job_category_id' => ['nullable', 'exists:job_categories,id'],
            'type' => ['required', 'in:domestic,professional'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,temporary,live_in,live_out'],
            'description' => ['required', 'string', 'max:5000'],
            'responsibilities' => ['nullable', 'string', 'max:3000'],
            'qualifications' => ['nullable', 'string', 'max:3000'],
            'benefits' => ['nullable', 'string', 'max:3000'],
            'location' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'salary_negotiable' => ['sometimes', 'boolean'],
            'application_deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'positions_available' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
    }

    private function agencyProfile(User $user): EmployerProfile
    {
        return $user->employerProfile()->firstOrCreate([], [
            'type' => 'business',
            'company_name' => 'Glow Recruitment Agency',
            'contact_person' => $user->name,
            'country' => 'Nigeria',
            'description' => 'Listings managed directly by the Glow Recruitment Agency team.',
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);
    }
}
