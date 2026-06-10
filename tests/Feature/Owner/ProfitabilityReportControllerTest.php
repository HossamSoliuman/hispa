<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfitabilityReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        // mcamara localization redirects non-prefixed GETs in tests; bypass it so
        // these tests exercise the real controller + view, not the locale redirect.
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    private function owner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        return $owner;
    }

    public function test_trip_profitability_screen_loads_for_owner(): void
    {
        $owner = $this->owner();
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-1']);
        Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boat->id, 'start_date' => now()->toDateString()]);

        $this->actingAs($owner, 'owner');

        $this->get(route('owner.reports.trip-profitability'))
            ->assertOk()
            ->assertViewIs('owner.report.trip_profitability');
    }

    public function test_boat_profitability_screen_loads_for_owner(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.reports.boat-profitability'))
            ->assertOk()
            ->assertViewIs('owner.report.boat_profitability');
    }

    public function test_production_species_screen_loads_for_owner(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.reports.production-species'))
            ->assertOk()
            ->assertViewIs('owner.report.production_species');
    }

    public function test_reports_hub_loads_for_owner(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.reports.hub'))
            ->assertOk()
            ->assertViewIs('owner.report.hub')
            ->assertSee(__('owner.analysis_reports.hub_title'));
    }

    public function test_expenses_by_category_screen_loads_for_owner(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.reports.expenses-by-category'))
            ->assertOk()
            ->assertViewIs('owner.report.expenses_by_category');
    }
}
