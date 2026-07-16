<?php

namespace Tests\Feature\Gov;

use App\Models\User;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class GovDashboardDesignTest extends TestCase
{
    public function test_dashboard_renders_the_compact_government_layout(): void
    {
        app()->setLocale('ar');

        auth('gov')->setUser(new User([
            'name' => 'Gov Test',
            'email' => 'gov@example.com',
        ]));

        $html = view('gov.dashboard.index', [
            'annualProduction' => 100.0,
            'monthlyProduction' => 12.0,
            'dailyProduction' => 1.0,
            'productionValue' => 900.0,
            'registeredSailors' => 10,
            'activeSailors' => 8,
            'saudiSailors' => 6,
            'foreignSailors' => 4,
            'totalTrips' => 30,
            'activeTrips' => 3,
            'annualTrips' => 20,
            'monthlyTrips' => 2,
            'activeTripsPercent' => 10,
            'totalPorts' => 5,
            'govPorts' => 3,
            'privatePorts' => 2,
            'activeSeasons' => 1,
            'totalSeasons' => 4,
            'totalSales' => 1500.0,
            'dailySales' => 100.0,
            'monthlySales' => 600.0,
            'salesCount' => 12,
            'productionTrend' => [
                'labels' => ['يناير', 'فبراير'],
                'data' => [45.0, 55.0],
            ],
            'riyadhTime' => '02:44 PM',
            'hijriDate' => 'الخميس، 1 محرم 1448 هـ',
            'errors' => new ViewErrorBag,
        ])->render();

        $this->assertStringContainsString('dir="rtl"', $html);
        $this->assertStringContainsString("font-family: 'Tajawal', sans-serif !important", $html);
        $this->assertStringContainsString('--gov-sidebar-width: 13.5rem', $html);
        $this->assertStringContainsString('gov-dashboard-shell', $html);
        $this->assertStringContainsString('gov-banner-title', $html);
        $this->assertStringContainsString('color: #fff', $html);
        $this->assertStringContainsString('gov-card-grid', $html);
        $this->assertStringContainsString('gov-card-production', $html);
        $this->assertStringContainsString('gov-card-sailors', $html);
        $this->assertStringContainsString('gov-card-trips', $html);
        $this->assertStringContainsString('gov-card-ports', $html);
        $this->assertStringContainsString('gov-card-seasons', $html);
        $this->assertStringContainsString('gov-card-sales', $html);
        $this->assertStringContainsString('gov-chart-card', $html);
        $this->assertStringContainsString('dashboard/assets/plugins/chart.js/dist/chart.umd.js', $html);
        $this->assertStringContainsString('الجهة الحكومية', $html);
    }
}
