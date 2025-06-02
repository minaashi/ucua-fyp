<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'head_name' => fake()->name(),
            'head_email' => fake()->unique()->safeEmail(),
            'head_phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
