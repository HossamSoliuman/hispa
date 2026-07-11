<?php

namespace Database\Factories\Startup;

use App\Models\Startup\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup\Loan>
 */
class LoanFactory extends Factory
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
            'name' => fake()->words(2, true),
            'lender' => fake()->company(),
            'amount' => fake()->randomFloat(2, 1000, 100000),
            'date' => fake()->date(),
            'installments_count' => 12,
            'installment_amount' => fake()->randomFloat(2, 100, 1000),
            'first_installment_date' => fake()->date(),
            'borne_by' => 'project',
            'partner_id' => null,
            'status' => 'active',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
