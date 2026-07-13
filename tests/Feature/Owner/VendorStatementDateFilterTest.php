<?php

namespace Tests\Feature\Owner;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorStatementDateFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_vendor_statement_filters_expenses_by_input_date(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'owner_id' => $owner->id,
        ]);

        $includedExpense = $this->createExpense(
            owner: $owner,
            vendor: $vendor,
            date: '2026-01-15',
            createdAt: '2026-02-15 12:00:00',
        );
        $this->createExpense(
            owner: $owner,
            vendor: $vendor,
            date: '2026-02-15',
            createdAt: '2026-01-15 12:00:00',
        );

        $response = $this->actingAs($owner, 'owner')->get(route('owner.reports.vendor-statement', [
            'vendor_id' => $vendor->id,
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ]));

        $response->assertOk();
        $response->assertViewHas('expenses', function (Collection $expenses) use ($includedExpense): bool {
            return $expenses->pluck('id')->all() === [$includedExpense->id];
        });
    }

    private function createExpense(User $owner, User $vendor, string $date, string $createdAt): Expense
    {
        $expense = Expense::create([
            'date' => $date,
            'number' => fake()->unique()->numerify('EXP-#####'),
            'owner_id' => $owner->id,
            'vendor_id' => $vendor->id,
            'total_price' => 100,
            'final_price' => 100,
            'status' => 'pending',
        ]);

        $expense->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->saveQuietly();

        return $expense;
    }
}
