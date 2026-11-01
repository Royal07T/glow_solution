<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => 'general',
            'rating' => null,
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(),
        ];
    }
}
