<?php

namespace App\Actions;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateJobApplication
{
    public function execute(User $user, Job $job, array $data): Application
    {
        return DB::transaction(function () use ($user, $job, $data) {
            // Check duplicate
            $existing = Application::where('job_id', $job->id)
                ->where('job_seeker_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new \Exception('You have already applied for this job.');
            }

            return Application::create([
                'job_id' => $job->id,
                'job_seeker_id' => $user->id,
                'cv_id' => $data['cv_id'] ?? null,
                'cover_letter' => $data['cover_letter'] ?? null,
                'status' => 'submitted',
                'applied_at' => now(),
            ]);
        });
    }
}
