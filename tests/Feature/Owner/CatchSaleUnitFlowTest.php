<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\CatchModel;
use App\Models\Customer;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\PaymentMethod;
use App\Models\Trip;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatchSaleUnitFlowTest extends TestCase
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

    private function owner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        // Each owner controls its own master data (units, fish, payment methods, ...).
        app(\App\Services\Owner\OwnerMasterDataService::class)->seedFor($owner);

        return $owner;
    }

    private function fish(User $owner): Fish
    {
        return Fish::create(['scientific_name' => 'Test Fish', 'status' => 1, 'owner_id' => $owner->id]);
    }

    public function test_catch_stores_separate_stock_rows_per_unit(): void
    {
        $owner = $this->owner();
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-1']);
        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boat->id]);
        $fish = $this->fish($owner);

        $kg = Unit::where('is_default', 1)->firstOrFail();
        $box = Unit::where('name_en', 'Box')->firstOrFail();

        $this->actingAs($owner, 'owner');

        $this->post(route('owner.catch.store'), [
            'trip_id' => $trip->id,
            'fish_id' => [$fish->id, $fish->id],
            'unit_id' => [$kg->id, $box->id],
            'weight' => [10, 3],
        ])->assertRedirect(route('owner.catch.index'));

        $catch = CatchModel::where('trip_id', $trip->id)->firstOrFail();

        // The same fish caught in two units must produce two distinct stock rows.
        $this->assertEqualsWithDelta(10, (float) FishQuantityStock::where('catch_id', $catch->id)
            ->where('fish_id', $fish->id)->where('unit_id', $kg->id)->value('quantity'), 0.001);
        $this->assertEqualsWithDelta(3, (float) FishQuantityStock::where('catch_id', $catch->id)
            ->where('fish_id', $fish->id)->where('unit_id', $box->id)->value('quantity'), 0.001);

        $this->assertDatabaseHas('catch_details', [
            'catch_id' => $catch->id, 'fish_id' => $fish->id, 'unit_id' => $kg->id, 'weight' => 10,
        ]);
        $this->assertDatabaseHas('catch_details', [
            'catch_id' => $catch->id, 'fish_id' => $fish->id, 'unit_id' => $box->id, 'weight' => 3,
        ]);
    }

    public function test_sale_decrements_only_the_selected_unit_stock(): void
    {
        $owner = $this->owner();
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-2']);
        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boat->id]);
        $fish = $this->fish($owner);
        $customer = Customer::create(['name' => 'عميل', 'status' => 1, 'owner_id' => $owner->id]);
        $paymentMethod = PaymentMethod::create(['name' => 'كاش', 'status' => 1, 'owner_id' => $owner->id]);

        $kg = Unit::where('is_default', 1)->firstOrFail();
        $box = Unit::where('name_en', 'Box')->firstOrFail();

        $catch = CatchModel::create([
            'trip_id' => $trip->id,
            'owner_id' => $owner->id,
            'catch_date' => now(),
            'total_weight' => 13,
            'total_amount' => 0,
        ]);

        foreach ([[$kg->id, 10], [$box->id, 3]] as [$unitId, $qty]) {
            FishQuantityStock::create([
                'fish_id' => $fish->id,
                'unit_id' => $unitId,
                'catch_id' => $catch->id,
                'trip_id' => $trip->id,
                'boat_id' => $boat->id,
                'quantity' => $qty,
            ]);
        }

        $this->actingAs($owner, 'owner');

        $this->post(route('owner.sales.store'), [
            'customer_id' => $customer->id,
            'trip_id' => $trip->id,
            'payment_method_id' => $paymentMethod->id,
            'payment_status' => 'unpaid',
            'sale_datetime' => now()->format('Y-m-d\TH:i'),
            'fish_id' => [$fish->id],
            'unit_id' => [$box->id],
            'weight' => [2],
            'price_per_kilo' => [50],
        ])->assertRedirect(route('owner.sales.index'));

        // Selling 2 boxes must only touch the box stock, never the kg stock.
        $this->assertEqualsWithDelta(1, (float) FishQuantityStock::where('catch_id', $catch->id)
            ->where('unit_id', $box->id)->value('quantity'), 0.001);
        $this->assertEqualsWithDelta(10, (float) FishQuantityStock::where('catch_id', $catch->id)
            ->where('unit_id', $kg->id)->value('quantity'), 0.001);

        $this->assertDatabaseHas('sale_details', [
            'fish_id' => $fish->id, 'unit_id' => $box->id, 'weight' => 2,
        ]);
    }

    public function test_sale_stamps_price_onto_catch_details(): void
    {
        $owner = $this->owner();
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-3']);
        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boat->id]);
        $fish = $this->fish($owner);
        $customer = Customer::create(['name' => 'عميل', 'status' => 1, 'owner_id' => $owner->id]);
        $paymentMethod = PaymentMethod::create(['name' => 'كاش', 'status' => 1, 'owner_id' => $owner->id]);

        $kg = Unit::where('is_default', 1)->firstOrFail();

        $this->actingAs($owner, 'owner');

        $this->post(route('owner.catch.store'), [
            'trip_id' => $trip->id,
            'fish_id' => [$fish->id],
            'unit_id' => [$kg->id],
            'weight' => [10],
        ])->assertRedirect(route('owner.catch.index'));

        $catch = CatchModel::where('trip_id', $trip->id)->firstOrFail();

        // Catch detail starts with no price (the create form no longer collects it).
        $this->assertEqualsWithDelta(0, (float) $catch->details()->value('price_per_kg'), 0.001);

        $this->post(route('owner.sales.store'), [
            'customer_id' => $customer->id,
            'trip_id' => $trip->id,
            'payment_method_id' => $paymentMethod->id,
            'payment_status' => 'unpaid',
            'sale_datetime' => now()->format('Y-m-d\TH:i'),
            'fish_id' => [$fish->id],
            'unit_id' => [$kg->id],
            'weight' => [4],
            'price_per_kilo' => [50],
        ])->assertRedirect(route('owner.sales.index'));

        // Selling stamps the catch detail: price per kg from the sale,
        // total valued at the full caught weight (10 * 50).
        $this->assertDatabaseHas('catch_details', [
            'catch_id' => $catch->id,
            'fish_id' => $fish->id,
            'unit_id' => $kg->id,
            'price_per_kg' => 50,
            'total_price' => 500,
        ]);
    }

    public function test_units_index_redirects_to_settings_tab(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        // Units management now lives as a tab inside system settings.
        $this->get(route('owner.units.index'))
            ->assertRedirect(route('owner.settings.index', ['tab' => 'units']));
    }

    public function test_units_tab_renders_in_settings(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.settings.index', ['tab' => 'units']))
            ->assertOk()
            ->assertViewIs('owner.settings.index')
            ->assertSee(__('owner.units.title'));
    }

    public function test_catch_create_page_renders_unit_dropdown(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.catch.create'))
            ->assertOk()
            ->assertViewIs('owner.catch.create')
            ->assertSee('name="unit_id[]"', false);
    }

    public function test_units_store_enforces_single_default(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $kg = Unit::where('is_default', 1)->firstOrFail();

        $this->post(route('owner.units.store'), [
            'name_ar' => 'طن',
            'name_en' => 'Ton',
            'status' => 1,
            'is_default' => 1,
        ])->assertOk();

        $this->assertFalse((bool) $kg->fresh()->is_default);
        $this->assertSame(1, Unit::where('is_default', 1)->count());
        $this->assertDatabaseHas('units', ['name_ar' => 'طن', 'is_default' => 1]);
    }
}
