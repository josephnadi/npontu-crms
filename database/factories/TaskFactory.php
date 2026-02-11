<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'type' => $this->faker->randomElement(['call', 'email', 'meeting', 'task', 'follow_up']),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'priority_score' => $this->faker->numberBetween(10, 100),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'owner_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'assigned_to' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
