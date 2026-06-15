<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\User;
use App\Service\Owner\MonthlyFinancialsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyFinancialsServiceTest extends TestCase
{
    use RefreshDatabase;

    private MonthlyFinancialsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MonthlyFinancialsService;
    }

    private function makeOwner(): User
    {
        return User::factory()->create(['role' => 'owner']);
    }

    private function makeSale(User $owner, float $total, float $netOwner, string $datetime): Sale
    {
        return Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => $total,
            'net_owner_amount' => $netOwner,
            'sale_datetime' => $datetime,
            'status' => 1,
        ]);
    }

    private function makeExpense(User $owner, string $type, float $finalPrice, string $date): Expense
    {
        $category = Category::create([
            'name_ar' => "فئة {$type}",
            'name_en' => $type,
            'type' => $type,
            'status' => 1,
        ]);

        return Expense::create([
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'final_price' => $finalPrice,
            'total_price' => $finalPrice,
            'date' => $date,
        ]);
    }

    public function test_client_example_waterfall(): void
    {
        $owner = $this->makeOwner();
        $this->makeSale($owner, 300000, 300000, '2026-06-15 10:00:00');
        $this->makeExpense($owner, 'operating', 40000, '2026-06-10');
        $this->makeExpense($owner, 'general', 20000, '2026-06-12');

        $result = $this->service->compute($owner->id, '2026-06-01', '2026-06-30');

        $this->assertSame(300000.0, $result['gross_sales']);
        $this->assertSame(300000.0, $result['net_owner_revenue']);
        $this->assertSame(60000.0, $result['total_expenses']);
        $this->assertSame(240000.0, $result['net_profit']);
        $this->assertSame(50.0, $result['owner_percent']);
        $this->assertSame(120000.0, $result['owner_share']);
        $this->assertSame(120000.0, $result['crew_share']);
    }

    public function test_crew_pool_distribution_by_shares(): void
    {
        // Captain 2, assistant 1.5, eight sailors at 1.0 each => Σ = 11.5
        $shares = [
            'captain' => 2.0,
            'assistant' => 1.5,
        ];
        for ($i = 1; $i <= 8; $i++) {
            $shares["sailor_{$i}"] = 1.0;
        }

        $distribution = $this->service->distributeCrewPool(120000, $shares);

        $this->assertSame(11.5, $distribution['total_shares']);
        $this->assertSame(10434.78, $distribution['share_value']);
        $this->assertSame(20869.56, $distribution['dues']['captain']);
        $this->assertSame(15652.17, $distribution['dues']['assistant']);
        $this->assertSame(10434.78, $distribution['dues']['sailor_1']);
    }

    public function test_equal_split_is_shares_of_one(): void
    {
        $distribution = $this->service->distributeCrewPool(120000, [
            'a' => 1.0, 'b' => 1.0, 'c' => 1.0,
        ]);

        $this->assertSame(40000.0, $distribution['dues']['a']);
        $this->assertSame(40000.0, $distribution['dues']['b']);
    }

    public function test_tenancy_isolation(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        $this->makeSale($ownerA, 100000, 100000, '2026-06-15 10:00:00');
        $this->makeSale($ownerB, 777, 777, '2026-06-15 10:00:00');

        $result = $this->service->compute($ownerA->id, '2026-06-01', '2026-06-30');

        $this->assertSame(100000.0, $result['gross_sales']);
    }

    public function test_last_day_boundary_is_inclusive(): void
    {
        $owner = $this->makeOwner();
        $this->makeSale($owner, 5000, 5000, '2026-06-30 23:59:59');

        $result = $this->service->compute($owner->id, '2026-06-01', '2026-06-30');

        $this->assertSame(5000.0, $result['gross_sales']);
    }
}
