<?php

namespace Database\Factories\Startup;

use App\Models\Startup\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup\LoanPayment>
 */
class LoanPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'owner_id' => fn (array $attributes): int => Loan::withoutGlobalScopes()->findOrFail($attributes['loan_id'])->owner_id,
            'date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'payer_type' => 'project',
            'partner_id' => null,
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'card']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
