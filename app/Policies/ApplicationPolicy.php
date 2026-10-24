<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function view(User $user, Application $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $application->job_seeker_id) {
            return true;
        }

        if ($user->isEmployer()) {
            return $application->job->employerProfile && $application->job->employerProfile->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isJobSeeker() && $user->hasVerifiedEmail();
    }

    public function update(User $user, Application $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEmployer()) {
            return $application->job->employerProfile && $application->job->employerProfile->user_id === $user->id;
        }

        return false;
    }

    public function withdraw(User $user, Application $application): bool
    {
        return $user->id === $application->job_seeker_id && $application->status === 'submitted';
    }
}
