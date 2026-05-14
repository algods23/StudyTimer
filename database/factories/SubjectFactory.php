<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Math', 'Science', 'English', 'History', 'Programming']),
            'color' => fake()->randomElement(['#fb7185', '#f43f5e', '#38bdf8', '#a78bfa']),
            'weekly_goal_minutes' => fake()->numberBetween(180, 720),
        ];
    }
}
