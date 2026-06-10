<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTopFiveTest extends TestCase
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

    public function test_dashboard_renders_top_five_for_current_month(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب أ', 'number' => 'B-1']);
        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boat->id, 'start_date' => now()->startOfMonth()->toDateString()]);
        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'trip_id' => $trip->id,
            'total_price' => 80000,
            'net_owner_amount' => 80000,
            'sale_datetime' => now()->toDateTimeString(),
            'status' => 1,
        ]);

        $this->actingAs($owner, 'owner');

        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertViewHas('topFive')
            ->assertSee(__('owner.dashboard.top_five.heading'));
    }
}
