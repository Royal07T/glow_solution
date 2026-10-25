<?php

namespace App\Http\Controllers\JobSeeker;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\User;
use App\Support\ProfileCompletion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->jobSeekerProfile;

        $stats = [
            'total_applications' => $user->applications()->count(),
            'under_review' => $user->applications()
                ->whereIn('status', [ApplicationStatus::UNDER_REVIEW->value])
                ->count(),
            'interviews' => $user->applications()
                ->where('status', ApplicationStatus::INTERVIEW->value)
                ->count(),
            'saved_jobs' => $user->savedJobs()->count(),
        ];

        $applications = $user->applications()
            ->with(['job.employerProfile'])
            ->latest('applied_at')
            ->take(5)
            ->get();

        $interviews = $user->applications()
            ->with(['job.employerProfile'])
            ->where('status', ApplicationStatus::INTERVIEW->value)
            ->latest('status_updated_at')
            ->take(3)
            ->get();

        $savedJobs = $user->savedJobs()
            ->with(['employerProfile'])
            ->latest('saved_jobs.created_at')
            ->take(3)
            ->get();

        $recommended = $this->recommendedJobs($user)->take(4);

        $activity = $user->applications()
            ->with('job')
            ->whereNotNull('status_updated_at')
            ->latest('status_updated_at')
            ->take(5)
            ->get();

        $completion = ProfileCompletion::for($user);
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get();

        $appliedJobIds = $user->applications()->pluck('job_id')->all();
        $savedJobIds = $user->savedJobs()->pluck('jobs.id')->all();

        return view('jobseeker.dashboard', compact(
            'user',
            'profile',
            'stats',
            'applications',
            'interviews',
            'savedJobs',
            'recommended',
            'activity',
            'completion',
            'categories',
            'appliedJobIds',
            'savedJobIds'
        ));
    }

    public function recommended()
    {
        $user = auth()->user();
        $profile = $user->jobSeekerProfile;
        $completion = ProfileCompletion::for($user);

        $all = $this->recommendedJobs($user);
        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 9;

        $jobs = new LengthAwarePaginator(
            $all->slice(($page - 1) * $perPage, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
        $jobs->withQueryString();

        $appliedJobIds = $user->applications()->pluck('job_id')->all();
        $savedJobIds = $user->savedJobs()->pluck('jobs.id')->all();

        return view('jobseeker.recommended', compact(
            'user',
            'profile',
            'completion',
            'jobs',
            'appliedJobIds',
            'savedJobIds'
        ));
    }

    /**
     * Transparent, profile-based job matching. Jobs are scored by category,
     * preferred location, employment preference and skills, then ordered by
     * score and posting date. No external service or fabricated data is used.
     */
    private function recommendedJobs(User $user): Collection
    {
        $profile = $user->jobSeekerProfile;
        $skills = collect($profile?->skills ?? [])
            ->filter()
            ->map(fn ($skill) => trim((string) $skill))
            ->filter()
            ->values();
        $categories = collect($profile?->preferred_categories ?? [])
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->values();
        $locations = collect($profile?->preferred_locations ?? [])
            ->filter()
            ->map(fn ($location) => strtolower(trim((string) $location)))
            ->filter()
            ->values();
        $preference = $profile?->employment_preference;

        $appliedJobIds = $user->applications()->pluck('job_id')->all();

        $candidates = Job::query()
            ->with(['employerProfile', 'category'])
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->when($appliedJobIds, fn ($query) => $query->whereNotIn('id', $appliedJobIds))
            ->latest('published_at')
            ->limit(120)
            ->get();

        $scored = $candidates->map(function (Job $job) use ($skills, $categories, $locations, $preference) {
            $score = 0;
            $reasons = [];

            if ($categories->contains((string) $job->job_category_id)) {
                $score += 4;
                $reasons[] = 'Matches your preferred category';
            }

            $jobLocation = strtolower((string) $job->location);
            if ($locations->isNotEmpty() && $locations->contains(fn ($location) => $jobLocation !== '' && str_contains($jobLocation, $location))) {
                $score += 3;
                $reasons[] = 'In a preferred location';
            }

            if ($preference && $preference !== 'any' && $job->employment_type === $preference) {
                $score += 2;
                $reasons[] = 'Matches your work type';
            }

            $haystack = strtolower($job->title.' '.$job->description.' '.$job->qualifications);
            foreach ($skills as $skill) {
                if ($skill !== '' && str_contains($haystack, strtolower($skill))) {
                    $score += 1;
                    $reasons[] = 'Matches your skill: '.$skill;
                }
            }

            return [
                'job' => $job,
                'score' => $score,
                'reasons' => array_values(array_unique($reasons)),
            ];
        });

        $sorted = $scored->sort(function (array $a, array $b) {
            if ($a['score'] === $b['score']) {
                return ($b['job']->published_at?->timestamp ?? 0) <=> ($a['job']->published_at?->timestamp ?? 0);
            }

            return $b['score'] <=> $a['score'];
        });

        return $sorted->values()->map(function (array $item) {
            $item['job']->setAttribute('match_reasons', $item['reasons']);
            $item['job']->setAttribute('match_score', $item['score']);

            return $item['job'];
        });
    }
}
