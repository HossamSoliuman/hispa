<?php

namespace Database\Factories\Startup;

use App\Models\Startup\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup\Contribution>
 */
class ContributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'project_id' => fn (array $attributes): int => Partner::withoutGlobalScopes()->findOrFail($attributes['partner_id'])->project_id,
            'owner_id' => fn (array $attributes): int => Partner::withoutGlobalScopes()->findOrFail($attributes['partner_id'])->owner_id,
            'date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'card']),
            'type' => fake()->randomElement(['capital', 'reimbursement', 'extra']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
