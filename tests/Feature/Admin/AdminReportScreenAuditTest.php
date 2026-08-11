<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminReportScreenAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_report_screens_render_in_arabic_and_english_with_working_controls(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = Admin::query()->create([
            'name' => 'Report Screen Administrator',
            'email' => 'report-screens@example.com',
            'password' => Hash::make('password'),
            'status' => true,
            'roles_name' => ['owner'],
        ]);
        $admin->assignRole(Role::findByName('owner', 'admin'));

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
        ]);

        $screens = [
            'hub' => [
                'route' => 'admin.reports-hub',
                'title' => 'admin.report.hub.title',
                'markers' => ['card shadow-sm', 'list-group-item'],
            ],
            'owners' => [
                'route' => 'admin.owner-report',
                'title' => 'admin.report.owners.title',
                'print_route' => 'admin.owner-report.print',
                'markers' => ['ownersFilters', 'URLSearchParams(new FormData(form))'],
            ],
            'revenue' => [
                'route' => 'admin.revenue-report',
                'title' => 'admin.report.revenue.title',
                'print_route' => 'admin.revenue-report.print',
                'markers' => ['payment_status', 'start_date=', 'end_date='],
            ],
            'sales' => [
                'route' => 'admin.sales-report',
                'title' => 'admin.report.sales.title',
                'print_route' => 'admin.sales-report.print',
                'markers' => ['id="filterBtn"', 'id="resetBtn"', 'start_date=', 'end_date=', 'status='],
            ],
            'stock' => [
                'route' => 'admin.stock-report',
                'title' => 'admin.report.stock.title',
                'print_route' => 'admin.stock-report.print',
                'markers' => ['id="filterBtn"', 'id="resetBtn"', 'start_date=', 'end_date=', 'fish_type='],
            ],
            'fish-history' => [
                'route' => 'admin.fish-history-report',
                'title' => 'admin.report.fish_history.title',
                'print_route' => 'admin.fish-history-report.print',
                'markers' => ['id="filterBtn"', 'id="resetBtn"', 'start_date=', 'end_date=', 'fish_id='],
            ],
            'trips' => [
                'route' => 'admin.trip-report',
                'title' => 'admin.report.trip.title',
                'print_route' => 'admin.trip-report.print',
                'markers' => ['id="filterBtn"', 'id="resetBtn"', 'start_date=', 'end_date=', 'status=', 'boat_id='],
            ],
            'boats' => [
                'route' => 'admin.boat-report',
                'title' => 'admin.report.boats.title',
                'print_route' => 'admin.boat-report.print',
                'markers' => [],
            ],
        ];

        foreach (['ar', 'en'] as $locale) {
            App::setLocale($locale);

            foreach ($screens as $screenName => $screen) {
                $response = $this->actingAs($admin, 'admin')->get(route($screen['route']));

                $response->assertOk();

                $html = $response->getContent();
                $title = trans($screen['title'], [], $locale);

                $this->assertNotSame('', trim($title), "{$screenName} has an empty {$locale} heading.");
                $this->assertNotSame($screen['title'], $title, "{$screenName} is missing its {$locale} heading translation.");
                $response->assertSeeText($title);

                foreach ($screen['markers'] as $marker) {
                    $this->assertStringContainsString($marker, $html, "{$screenName} is missing {$marker}.");
                }

                if (isset($screen['print_route'])) {
                    $this->assertStringContainsString('hud-stat-card', $html, "{$screenName} is missing KPI cards.");
                    $this->assertStringContainsString('table-responsive', $html, "{$screenName} is missing its table card.");
                    $this->assertStringContainsString(route($screen['print_route']), $html, "{$screenName} is missing its print action.");
                } else {
                    foreach (array_column($screens, 'route') as $routeName) {
                        if ($routeName !== 'admin.reports-hub') {
                            $this->assertStringContainsString(route($routeName), $html, "The reports hub is missing {$routeName}.");
                        }
                    }
                }
            }
        }
    }
}
