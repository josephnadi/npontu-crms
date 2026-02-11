<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' ' . $this->faker->randomElement(['Partners', 'Systems', 'Solutions', 'Global']),
            'type' => $this->faker->randomElement(['referral', 'reseller', 'technology', 'service', 'other']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending', 'terminated']),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'commission_rate' => $this->faker->randomFloat(2, 5, 25),
            'user_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
