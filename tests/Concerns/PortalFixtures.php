<?php

namespace Tests\Concerns;

use App\Models\Application;
use App\Models\EmployerProfile;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

trait PortalFixtures
{
    protected function jobSeeker(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'job_seeker',
            'email_verified_at' => now(),
        ], $attributes));

        $user->jobSeekerProfile()->create([
            'first_name' => 'Test',
            'last_name' => 'Seeker',
            'city' => 'Lagos',
            'country' => 'Nigeria',
        ]);

        return $user;
    }

    protected function employer(bool $verified = true, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'employer',
            'email_verified_at' => now(),
        ], $attributes));

        EmployerProfile::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Verified Company',
            'contact_person' => $user->name,
            'verification_status' => $verified ? 'approved' : 'pending',
            'verified_at' => $verified ? now() : null,
        ]);

        return $user->fresh();
    }

    protected function admin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function plan(string $slug = 'free-jobseeker', string $role = 'job_seeker', bool $free = true, array $entitlements = []): SubscriptionPlan
    {
        return SubscriptionPlan::create([
            'name' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'target_role' => $role,
            'interval' => 'monthly',
            'price' => $free ? 0 : 10000,
            'currency' => 'NGN',
            'description' => 'Test plan',
            'entitlements' => $entitlements ?: [
                'active_job_listings' => 5,
                'job_applications' => -1,
            ],
            'is_active' => true,
            'is_free' => $free,
            'sort_order' => 1,
        ]);
    }

    protected function subscribe(User $user, SubscriptionPlan $plan): Subscription
    {
        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    protected function employerWithPlan(bool $verified = true): User
    {
        $employer = $this->employer($verified);
        $this->subscribe($employer, $this->plan('employer-plan-'.$employer->id, 'employer'));

        return $employer->fresh();
    }

    protected function job(User $employer, array $attributes = []): Job
    {
        return Job::factory()->create(array_merge([
            'employer_profile_id' => $employer->employerProfile->id,
            'job_category_id' => JobCategory::factory()->create()->id,
            'status' => 'approved',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addMonth(),
            'salary_currency' => 'NGN',
        ], $attributes));
    }

    protected function application(User $seeker, Job $job, array $attributes = []): Application
    {
        return Application::create(array_merge([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'status' => 'submitted',
            'applied_at' => now(),
        ], $attributes));
    }
}
