<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Job $job): bool
    {
        if ($job->status === 'approved' && $job->published_at && $job->published_at->isPast() && !$job->isExpired()) {
            return true;
        }

        if ($user && $user->isAdmin()) {
            return true;
        }

        if ($user && $user->isEmployer()) {
            return $job->employerProfile && $job->employerProfile->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if (!$user->isEmployer()) {
            return false;
        }

        $profile = $user->employerProfile;
        if (!$profile || !$profile->isVerified()) {
            return false;
        }

        // Check subscription limits
        $subscription = $user->activeSubscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        // Check if under limit
        $entitlements = $subscription->plan->entitlements ?? [];
        $limit = $entitlements['active_job_listings'] ?? 0;
        
        if ($limit == -1) { // unlimited
            return true;
        }
        
        $currentCount = Job::whereHas('employerProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereIn('status', ['approved', 'pending'])->count();
        
        return $currentCount < $limit;
    }

    public function update(User $user, Job $job): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEmployer()) {
            return $job->employerProfile && $job->employerProfile->user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Job $job): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEmployer()) {
            return $job->employerProfile && $job->employerProfile->user_id === $user->id && $job->status !== 'approved';
        }

        return false;
    }

    public function approve(User $user, Job $job): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Job $job): bool
    {
        return $user->isAdmin();
    }
}
