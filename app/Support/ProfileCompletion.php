<?php

namespace App\Support;

use App\Models\User;

class ProfileCompletion
{
    /**
     * Calculate profile completeness from fields that actually exist on the
     * job seeker profile. Optional fields are weighted equally and clearly
     * surfaced so candidates know what to do next.
     *
     * @return array{percent:int, completed:int, total:int, checks:array<int, array<string, mixed>>, missing:array<int, array<string, mixed>>}
     */
    public static function for(User $user): array
    {
        $profile = $user->jobSeekerProfile;

        $firstName = $profile?->first_name ?: $user->first_name;
        $lastName = $profile?->last_name ?: $user->last_name;
        $hasLocation = filled($profile?->city) || filled($profile?->state) || filled($profile?->address);

        $checks = [
            [
                'key' => 'personal',
                'label' => 'Personal information',
                'message' => 'Add your name and location so employers know who you are.',
                'complete' => filled($firstName) && filled($lastName) && $hasLocation,
                'url' => route('jobseeker.profile.edit').'#personal',
            ],
            [
                'key' => 'summary',
                'label' => 'Professional summary',
                'message' => 'Write a short summary about your experience and goals.',
                'complete' => filled($profile?->bio),
                'url' => route('jobseeker.profile.edit').'#summary',
            ],
            [
                'key' => 'skills',
                'label' => 'Skills',
                'message' => 'List the skills you want employers to find you for.',
                'complete' => ! empty($profile?->skills),
                'url' => route('jobseeker.profile.edit').'#skills',
            ],
            [
                'key' => 'education',
                'label' => 'Education',
                'message' => 'Select your highest level of education.',
                'complete' => filled($profile?->education_level),
                'url' => route('jobseeker.profile.edit').'#education',
            ],
            [
                'key' => 'experience',
                'label' => 'Experience level',
                'message' => 'Tell us how much relevant experience you have.',
                'complete' => filled($profile?->experience_level),
                'url' => route('jobseeker.profile.edit').'#experience',
            ],
            [
                'key' => 'preferences',
                'label' => 'Career preferences',
                'message' => 'Set the roles, locations and work type you prefer.',
                'complete' => filled($profile?->employment_preference)
                    || ! empty($profile?->preferred_categories)
                    || ! empty($profile?->preferred_locations),
                'url' => route('jobseeker.profile.edit').'#preferences',
            ],
            [
                'key' => 'cv',
                'label' => 'CV / resume',
                'message' => 'Upload a CV so you can attach it to applications.',
                'complete' => $user->cvs()->exists(),
                'url' => route('cvs.index'),
            ],
        ];

        $completed = count(array_filter($checks, fn (array $check) => $check['complete']));
        $total = count($checks);

        return [
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            'completed' => $completed,
            'total' => $total,
            'checks' => $checks,
            'missing' => array_values(array_filter($checks, fn (array $check) => ! $check['complete'])),
        ];
    }
}
