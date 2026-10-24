<?php

namespace App\Policies;

use App\Models\Cv;
use App\Models\User;

class CvPolicy
{
    public function view(User $user, Cv $cv): bool
    {
        // Only the owner can view their CV
        return $user->id === $cv->user_id || $user->isAdmin();
    }

    public function download(User $user, Cv $cv): bool
    {
        // Owner or employer who has received application from this CV
        if ($user->id === $cv->user_id || $user->isAdmin()) {
            return true;
        }

        if ($user->isEmployer()) {
            // Check if employer has an application with this CV
            return $cv->applications()->whereHas('job.employerProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isJobSeeker();
    }

    public function update(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function delete(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }
}
