<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobSeekerProfile;
use App\Models\EmployerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@glowrecruitment.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Job Seekers
        $jobseeker1 = User::create([
            'name' => 'John Doe',
            'email' => 'jobseeker1@glowrecruitment.com',
            'password' => Hash::make('password'),
            'role' => 'job_seeker',
            'email_verified_at' => now(),
        ]);
        JobSeekerProfile::create([
            'user_id' => $jobseeker1->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'city' => 'Lagos',
            'country' => 'Nigeria',
        ]);

        $jobseeker2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jobseeker2@glowrecruitment.com',
            'password' => Hash::make('password'),
            'role' => 'job_seeker',
            'email_verified_at' => now(),
        ]);
        JobSeekerProfile::create([
            'user_id' => $jobseeker2->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'city' => 'Abuja',
            'country' => 'Nigeria',
        ]);

        // Employers
        $employer1 = User::create([
            'name' => 'ABC Company',
            'email' => 'employer1@glowrecruitment.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'email_verified_at' => now(),
        ]);
        EmployerProfile::create([
            'user_id' => $employer1->id,
            'type' => 'business',
            'company_name' => 'ABC Company Ltd',
            'contact_person' => 'ABC Company',
            'city' => 'Lagos',
            'country' => 'Nigeria',
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        $employer2 = User::create([
            'name' => 'Mrs. Ade',
            'email' => 'employer2@glowrecruitment.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'email_verified_at' => now(),
        ]);
        EmployerProfile::create([
            'user_id' => $employer2->id,
            'type' => 'household',
            'contact_person' => 'Mrs. Ade',
            'city' => 'Abuja',
            'country' => 'Nigeria',
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);
    }
}
