<?php

namespace Tests\Feature\Owner;

use App\Models\Asset;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\User;
use App\Service\Owner\MonthClosingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthClosingServiceTest extends TestCase
{
    use RefreshDatabase;

    private MonthClosingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MonthClosingService::class);
    }

    private function seedClientExample(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);

        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 300000,
            'net_owner_amount' => 300000,
            'sale_datetime' => '2026-06-15 10:00:00',
            'status' => 1,
        ]);

        foreach ([['operating', 40000], ['general', 20000]] as [$type, $amount]) {
            $category = Category::create(['name_ar' => $type, 'name_en' => $type, 'type' => $type, 'status' => 1]);
            Expense::create([
                'owner_id' => $owner->id,
                'category_id' => $category->id,
                'final_price' => $amount,
                'total_price' => $amount,
                'date' => '2026-06-10',
            ]);
        }

        // Captain (2 shares), assistant (1.5), eight sailors (1 each) => Σ = 11.5
        User::factory()->create(['role' => 'captain', 'owner_id' => $owner->id, 'salary_type' => 'percentage', 'profit_shares' => 2.0]);
        User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'salary_type' => 'percentage', 'profit_shares' => 1.5]);
        for ($i = 0; $i < 8; $i++) {
            User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'salary_type' => 'percentage', 'profit_shares' => 1.0]);
        }

        return $owner;
    }

    public function test_preview_matches_client_distribution(): void
    {
        $owner = $this->seedClientExample();

        $preview = $this->service->preview($owner->id, 2026, 6);

        $this->assertSame(240000.0, $preview['financials']['net_profit']);
        $this->assertSame(120000.0, $preview['financials']['crew_share']);
        $this->assertSame(11.5, $preview['total_shares']);
        $this->assertSame(10434.78, $preview['share_value']);

        $captainDue = collect($preview['dues'])->firstWhere('role', 'captain');
        $this->assertSame(20869.56, $captainDue['due_amount']);
    }

    public function test_custom_percentage_member_takes_cut_off_the_top(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        // net profit 100,000 => crew pool 50,000.
        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 100000,
            'net_owner_amount' => 100000,
            'sale_datetime' => '2026-06-15 10:00:00',
            'status' => 1,
        ]);

        // Captain on a custom 40% of the crew pool, plus 4 equal-share sailors.
        $captain = User::factory()->create([
            'role' => 'captain', 'owner_id' => $owner->id,
            'salary_type' => 'percentage', 'custom_share_percent' => 40.0,
        ]);
        for ($i = 0; $i < 4; $i++) {
            User::factory()->create([
                'role' => 'crew', 'owner_id' => $owner->id,
                'salary_type' => 'percentage', 'profit_shares' => 1.0,
            ]);
        }

        $preview = $this->service->preview($owner->id, 2026, 6);

        $this->assertSame(50000.0, $preview['financials']['crew_share']);

        // Captain: 40% of 50,000 = 20,000. Remaining 30,000 / 4 = 7,500 each.
        $captainDue = collect($preview['dues'])->firstWhere('user_id', $captain->id);
        $this->assertSame(20000.0, $captainDue['due_amount']);
        $this->assertSame(40.0, (float) $captainDue['custom_share_percent']);

        $sailorDue = collect($preview['dues'])->firstWhere('role', 'crew');
        $this->assertSame(7500.0, $sailorDue['due_amount']);
        $this->assertNull($sailorDue['custom_share_percent']);

        // Whole crew pool is fully distributed.
        $this->assertSame(50000.0, round(collect($preview['dues'])->sum('due_amount'), 2));
    }

    public function test_close_persists_frozen_snapshot(): void
    {
        $owner = $this->seedClientExample();

        $closing = $this->service->close($owner->id, 2026, 6);

        $this->assertDatabaseHas('month_closings', [
            'owner_id' => $owner->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'closed',
        ]);
        $this->assertSame('240000.00', $closing->net_profit);
        $this->assertSame('120000.00', $closing->crew_share);
        $this->assertCount(10, $closing->dues);

        // Snapshot must not change when sales are later edited.
        Sale::where('seller_id', $owner->id)->update(['total_price' => 999, 'net_owner_amount' => 999]);
        $this->assertSame('240000.00', $closing->fresh()->net_profit);
    }

    public function test_close_scopes_figures_and_crew_to_the_selected_boat(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $boatA = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب أ', 'number' => 'B-A']);
        $boatB = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب ب', 'number' => 'B-B']);

        $tripA = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boatA->id, 'start_date' => '2026-06-01']);
        $tripB = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boatB->id, 'start_date' => '2026-06-01']);

        foreach ([[$tripA, 100000], [$tripB, 40000]] as [$trip, $amount]) {
            Sale::create([
                'number' => 'S-'.uniqid(),
                'seller_type' => 'owner',
                'seller_id' => $owner->id,
                'trip_id' => $trip->id,
                'total_price' => $amount,
                'net_owner_amount' => $amount,
                'sale_datetime' => '2026-06-15 10:00:00',
                'status' => 1,
            ]);
        }

        User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'boat_id' => $boatA->id, 'salary_type' => 'percentage', 'profit_shares' => 1.0]);
        User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'boat_id' => $boatB->id, 'salary_type' => 'percentage', 'profit_shares' => 1.0]);

        $closing = $this->service->close($owner->id, 2026, 6, null, $boatA->id);

        $this->assertSame($boatA->id, $closing->boat_id);
        $this->assertSame('100000.00', $closing->net_sales); // only boat A's sale
        $this->assertCount(1, $closing->dues); // only boat A's crew

        // The same boat cannot be closed twice, but other scopes remain open.
        $this->assertNotNull($this->service->find($owner->id, 2026, 6, $boatA->id));
        $this->assertNull($this->service->find($owner->id, 2026, 6, $boatB->id));
        $this->assertNull($this->service->find($owner->id, 2026, 6)); // whole-fleet scope untouched

        $fleetClosing = $this->service->close($owner->id, 2026, 6, null, null);
        $this->assertNull($fleetClosing->boat_id);
        $this->assertSame('140000.00', $fleetClosing->net_sales); // both boats
    }

    public function test_close_deducts_straight_line_depreciation_before_distribution(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 100000,
            'net_owner_amount' => 100000,
            'sale_datetime' => '2026-06-15 10:00:00',
            'status' => 1,
        ]);

        // Boat 60,000 − 6,000 salvage ÷ 12 years ÷ 12 = 375/month.
        Asset::create([
            'owner_id' => $owner->id,
            'asset_type' => 'boat',
            'name' => 'قارب',
            'purchase_date' => '2025-01-01',
            'purchase_cost' => 60000,
            'salvage_value' => 6000,
            'depreciation_method' => 'straight_line',
            'useful_life_years' => 12,
            'status' => 'active',
        ]);

        User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'salary_type' => 'percentage', 'profit_shares' => 1.0]);

        $preview = $this->service->preview($owner->id, 2026, 6);

        $this->assertSame(375.0, $preview['asset_depreciation']['total']);
        $this->assertSame(375.0, $preview['financials']['depreciation']);
        $this->assertSame(99625.0, $preview['financials']['net_profit']); // 100,000 − 375

        $closing = $this->service->close($owner->id, 2026, 6);

        $this->assertSame('375.00', $closing->depreciation);
        $this->assertSame('99625.00', $closing->net_profit);
        $this->assertCount(1, $closing->asset_depreciation_breakdown);
        $this->assertSame('قارب', $closing->asset_depreciation_breakdown[0]['name']);
        $this->assertSame(375.0, (float) $closing->asset_depreciation_breakdown[0]['monthly']);
    }

    public function test_depreciation_stops_after_useful_life_ends(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 100000,
            'net_owner_amount' => 100000,
            'sale_datetime' => '2026-06-15 10:00:00',
            'status' => 1,
        ]);

        // Fully written off by 2026-06: 5-year life from 2018.
        Asset::create([
            'owner_id' => $owner->id,
            'asset_type' => 'other',
            'name' => 'معدة قديمة',
            'purchase_date' => '2018-01-01',
            'purchase_cost' => 60000,
            'salvage_value' => 0,
            'depreciation_method' => 'straight_line',
            'useful_life_years' => 5,
            'status' => 'active',
        ]);

        $preview = $this->service->preview($owner->id, 2026, 6);

        $this->assertSame(0.0, $preview['asset_depreciation']['total']);
        $this->assertSame(100000.0, $preview['financials']['net_profit']);
    }

    public function test_cannot_close_same_boat_twice(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-1']);

        $this->service->close($owner->id, 2026, 6, null, $boat->id);

        $this->expectException(\DomainException::class);
        $this->service->close($owner->id, 2026, 6, null, $boat->id);
    }

    public function test_cannot_close_twice(): void
    {
        $owner = $this->seedClientExample();
        $this->service->close($owner->id, 2026, 6);

        $this->expectException(\DomainException::class);
        $this->service->close($owner->id, 2026, 6);
    }

    public function test_reopen_blocked_after_payment(): void
    {
        $owner = $this->seedClientExample();
        $closing = $this->service->close($owner->id, 2026, 6);

        $closing->dues()->first()->update(['paid_amount' => 100]);

        $this->expectException(\DomainException::class);
        $this->service->reopen($closing);
    }

    public function test_reopen_allowed_without_payments(): void
    {
        $owner = $this->seedClientExample();
        $closing = $this->service->close($owner->id, 2026, 6);

        $this->service->reopen($closing);

        $this->assertDatabaseMissing('month_closings', ['id' => $closing->id]);
        $this->assertNull($this->service->find($owner->id, 2026, 6));
    }

    public function test_reopen_blocked_after_linked_percentage_payment(): void
    {
        $owner = $this->seedClientExample();
        $closing = $this->service->close($owner->id, 2026, 6);
        $captain = User::where('owner_id', $owner->id)->where('role', 'captain')->firstOrFail();

        // No stored due payment, but the crew were paid via the percentage payroll.
        $payroll = PayrollModel::create([
            'owner_id' => $owner->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'approved',
            'type' => 'percentage',
        ]);
        PayrollDetailsModel::create([
            'payroll_id' => $payroll->id,
            'user_id' => $captain->id,
            'base_salary' => 0,
            'percentage' => 0,
            'sales_amount' => 0,
            'final_salary' => 5000,
            'is_paid' => true,
            'paid_at' => now(),
            'paid_amount' => 5000,
        ]);

        $this->expectException(\DomainException::class);
        $this->service->reopen($closing);
    }
}
