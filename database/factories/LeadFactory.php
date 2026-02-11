<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'mobile' => $this->faker->phoneNumber(),
            'company_name' => $this->faker->company(),
            'job_title' => $this->faker->jobTitle(),
            'lead_source_id' => \App\Models\LeadSource::inRandomOrder()->first()?->id ?? 1,
            'status' => $this->faker->randomElement(['new', 'contacted', 'qualified', 'lost']),
            'score' => $this->faker->numberBetween(10, 100),
            'address_line1' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'notes' => $this->faker->paragraph(),
            'tags' => $this->faker->words(3),
            'owner_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'created_by' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
