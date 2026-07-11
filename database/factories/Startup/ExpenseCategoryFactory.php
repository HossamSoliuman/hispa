<?php

namespace Database\Factories\Startup;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->state(['role' => 'owner']),
            'name_ar' => fake()->unique()->word(),
            'name_en' => fake()->unique()->word(),
            'is_active' => true,
        ];
    }
}
