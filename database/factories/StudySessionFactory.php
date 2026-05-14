<?php

namespace Database\Factories;

use App\Models\StudySession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudySession>
 */
class StudySessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 month', 'now');
        $duration = fake()->numberBetween(25, 120);

        return [
            'started_at' => $startedAt,
            'ended_at' => (clone $startedAt)->modify("+{$duration} minutes"),
            'duration_minutes' => $duration,
            'mode' => fake()->randomElement(['custom', 'pomodoro']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
