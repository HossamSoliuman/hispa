<?php

namespace Tests\Feature\Owner;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardPendingExpensesTest extends TestCase
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

    public function test_dashboard_lists_only_the_authenticated_owners_pending_expenses(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        $otherOwner = User::factory()->create(['role' => 'owner']);
        $otherOwner->assignRole('owner');

        $this->expense($owner, 'EXP-PENDING-001', 'pending', 125.50);
        $this->expense($owner, 'EXP-PENDING-002', 'pending', 75.00);
        $this->expense($owner, 'EXP-PAID-001', 'paid', 900.00);
        $this->expense($otherOwner, 'EXP-OTHER-001', 'pending', 700.00);

        $response = $this->actingAs($owner, 'owner')->get(route('owner.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas('pendingExpenses', fn ($expenses): bool => $expenses->pluck('number')->all() === [
                'EXP-PENDING-001',
                'EXP-PENDING-002',
            ])
            ->assertViewHas('pendingExpensesSummary', fn (array $summary): bool => $summary['count'] === 2
                && abs($summary['amount'] - 200.50) < 0.001)
            ->assertSee(__('owner.dashboard.pending_expenses.title'))
            ->assertSee('EXP-PENDING-001')
            ->assertSee('EXP-PENDING-002')
            ->assertDontSee('EXP-PAID-001')
            ->assertDontSee('EXP-OTHER-001')
            ->assertSee(route('owner.expenses.index', ['status' => 'pending']), false);
    }

    private function expense(User $owner, string $number, string $status, float $amount): Expense
    {
        return Expense::create([
            'date' => now()->toDateString(),
            'number' => $number,
            'owner_id' => $owner->id,
            'total_price' => $amount,
            'final_price' => $amount,
            'status' => $status,
        ]);
    }
}
