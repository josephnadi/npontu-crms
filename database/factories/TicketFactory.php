<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => 'TIC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'subject' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'pending', 'resolved', 'closed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'category' => $this->faker->randomElement(['technical', 'billing', 'feature_request', 'bug', 'general']),
            'client_id' => \App\Models\Client::factory(),
            'user_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'assigned_to' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
