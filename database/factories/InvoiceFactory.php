<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . strtoupper($this->faker->bothify('??####')),
            'client_id' => \App\Models\Client::factory(),
            'issue_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'subtotal' => 0, // Will be calculated
            'tax_total' => 0, // Will be calculated
            'total_amount' => 0, // Will be calculated
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']),
            'notes' => $this->faker->sentence(),
            'owner_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
