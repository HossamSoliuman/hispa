<?php

namespace Tests\Feature\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminReportsSidebarTest extends TestCase
{
    public function test_reports_menu_is_a_single_link_to_the_reports_hub(): void
    {
        $request = Request::create(route('admin.reports-hub'));
        $route = Route::getRoutes()->getByName('admin.reports-hub');

        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);

        $html = Blade::render(file_get_contents(resource_path('views/admin/partial/sidebar.blade.php')));
        $reportsIconStart = strpos($html, 'bi bi-graph-up');
        $reportsMenuStart = strrpos(substr($html, 0, $reportsIconStart), '<div class="menu-item');
        $settingsMenuStart = strpos($html, 'bi bi-gear', $reportsMenuStart);
        $reportsMenu = substr($html, $reportsMenuStart, $settingsMenuStart - $reportsMenuStart);

        $this->assertStringContainsString('href="'.route('admin.reports-hub').'"', $reportsMenu);
        $this->assertStringNotContainsString('menu-caret', $reportsMenu);
        $this->assertStringNotContainsString('menu-submenu', $reportsMenu);

        foreach ([
            'admin.sales-report',
            'admin.stock-report',
            'admin.trip-report',
            'admin.fish-history-report',
            'admin.boat-report',
            'admin.owner-report',
            'admin.revenue-report',
        ] as $routeName) {
            $this->assertStringNotContainsString('href="'.route($routeName).'"', $reportsMenu);
        }
    }
}
