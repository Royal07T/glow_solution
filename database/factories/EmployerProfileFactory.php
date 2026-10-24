<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployerProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'business',
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'verification_status' => 'pending',
        ];
    }
}
