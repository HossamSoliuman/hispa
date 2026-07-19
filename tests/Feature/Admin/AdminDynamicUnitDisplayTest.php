<?php

namespace Tests\Feature\Admin;

use App\DataTable\Owner\StockDataTable;
use App\Models\Fish;
use App\Models\FishQuantityStock;
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
}
