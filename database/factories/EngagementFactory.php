<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Engagement>
 */
class EngagementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['email_open', 'link_click', 'page_view', 'form_submission']),
            'subject' => $this->faker->sentence(4),
            'description' => $this->faker->sentence(),
            'score' => $this->faker->numberBetween(1, 10),
            'engagement_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'user_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'created_by' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
        ];
    }
}
