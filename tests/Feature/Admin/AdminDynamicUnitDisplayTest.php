<?php

namespace Tests\Feature\Admin;

use App\DataTable\Owner\StockDataTable;
use App\DataTable\Report\StockReportDataTable;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Trip;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminDynamicUnitDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_weight_totals_keep_each_stored_unit_separate(): void
    {
        $kilogram = (object) ['name' => 'kg'];
        $box = (object) ['name' => 'Box'];

        $display = formatWeightByUnit([
            (object) ['weight' => 10, 'unit' => $kilogram],
            (object) ['weight' => 3, 'unit' => $box],
            (object) ['weight' => 2.5, 'unit' => $kilogram],
        ]);

        $this->assertSame('12.50 kg + 3.00 Box', $display);
    }

    public function test_riyal_icon_component_renders_an_accessible_svg(): void
    {
        $icon = view('components.riyal-icon', ['size' => 'sm'])->render();

        $this->assertStringContainsString('<svg', $icon);
        $this->assertStringContainsString('aria-hidden="true"', $icon);
    }

    public function test_admin_stock_data_uses_the_unit_stored_with_each_stock_row(): void
    {
        app()->setLocale('en');

        $owner = User::factory()->create(['role' => 'owner']);
        $fish = Fish::create([
            'name_ar' => 'سمك اختبار',
            'name_en' => 'Test Fish',
            'owner_id' => $owner->id,
            'status' => true,
        ]);
        $kilogram = Unit::withoutGlobalScopes()->create([
            'name_ar' => 'كجم',
            'name_en' => 'kg',
            'owner_id' => $owner->id,
            'status' => true,
        ]);
        $box = Unit::withoutGlobalScopes()->create([
            'name_ar' => 'صندوق',
            'name_en' => 'Box',
            'owner_id' => $owner->id,
            'status' => true,
        ]);

        FishQuantityStock::create(['fish_id' => $fish->id, 'unit_id' => $kilogram->id, 'quantity' => 10]);
        FishQuantityStock::create(['fish_id' => $fish->id, 'unit_id' => $box->id, 'quantity' => 3]);

        $response = app(StockDataTable::class)->getData(Request::create('/', 'GET', [], [], [], [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]));
        $payload = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('10.00 kg + 3.00 Box', $payload['total_weight']);
        $this->assertSame(['Box', 'kg'], collect($payload['data'])->pluck('unit')->sort()->values()->all());
    }

    public function test_admin_stock_report_uses_landed_catch_weights_and_their_stored_units(): void
    {
        app()->setLocale('en');

        $owner = User::factory()->create(['role' => 'owner']);
        $captain = User::factory()->create(['role' => 'captain']);
        $trip = Trip::factory()->create([
            'owner_id' => $owner->id,
            'captain_id' => $captain->id,
        ]);
        $fish = Fish::create([
            'name_ar' => 'ط³ظ…ظƒ ط§ط®طھط¨ط§ط±',
            'name_en' => 'Test Fish',
            'owner_id' => $owner->id,
            'status' => true,
        ]);
        $box = Unit::withoutGlobalScopes()->create([
            'name_ar' => 'طµظ†ط¯ظˆظ‚',
            'name_en' => 'Box',
            'owner_id' => $owner->id,
            'status' => true,
        ]);
        $catch = CatchModel::create([
            'trip_id' => $trip->id,
            'owner_id' => $owner->id,
            'catch_date' => now(),
        ]);

        CatchDetail::create([
            'catch_id' => $catch->id,
            'fish_id' => $fish->id,
            'unit_id' => $box->id,
            'weight' => 12.5,
        ]);
        FishQuantityStock::create([
            'fish_id' => $fish->id,
            'unit_id' => $box->id,
            'catch_id' => $catch->id,
            'trip_id' => $trip->id,
            'quantity' => 0,
        ]);

        $response = app(StockReportDataTable::class)->getData(Request::create('/', 'GET', [], [], [], [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]));
        $payload = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('12.50 Box', $payload['data'][0]['weight_captain']);
        $this->assertSame('12.50 Box', $payload['data'][0]['total_weight']);
        $this->assertSame('12.50 Box', $payload['totalWeight']);
    }

    public function test_admin_stock_report_headers_do_not_assume_kilograms(): void
    {
        $template = file_get_contents(resource_path('views/admin/report/stock.blade.php'));

        $this->assertStringContainsString("__('admin.report.stock.total_weight')", $template);
        $this->assertStringContainsString("__('admin.report.stock.difference')", $template);
        $this->assertStringNotContainsString("__('admin.report.stock.total_kg')", $template);
        $this->assertStringNotContainsString("__('admin.report.stock.diff_kg')", $template);
    }
}
