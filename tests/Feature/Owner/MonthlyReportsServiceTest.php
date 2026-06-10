<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\User;
use App\Service\Owner\MonthlyReportsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MonthlyReportsServiceTest extends TestCase
{
    use RefreshDatabase;

    private MonthlyReportsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MonthlyReportsService::class);
    }

    private function fish(string $name): int
    {
        return DB::table('fish')->insertGetId([
            'scientific_name' => $name,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeSale(User $owner, int $tripId, float $total, float $net, string $datetime): Sale
    {
        return Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'trip_id' => $tripId,
            'total_price' => $total,
            'net_owner_amount' => $net,
            'sale_datetime' => $datetime,
            'status' => 1,
        ]);
    }

    public function test_boat_profitability_groups_by_boat(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب أ', 'number' => 'B-1']);

        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boat->id, 'start_date' => '2026-06-05']);

        $this->makeSale($owner, $trip->id, 100000, 90000, '2026-06-10 10:00:00');

        $cat = Category::create(['name_ar' => 'وقود', 'name_en' => 'fuel', 'type' => 'operating', 'status' => 1]);
        Expense::create([
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'category_id' => $cat->id,
            'final_price' => 30000,
            'total_price' => 30000,
            'date' => '2026-06-08',
        ]);

        $rows = $this->service->boatProfitability($owner->id, '2026-06-01', '2026-06-30');

        $this->assertCount(1, $rows);
        $this->assertSame($boat->id, $rows[0]['boat_id']);
        $this->assertSame(90000.0, $rows[0]['net_sales']);
        $this->assertSame(30000.0, $rows[0]['expenses']);
        $this->assertSame(60000.0, $rows[0]['net_profit']);
    }

    public function test_trip_profitability_allocates_expenses_by_window(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-2']);

        $trip = Trip::factory()->create([
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'start_date' => '2026-06-05',
            'end_date' => '2026-06-07',
        ]);

        $this->makeSale($owner, $trip->id, 50000, 45000, '2026-06-12 10:00:00');

        $cat = Category::create(['name_ar' => 'ثلج', 'name_en' => 'ice', 'type' => 'operating', 'status' => 1]);
        // In window -> counted
        Expense::create(['owner_id' => $owner->id, 'boat_id' => $boat->id, 'category_id' => $cat->id, 'final_price' => 5000, 'total_price' => 5000, 'date' => '2026-06-06']);
        // Out of window -> ignored
        Expense::create(['owner_id' => $owner->id, 'boat_id' => $boat->id, 'category_id' => $cat->id, 'final_price' => 9000, 'total_price' => 9000, 'date' => '2026-06-20']);

        $rows = $this->service->tripProfitability($owner->id, '2026-06-01', '2026-06-30');

        $this->assertCount(1, $rows);
        $this->assertSame(45000.0, $rows[0]['net_sales']);
        $this->assertSame(5000.0, $rows[0]['expenses']);
        $this->assertSame(40000.0, $rows[0]['net_profit']);
    }

    public function test_production_by_species_compares_caught_vs_sold(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'start_date' => '2026-06-05']);
        $fishId = $this->fish('Hamour');

        $catchId = DB::table('catch_models')->insertGetId([
            'trip_id' => $trip->id,
            'owner_id' => $owner->id,
            'catch_date' => '2026-06-06 08:00:00',
            'total_weight' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('catch_details')->insert([
            'catch_id' => $catchId,
            'fish_id' => $fishId,
            'fish_name' => 'Hamour',
            'weight' => 100,
            'total_price' => 8000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sale = $this->makeSale($owner, $trip->id, 7000, 6300, '2026-06-10 10:00:00');
        DB::table('sale_details')->insert([
            'sale_id' => $sale->id,
            'fish_id' => $fishId,
            'fish_name' => 'Hamour',
            'weight' => 80,
            'price_per_kilo' => 87.5,
            'total_price' => 7000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rows = $this->service->productionBySpecies($owner->id, '2026-06-01', '2026-06-30');

        $this->assertCount(1, $rows);
        $this->assertSame('Hamour', $rows[0]['fish_name']);
        $this->assertSame(100.0, $rows[0]['caught_weight']);
        $this->assertSame(80.0, $rows[0]['sold_weight']);
        $this->assertSame(7000.0, $rows[0]['sold_value']);
    }

    public function test_expenses_by_category_groups_and_sums(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $fuel = Category::create(['name_ar' => 'وقود', 'name_en' => 'fuel', 'type' => 'operating', 'status' => 1]);
        $gov = Category::create(['name_ar' => 'رسوم', 'name_en' => 'fees', 'type' => 'government', 'status' => 1]);

        Expense::create(['owner_id' => $owner->id, 'category_id' => $fuel->id, 'final_price' => 3000, 'total_price' => 3000, 'date' => '2026-06-05']);
        Expense::create(['owner_id' => $owner->id, 'category_id' => $fuel->id, 'final_price' => 2000, 'total_price' => 2000, 'date' => '2026-06-09']);
        Expense::create(['owner_id' => $owner->id, 'category_id' => $gov->id, 'final_price' => 1000, 'total_price' => 1000, 'date' => '2026-06-10']);

        $rows = $this->service->expensesByCategory($owner->id, '2026-06-01', '2026-06-30');

        $this->assertCount(2, $rows);
        // Sorted by amount desc: fuel (5000) first.
        $this->assertSame('operating', $rows[0]['type']);
        $this->assertSame(5000.0, $rows[0]['amount']);
        $this->assertSame(2, $rows[0]['count']);
        $this->assertSame(1000.0, $rows[1]['amount']);
    }

    public function test_reports_are_owner_scoped(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $other = User::factory()->create(['role' => 'owner']);

        $boat = Boat::create(['owner_id' => $other->id, 'name_ar' => 'غريب', 'number' => 'B-9']);
        $trip = Trip::factory()->create(['owner_id' => $other->id, 'boat_id' => $boat->id, 'start_date' => '2026-06-05']);
        $this->makeSale($other, $trip->id, 100000, 90000, '2026-06-10 10:00:00');

        $this->assertSame([], $this->service->boatProfitability($owner->id, '2026-06-01', '2026-06-30'));
        $this->assertSame([], $this->service->tripProfitability($owner->id, '2026-06-01', '2026-06-30'));
        $this->assertSame([], $this->service->productionBySpecies($owner->id, '2026-06-01', '2026-06-30'));
    }
}
