<?php

namespace Database\Factories\Startup;

use App\Models\Startup\ExpenseCategory;
use App\Models\Startup\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'owner_id' => fn (array $attributes): int => Project::withoutGlobalScopes()->findOrFail($attributes['project_id'])->owner_id,
            'category_id' => ExpenseCategory::factory(),
            'date' => fake()->date(),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'payer_type' => 'project',
            'partner_id' => null,
            'loan_id' => null,
            'payment_method' => 'cash',
            'is_shared' => true,
            'attachment' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
