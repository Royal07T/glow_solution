<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Job Seeker Plans
        SubscriptionPlan::create([
            'name' => 'Free Job Seeker',
            'slug' => 'free-jobseeker',
            'target_role' => 'job_seeker',
            'interval' => 'monthly',
            'price' => 0,
            'currency' => 'NGN',
            'description' => 'Free access with limited features',
            'entitlements' => [
                'full_job_details' => false,
                'job_applications' => 3,
                'saved_jobs' => 5,
                'application_tracking' => true,
                'premium_job_alerts' => false,
                'career_resources' => false,
            ],
            'is_active' => true,
            'is_free' => true,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::create([
            'name' => 'Premium Job Seeker',
            'slug' => 'premium-jobseeker',
            'target_role' => 'job_seeker',
            'interval' => 'monthly',
            'price' => 5000,
            'currency' => 'NGN',
            'description' => 'Unlimited access to all job features',
            'entitlements' => [
                'full_job_details' => true,
                'job_applications' => -1,
                'saved_jobs' => -1,
                'application_tracking' => true,
                'premium_job_alerts' => true,
                'career_resources' => true,
            ],
            'is_active' => true,
            'is_free' => false,
            'sort_order' => 2,
        ]);

        // Employer Plans
        SubscriptionPlan::create([
            'name' => 'Basic Employer',
            'slug' => 'basic-employer',
            'target_role' => 'employer',
            'interval' => 'monthly',
            'price' => 10000,
            'currency' => 'NGN',
            'description' => 'Post up to 5 jobs per month',
            'entitlements' => [
                'active_job_listings' => 5,
                'applicant_management' => true,
                'recruitment_tools' => false,
                'company_profile_enhancements' => false,
            ],
            'is_active' => true,
            'is_free' => false,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::create([
            'name' => 'Premium Employer',
            'slug' => 'premium-employer',
            'target_role' => 'employer',
            'interval' => 'monthly',
            'price' => 25000,
            'currency' => 'NGN',
            'description' => 'Unlimited job postings and premium features',
            'entitlements' => [
                'active_job_listings' => -1,
                'applicant_management' => true,
                'recruitment_tools' => true,
                'company_profile_enhancements' => true,
            ],
            'is_active' => true,
            'is_free' => false,
            'sort_order' => 2,
        ]);
    }
}
