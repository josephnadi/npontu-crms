<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'value' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => 'USD',
            'deal_stage_id' => \App\Models\DealStage::inRandomOrder()->first()?->id ?? 1,
            'client_id' => \App\Models\Client::factory(),
            'contact_id' => \App\Models\Contact::factory(),
            'expected_close_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'probability' => $this->faker->numberBetween(10, 100),
            'status' => $this->faker->randomElement(['open', 'won', 'lost']),
            'tags' => $this->faker->words(3),
            'owner_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'created_by' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
