<?php

namespace Tests\Feature\Owner;

use App\Models\Expense;
use App\Models\MonthClosing;
use App\Models\User;
use App\Repository\Owner\ExpenseRepository;
use App\Service\Owner\MonthClosingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClosedMonthListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    private function owner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        app(\App\Services\Owner\OwnerMasterDataService::class)->seedFor($owner);

        return $owner;
    }

    private function expenseOn(User $owner, Carbon $date, float $amount): Expense
    {
        return Expense::create([
            'date' => $date->toDateString(),
            'number' => 'EXP-'.$owner->id.'-'.uniqid(),
            'owner_id' => $owner->id,
            'category_id' => null,
            'total_price' => $amount,
            'final_price' => $amount,
            'status' => 'paid',
        ]);
    }

    private function closeMonth(User $owner, Carbon $month): void
    {
        MonthClosing::create([
            'owner_id' => $owner->id,
            'year' => (int) $month->year,
            'month' => (int) $month->month,
            'boat_id' => null,
            'status' => 'closed',
        ]);
    }

    public function test_closed_months_helper_returns_distinct_periods(): void
    {
        $owner = $this->owner();
        $may = Carbon::create(2026, 5, 1);
        $june = Carbon::create(2026, 6, 1);

        $this->closeMonth($owner, $may);
        $this->closeMonth($owner, $june);
        // A non-closed (draft) record must be ignored.
        MonthClosing::create([
            'owner_id' => $owner->id, 'year' => 2026, 'month' => 7, 'boat_id' => null, 'status' => 'draft',
        ]);

        $months = app(MonthClosingService::class)->closedMonths($owner->id);

        $this->assertCount(2, $months);
        $this->assertEqualsCanonicalizing(
            [['year' => 2026, 'month' => 5], ['year' => 2026, 'month' => 6]],
            $months,
        );
    }

    public function test_index_metrics_excludes_closed_months_by_default(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $open = Carbon::create(2026, 7, 10);
        $closed = Carbon::create(2026, 6, 10);

        $this->expenseOn($owner, $open, 100);
        $this->expenseOn($owner, $closed, 500);

        // Nothing closed yet: both months feed the stat cards.
        $metrics = app(ExpenseRepository::class)->indexMetrics();
        $this->assertSame(2, $metrics['count']);
        $this->assertEqualsWithDelta(600, (float) $metrics['totalAmount'], 0.001);

        $this->closeMonth($owner, $closed);

        // After closing June, only the open month remains on the cards.
        $metrics = app(ExpenseRepository::class)->indexMetrics();
        $this->assertSame(1, $metrics['count']);
        $this->assertEqualsWithDelta(100, (float) $metrics['totalAmount'], 0.001);
    }

    public function test_datatable_query_hides_closed_months_but_a_filter_reveals_them(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $open = Carbon::create(2026, 7, 10);
        $closed = Carbon::create(2026, 6, 10);

        $this->expenseOn($owner, $open, 100);
        $this->expenseOn($owner, $closed, 500);
        $this->closeMonth($owner, $closed);

        $repository = app(ExpenseRepository::class);

        // Default listing drops the closed month.
        $unfiltered = $repository->expensesQueryForDataTable(Request::create('/', 'GET'));
        $this->assertSame(1, $unfiltered->count());

        // Any filter lifts the restriction so a closed month can be inspected.
        $filtered = $repository->expensesQueryForDataTable(
            Request::create('/', 'GET', ['from_date' => $closed->copy()->startOfMonth()->toDateString()]),
        );
        $this->assertSame(2, $filtered->count());
    }
}
