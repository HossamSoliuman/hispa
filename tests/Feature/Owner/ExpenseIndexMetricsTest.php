<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Repository\Owner\ExpenseRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseIndexMetricsTest extends TestCase
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

    private function expense(User $owner, ?int $categoryId, float $amount): Expense
    {
        return Expense::create([
            'date' => now()->toDateString(),
            'number' => 'EXP-'.$owner->id.'-'.uniqid(),
            'owner_id' => $owner->id,
            'category_id' => $categoryId,
            'total_price' => $amount,
            'final_price' => $amount,
            'status' => 'paid',
        ]);
    }

    /**
     * Regression: now that `categories` carries an `owner_id`, the `allExpenses`
     * hasManyThrough joins `expenses` ⋈ `categories` — both with an `owner_id`
     * column. The Expense owner scope must qualify its column or SQLite/MySQL
     * raise "ambiguous column name: owner_id" when building the index metrics.
     */
    public function test_index_metrics_resolves_category_rollup_and_is_owner_scoped(): void
    {
        $owner = $this->owner();
        $other = $this->owner();

        $parent = Category::create([
            'name_ar' => 'مصروفات', 'name_en' => 'Expenses',
            'type' => 'general', 'status' => 1, 'parent_id' => null, 'owner_id' => $owner->id,
        ]);
        $child = Category::create([
            'name_ar' => 'وقود', 'name_en' => 'Fuel',
            'type' => 'general', 'status' => 1, 'parent_id' => $parent->id, 'owner_id' => $owner->id,
        ]);

        // One expense under the owner's child category (exercises allExpenses).
        $this->expense($owner, $child->id, 100);
        // A different owner's expense must not leak into the metrics.
        $this->expense($other, null, 999);

        $this->actingAs($owner, 'owner');

        $metrics = app(ExpenseRepository::class)->indexMetrics();

        // The owner only sees their own expense totals.
        $this->assertSame(1, $metrics['count']);
        $this->assertEqualsWithDelta(100, (float) $metrics['totalAmount'], 0.001);

        // The parent rolls up its child's expense via the hasManyThrough.
        $rollup = $metrics['categoriesRate']->firstWhere('id', $parent->id);
        $this->assertNotNull($rollup);
        $this->assertSame(1, (int) $rollup->expenses_count);
        $this->assertEqualsWithDelta(100, (float) $rollup->total_amount, 0.001);
    }
}
