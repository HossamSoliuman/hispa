<?php

namespace Database\Factories;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'name_en' => fake()->words(2, true),
            'number' => 'TRIP-'.fake()->unique()->numerify('######'),
            'license_number' => 'LIC-'.fake()->unique()->numerify('######'),
            'status' => TripStatus::New,
            'owner_id' => User::factory()->create(['role' => 'owner'])->id,
            'captain_id' => User::factory()->create(['role' => 'captain'])->id,
            'boat_name' => fake()->word(),
            'boat_number' => fake()->numerify('BT-###'),
        ];
    }

    public function asNew(): static
    {
        return $this->state(['status' => TripStatus::New]);
    }

    public function running(): static
    {
        return $this->state(['status' => TripStatus::InProgress]);
    }

    public function sold(): static
    {
        return $this->state(['status' => TripStatus::Sold]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => TripStatus::Cancelled]);
    }
}
