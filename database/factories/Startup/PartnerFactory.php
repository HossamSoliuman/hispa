<?php

namespace Database\Factories\Startup;

use App\Models\Startup\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup\Partner>
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
            'project_id' => Project::factory(),
            'owner_id' => fn (array $attributes): int => Project::withoutGlobalScopes()->findOrFail($attributes['project_id'])->owner_id,
            'name' => fake()->name(),
            'share_percent' => fake()->randomElement(['25.00', '50.00', '100.00']),
            'phone' => fake()->optional()->phoneNumber(),
            'partner_type' => fake()->randomElement(['owner', 'investor', 'manager']),
            'has_salary' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
