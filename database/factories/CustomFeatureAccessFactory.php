<?php

namespace Database\Factories;

use App\Enums\CustomFeature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomFeatureAccess>
 */
class CustomFeatureAccessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'owner']),
            'feature' => CustomFeature::BusinessStartup,
            'status' => 'active',
            'paused_at' => null,
            'granted_by_admin_id' => null,
        ];
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }
}
