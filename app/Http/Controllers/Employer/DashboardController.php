<?php

namespace App\Http\Controllers\Employer;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->employerProfile;
        $subscription = $user->activeSubscription()->with('plan')->first();

        $stats = [
            'active_jobs' => 0,
            'total_applications' => 0,
            'awaiting_review' => 0,
            'shortlisted' => 0,
            'interviews' => 0,
        ];

        $jobs = collect();
        $recentApplications = collect();
        $jobLimit = $subscription?->plan?->entitlements['active_job_listings'] ?? 0;

        if ($profile) {
            $jobs = $profile->jobs()
                ->withCount('applications')
                ->latest()
                ->take(5)
                ->get();

            $stats['active_jobs'] = $profile->jobs()
                ->where('status', 'approved')
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->count();

            $base = Application::query()->whereHas('job', fn ($query) => $query->where('employer_profile_id', $profile->id));

            $stats['total_applications'] = (clone $base)->count();
            $stats['awaiting_review'] = (clone $base)
                ->whereIn('status', [ApplicationStatus::SUBMITTED->value, ApplicationStatus::UNDER_REVIEW->value])
                ->count();
            $stats['shortlisted'] = (clone $base)
                ->where('status', ApplicationStatus::SHORTLISTED->value)
                ->count();
            $stats['interviews'] = (clone $base)
                ->where('status', ApplicationStatus::INTERVIEW->value)
                ->count();

            $recentApplications = Application::query()
                ->with(['job', 'jobSeeker'])
                ->whereHas('job', fn ($query) => $query->where('employer_profile_id', $profile->id))
                ->latest('applied_at')
                ->take(5)
                ->get();
        }

        return view('employer.dashboard', compact(
            'user',
            'profile',
            'subscription',
            'stats',
            'jobs',
            'recentApplications',
            'jobLimit'
        ));
    }
}
