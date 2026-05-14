<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(),
            'type' => fake()->randomElement(['reminder', 'goal', 'success']),
            'scheduled_at' => fake()->optional()->dateTimeBetween('now', '+1 week'),
            'read_at' => null,
        ];
    }
}
