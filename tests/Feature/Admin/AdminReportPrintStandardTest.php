<?php

namespace Tests\Feature\Admin;

use App\Enums\TripStatus;
use App\Models\Admin;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Fish;
use App\Models\FishStockHistory;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\Trip;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminReportPrintStandardTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private User $owner;

    private Sale $sale;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->admin = Admin::query()->create([
            'name' => 'Report Administrator',
            'email' => 'reports@example.com',
            'password' => Hash::make('password'),
            'status' => true,
            'roles_name' => ['owner'],
        ]);
        $this->admin->assignRole(Role::findByName('owner', 'admin'));

        $this->createPopulatedReportFixtures();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
        ]);
    }

    public function test_all_admin_print_routes_render_the_standard_in_arabic_and_english(): void
    {
        $reports = [
            'owners' => [
                'route' => 'admin.owner-report.print',
                'title' => 'admin.report.owners.print_title',
                'owner_header' => null,
            ],
            'revenue' => [
                'route' => 'admin.revenue-report.print',
                'title' => 'admin.report.revenue.print_title',
                'owner_header' => null,
            ],
            'sales' => [
                'route' => 'admin.sales-report.print',
                'title' => 'admin.report.sales.platform_print_title',
                'owner_header' => 'admin.report.sales.owner',
            ],
            'stock' => [
                'route' => 'admin.stock-report.print',
                'title' => 'admin.report.stock.platform_print_title',
                'owner_header' => 'admin.report.stock.owner',
            ],
            'fish-history' => [
                'route' => 'admin.fish-history-report.print',
                'title' => 'admin.report.fish_history.platform_print_title',
                'owner_header' => 'admin.report.fish_history.owner',
            ],
            'trips' => [
                'route' => 'admin.trip-report.print',
                'title' => 'admin.report.trips.platform_print_title',
                'owner_header' => 'admin.report.trips.owner',
            ],
            'boats' => [
                'route' => 'admin.boat-report.print',
                'title' => 'admin.report.boats.platform_print_title',
                'owner_header' => 'admin.report.boats.owner',
            ],
        ];

        foreach (['ar', 'en'] as $locale) {
            App::setLocale($locale);

            foreach ($reports as $reportName => $report) {
                $response = $this->actingAs($this->admin, 'admin')->get(route($report['route']));

                $response->assertOk();

                $html = $response->getContent();
                $title = trans($report['title'], [], $locale);

                $this->assertNotSame('', trim($title), "{$reportName} has an empty {$locale} title.");
                $this->assertNotSame($report['title'], $title, "{$reportName} is missing its {$locale} title translation.");
                $response->assertSeeText($title);
                $response->assertSeeText('Hispa Platform');
                $response->assertSeeText('Hispa Company');
                $this->assertStringContainsString('class="rmast"', $html, "{$reportName} is missing the company masthead.");
                $this->assertMatchesRegularExpression('/<div class="rtitle">\s*\S.*?<\/div>/su', $html, "{$reportName} has no rendered title.");
                $this->assertStringContainsString('<tfoot>', $html, "{$reportName} is missing its populated totals row.");

                if ($report['owner_header'] !== null) {
                    $ownerHeader = trans($report['owner_header'], [], $locale);

                    $this->assertNotSame($report['owner_header'], $ownerHeader);
                    $this->assertMatchesRegularExpression(
                        '/<th\b[^>]*>\s*'.preg_quote(e($ownerHeader), '/').'\s*<\/th>/u',
                        $html,
                        "{$reportName} is missing the localized {$locale} owner column."
                    );
                }
            }
        }
    }

    public function test_owner_sales_print_keeps_default_dates_and_hides_platform_owner_column(): void
    {
        App::setLocale('en');

        $ownerRole = Role::query()->firstOrCreate([
            'name' => 'owner',
            'guard_name' => 'web',
        ]);
        $this->owner->assignRole($ownerRole);

        $response = $this->actingAs($this->owner, 'owner')->get(route('owner.sales-report.print'));

        $response->assertOk();
        $response->assertSeeText($this->sale->number);
        $response->assertSeeText(trans('owner.sales_report.all_dates', [], 'en'));
        $this->assertDoesNotMatchRegularExpression(
            '/<th\b[^>]*>\s*'.preg_quote(e(trans('admin.report.sales.owner', [], 'en')), '/').'\s*<\/th>/u',
            $response->getContent()
        );
    }

    private function createPopulatedReportFixtures(): void
    {
        Setting::query()->insert([
            ['key' => 'title', 'value' => 'Hispa Company', 'type' => 'text'],
            ['key' => 'site_name', 'value' => 'Hispa Company', 'type' => 'text'],
            ['key' => 'title_en', 'value' => 'Hispa Platform', 'type' => 'text'],
            ['key' => 'phone', 'value' => '5551234567', 'type' => 'text'],
            ['key' => 'email', 'value' => 'reports@hispa.example', 'type' => 'text'],
        ]);

        $this->owner = User::factory()->create([
            'name' => 'Fixture Owner',
            'role' => 'owner',
            'status' => 1,
        ]);

        $package = SubscriptionPackage::query()->create([
            'name_ar' => 'خطة التحقق',
            'name_en' => 'Verification Plan',
            'boats_count' => 3,
            'price' => 100,
            'original_price' => 120,
            'duration_type' => 'monthly',
            'is_active' => true,
        ]);

        $subscription = Subscription::query()->create([
            'user_id' => $this->owner->id,
            'package_id' => $package->id,
            'start_date' => now()->subWeek()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'active',
            'is_suspended' => false,
        ]);

        Invoice::query()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $this->owner->id,
            'invoice_number' => 'INV-REPORT-0001',
            'amount' => 100,
            'vat_rate' => 15,
            'vat_amount' => 15,
            'total_amount' => 115,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $boat = Boat::query()->create([
            'owner_id' => $this->owner->id,
            'name_ar' => 'قارب التحقق',
            'name_en' => 'Verification Boat',
            'number' => 'BOAT-REPORT-001',
            'status' => 1,
            'type' => 'Fishing',
            'serial_number' => 'SER-001',
            'body_number' => 'BODY-001',
            'payload' => 800,
        ]);

        $captain = User::factory()->create([
            'name' => 'Fixture Captain',
            'role' => 'captain',
            'owner_id' => $this->owner->id,
            'boat_id' => $boat->id,
            'status' => 1,
        ]);

        $trip = Trip::factory()->create([
            'name' => 'رحلة التحقق',
            'name_en' => 'Verification Trip',
            'status' => TripStatus::Sold,
            'owner_id' => $this->owner->id,
            'captain_id' => $captain->id,
            'boat_id' => $boat->id,
            'boat_name' => $boat->name_ar,
            'boat_number' => $boat->number,
            'start_date' => now()->subDay(),
            'end_date' => now(),
        ]);

        $unit = Unit::query()->create([
            'name_ar' => 'كيلوجرام',
            'name_en' => 'Kilogram',
            'is_default' => true,
            'status' => true,
            'owner_id' => $this->owner->id,
        ]);

        $fish = Fish::query()->create([
            'name_ar' => 'سمك التحقق',
            'name_en' => 'Verification Fish',
            'status' => 1,
            'owner_id' => $this->owner->id,
        ]);

        $catch = CatchModel::query()->create([
            'trip_id' => $trip->id,
            'owner_id' => $this->owner->id,
            'catch_date' => now(),
            'total_weight' => 25,
            'total_amount' => 250,
        ]);

        CatchDetail::query()->create([
            'catch_id' => $catch->id,
            'fish_id' => $fish->id,
            'unit_id' => $unit->id,
            'fish_name' => $fish->name_en,
            'weight' => 25,
            'price_per_kg' => 10,
            'total_price' => 250,
        ]);

        FishStockHistory::query()->create([
            'fish_id' => $fish->id,
            'operation_type' => 'add',
            'changed_weight' => 25,
            'before_weight' => 0,
            'after_weight' => 25,
            'done_by' => $captain->id,
        ]);

        $paymentMethod = PaymentMethod::query()->create([
            'name' => 'نقدي',
            'name_en' => 'Cash',
            'status' => 1,
            'owner_id' => $this->owner->id,
        ]);

        $this->sale = Sale::query()->create([
            'number' => 'SALE-REPORT-001',
            'seller_type' => 'owner',
            'seller_id' => $this->owner->id,
            'trip_id' => $trip->id,
            'catch_id' => $catch->id,
            'boat_id' => $boat->id,
            'customer_name' => 'Fixture Customer',
            'payment_method_id' => $paymentMethod->id,
            'payment_status' => 'paid',
            'status' => 1,
            'total_price' => 250,
            'net_owner_amount' => 200,
            'remaining_total' => 0,
            'sale_datetime' => now(),
        ]);

        SaleDetail::query()->create([
            'sale_id' => $this->sale->id,
            'fish_id' => $fish->id,
            'unit_id' => $unit->id,
            'fish_name' => $fish->name_en,
            'quantity' => 1,
            'weight' => 25,
            'price_per_kilo' => 10,
            'total_price' => 250,
        ]);
    }
}
