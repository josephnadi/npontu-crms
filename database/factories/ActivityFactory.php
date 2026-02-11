<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['call', 'email', 'meeting', 'note', 'task']),
            'subject' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'activity_date' => $this->faker->dateTimeBetween('-1 month', '+1 week'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'duration_minutes' => $this->faker->randomElement([15, 30, 45, 60, 120]),
            'owner_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'created_by' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
