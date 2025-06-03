<?php

namespace Database\Factories;

use App\Models\Warning;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warning>
 */
class WarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_id' => Report::factory(),
            'suggested_by' => User::factory(),
            'type' => fake()->randomElement(['minor', 'moderate', 'major']),
            'reason' => fake()->sentence(),
            'suggested_action' => fake()->sentence(),
            'status' => 'pending',
            'approved_by' => null,
            'admin_notes' => null,
            'approved_at' => null,
            'sent_at' => null,
            'recipient_id' => null,
            'warning_message' => null,
            'template_id' => null,
            'email_sent_at' => null,
            'email_delivery_status' => null,
        ];
    }

    /**
     * Indicate that the warning is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'warning_message' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the warning is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'admin_notes' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the warning is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'sent_at' => now(),
            'warning_message' => fake()->paragraph(),
            'email_sent_at' => now(),
            'email_delivery_status' => 'sent',
        ]);
    }
}
