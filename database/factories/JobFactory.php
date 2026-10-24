<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'slug' => Str::slug(fake()->jobTitle()) . '-' . Str::random(6),
            'type' => fake()->randomElement(['domestic', 'professional']),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract']),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'status' => 'pending',
            'positions_available' => 1,
        ];
    }
}
