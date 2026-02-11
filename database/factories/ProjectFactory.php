<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'progress' => $this->faker->numberBetween(0, 100),
            'start_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'budget' => $this->faker->randomFloat(2, 5000, 50000),
            'owner_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
