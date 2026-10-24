<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $employer1 = User::where('email', 'employer1@glowrecruitment.com')->first();
        $jobseeker1 = User::where('email', 'jobseeker1@glowrecruitment.com')->first();

        if (! $employer1?->employerProfile || ! $jobseeker1) {
            return;
        }

        $premium = SubscriptionPlan::where('slug', 'premium-employer')->first();
        $starter = SubscriptionPlan::where('slug', 'starter-employer')->first();
        $jobseekerPlan = SubscriptionPlan::where('slug', 'free-jobseeker')->first();

        if ($premium) {
            Subscription::create([
                'user_id' => $employer1->id,
                'subscription_plan_id' => $premium->id,
                'status' => 'active',
                'starts_at' => now()->subWeek(),
                'ends_at' => now()->addMonth(),
            ]);
        }

        $employer2 = User::where('email', 'employer2@glowrecruitment.com')->first();
        if ($employer2 && $starter) {
            Subscription::create([
                'user_id' => $employer2->id,
                'subscription_plan_id' => $starter->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);
        }

        if ($jobseekerPlan) {
            Subscription::create([
                'user_id' => $jobseeker1->id,
                'subscription_plan_id' => $jobseekerPlan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);
        }

        $categories = JobCategory::pluck('id', 'slug');

        $jobs = [
            [
                'title' => 'Live-in Housekeeper',
                'category' => 'housekeeping',
                'type' => 'domestic',
                'employment_type' => 'live_in',
                'description' => 'We are looking for a trustworthy live-in housekeeper to manage a private residence in Ikoyi.',
                'responsibilities' => 'General cleaning, laundry, meal preparation and errands.',
                'qualifications' => 'At least 2 years of verifiable housekeeping experience.',
                'location' => 'Ikoyi, Lagos',
                'salary_min' => 180000,
                'salary_max' => 250000,
            ],
            [
                'title' => 'Experienced Nanny',
                'category' => 'childcare-nanny',
                'type' => 'domestic',
                'employment_type' => 'full_time',
                'description' => 'A warm and reliable nanny needed for two children aged 3 and 6.',
                'responsibilities' => 'School runs, meals, play and homework support.',
                'qualifications' => 'First aid training and childcare references.',
                'location' => 'Lekki, Lagos',
                'salary_min' => 200000,
                'salary_max' => 280000,
            ],
            [
                'title' => 'Front Desk Administrator',
                'category' => 'administration',
                'type' => 'professional',
                'employment_type' => 'full_time',
                'description' => 'Join a growing professional services firm as the first point of contact for clients.',
                'responsibilities' => 'Manage reception, schedules and client enquiries.',
                'qualifications' => 'BSc or HND with excellent communication skills.',
                'location' => 'Victoria Island, Lagos',
                'salary_min' => 250000,
                'salary_max' => 350000,
            ],
        ];

        $created = [];

        foreach ($jobs as $data) {
            $job = Job::create([
                'employer_profile_id' => $employer1->employerProfile->id,
                'job_category_id' => $categories[$data['category']] ?? null,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(8)),
                'type' => $data['type'],
                'employment_type' => $data['employment_type'],
                'description' => $data['description'],
                'responsibilities' => $data['responsibilities'],
                'qualifications' => $data['qualifications'],
                'location' => $data['location'],
                'city' => 'Lagos',
                'state' => 'Lagos',
                'salary_min' => $data['salary_min'],
                'salary_max' => $data['salary_max'],
                'salary_currency' => 'NGN',
                'positions_available' => 1,
                'status' => 'approved',
                'published_at' => now()->subDays(2),
                'expires_at' => now()->addDays(30),
                'reviewed_by' => User::where('role', 'admin')->value('id'),
                'reviewed_at' => now()->subDays(2),
            ]);

            $created[] = $job;
        }

        if ($created) {
            Application::create([
                'job_id' => $created[0]->id,
                'job_seeker_id' => $jobseeker1->id,
                'cover_letter' => 'I have five years of experience managing private residences.',
                'status' => 'shortlisted',
                'applied_at' => now()->subDay(),
                'status_updated_at' => now(),
            ]);

            Application::create([
                'job_id' => $created[2]->id,
                'job_seeker_id' => $jobseeker1->id,
                'cover_letter' => 'I would love to bring my client service experience to your team.',
                'status' => 'submitted',
                'applied_at' => now()->subHours(6),
            ]);

            $jobseeker1->savedJobs()->syncWithoutDetaching([$created[1]->id]);
        }
    }
}
