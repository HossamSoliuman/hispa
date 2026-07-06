<?php

namespace Tests\Feature\Owner;

use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Fish;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use App\Service\Owner\MonthClosingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnnualSummaryControllerTest extends TestCase
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

    private function makeOwnerWithClosedYear(int $year): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        User::factory()->create([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'salary_type' => 'percentage',
            'profit_shares' => 1.0,
        ]);

        foreach ([3, 6] as $month) {
            Sale::create([
                'number' => 'S-'.uniqid(),
                'seller_type' => 'owner',
                'seller_id' => $owner->id,
                'total_price' => 60000,
                'net_owner_amount' => 60000,
                'sale_datetime' => sprintf('%d-%02d-15 10:00:00', $year, $month),
                'status' => 1,
            ]);
            app(MonthClosingService::class)->close($owner->id, $year, $month);
        }

        return $owner;
    }

    public function test_index_lists_closed_years(): void
    {
        $owner = $this->makeOwnerWithClosedYear(2026);

        $response = $this->actingAs($owner, 'owner')
            ->get(route('owner.reports.annual-summary'));

        $response->assertOk();
        $years = $response->viewData('years');
        $this->assertCount(1, $years);
        $this->assertSame(2026, $years[0]['year']);
        $this->assertSame(2, $years[0]['summary']['closed_count']);
    }

    public function test_print_renders_analysis_sections(): void
    {
        $owner = $this->makeOwnerWithClosedYear(2026);

        $response = $this->actingAs($owner, 'owner')
            ->get(route('owner.reports.annual-summary.print', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee(__('owner.annual_summary.analysis_title'));
        $response->assertSee(__('owner.annual_summary.trips_title'));
        $response->assertSee(__('owner.annual_summary.payroll_title'));
        $response->assertSee(__('owner.annual_summary.insights_title'));
        $response->assertSee(__('owner.annual_summary.recommendations_title'));
        // The expanded multi-page report renders every new section.
        $response->assertSee(__('owner.annual_summary.waterfall_title'));
        $response->assertSee(__('owner.annual_summary.quarterly_title'));
        $response->assertSee(__('owner.annual_summary.trend_title'));
        $response->assertSee(__('owner.annual_summary.sales_analysis_title'));
        $response->assertSee(__('owner.annual_summary.catch_title'));
        $response->assertSee(__('owner.annual_summary.expenses_type_title'));
        $response->assertSee(__('owner.annual_summary.crew_earnings_title'));
        // Landscape orientation is emitted by the shared report layout.
        $response->assertSee('size: A4 landscape', false);
    }

    public function test_print_renders_sales_and_catch_breakdowns(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');
        User::factory()->create([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'salary_type' => 'percentage',
            'profit_shares' => 1.0,
        ]);

        $fish = Fish::create([
            'name_ar' => 'هامور',
            'name_en' => 'Hamour',
            'status' => 1,
            'owner_id' => $owner->id,
        ]);

        $sale = Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'customer_name' => 'Fish Market Co',
            'total_price' => 60000,
            'net_owner_amount' => 60000,
            'sale_datetime' => '2026-03-15 10:00:00',
            'status' => 1,
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'fish_id' => $fish->id,
            'fish_name' => 'Hamour',
            'quantity' => 100,
            'weight' => 500,
            'price_per_kilo' => 120,
            'total_price' => 60000,
        ]);

        $catch = CatchModel::create([
            'trip_id' => 1,
            'owner_id' => $owner->id,
            'catch_date' => '2026-03-16 08:00:00',
            'total_weight' => 500,
            'total_amount' => 60000,
        ]);
        CatchDetail::create([
            'catch_id' => $catch->id,
            'fish_id' => $fish->id,
            'fish_name' => 'Hamour',
            'weight' => 500,
            'price_per_kg' => 120,
            'total_price' => 60000,
        ]);

        app(MonthClosingService::class)->close($owner->id, 2026, 3);

        $response = $this->actingAs($owner, 'owner')
            ->get(route('owner.reports.annual-summary.print', ['year' => 2026]));

        $response->assertOk();
        // The sale line feeds the "top species" and "top customers" breakdowns;
        // the catch detail feeds the catch production breakdown.
        $response->assertSee('Hamour');
        $response->assertSee('Fish Market Co');
        $response->assertSee(__('owner.annual_summary.by_fish_title'));
        $response->assertSee(__('owner.annual_summary.catch.by_species_title'));
    }
}
