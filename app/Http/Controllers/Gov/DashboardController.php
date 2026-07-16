<?php

namespace App\Http\Controllers\Gov;

use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\CatchDetail;
use App\Models\Port;
use App\Models\Sale;
use App\Models\Season;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Government supervisor overview: platform-wide marine-fishing aggregates —
     * production, active sailors, trips, ports, seasons and sales — plus a
     * monthly fish-production trend. Gov users are not owner-scoped, so every
     * figure spans the whole platform.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $year = $today->year;

        // ===== Fish production (by weight) =====
        $annualProduction = (float) $this->productionQuery()->whereYear('catch_models.catch_date', $year)->sum('catch_details.weight');
        $monthlyProduction = (float) $this->productionQuery()
            ->whereYear('catch_models.catch_date', $year)
            ->whereMonth('catch_models.catch_date', $today->month)
            ->sum('catch_details.weight');
        $dailyProduction = (float) $this->productionQuery()->whereDate('catch_models.catch_date', $today)->sum('catch_details.weight');
        $productionValue = (float) $this->productionQuery()->whereYear('catch_models.catch_date', $year)->sum('catch_details.total_price');

        // ===== Active sailors (crew) =====
        $registeredSailors = User::where('role', 'crew')->count();
        $activeSailors = User::where('role', 'crew')->where('status', 1)->count();
        $saudiSailors = User::where('role', 'crew')
            ->whereIn('nationality', ['saudi', 'سعودي', 'السعودية', 'السعودي'])
            ->count();
        $foreignSailors = max(0, $registeredSailors - $saudiSailors);

        // ===== Fishing trips =====
        $totalTrips = Trip::count();
        $activeTrips = Trip::where('status', TripStatus::InProgress->value)->count();
        $annualTrips = Trip::whereYear('created_at', $year)->count();
        $monthlyTrips = Trip::whereYear('created_at', $year)->whereMonth('created_at', $today->month)->count();
        $activeTripsPercent = $totalTrips > 0 ? (int) round($activeTrips / $totalTrips * 100) : 0;

        // ===== Ports =====
        $totalPorts = Port::count();
        $govPorts = Port::where('status', 1)
            ->where(fn ($q) => $q->where('category_ar', 'حكومي')->orWhere('category_en', 'like', '%gov%'))
            ->count();
        $privatePorts = Port::where('status', 1)
            ->where(fn ($q) => $q->where('category_ar', 'خاص')->orWhere('category_en', 'like', '%priv%'))
            ->count();

        // ===== Fishing seasons =====
        // The seasons feature is still being wired up; degrade gracefully if the
        // backing table has not been provisioned in this environment yet.
        $hasSeasons = Schema::hasTable('seasons');
        $activeSeasons = $hasSeasons ? Season::where('status', 1)->count() : 0;
        $totalSeasons = $hasSeasons ? Season::count() : 0;

        // ===== Sales =====
        $totalSales = (float) Sale::sum('total_price');
        $dailySales = (float) Sale::whereDate('created_at', $today)->sum('total_price');
        $monthlySales = (float) Sale::whereYear('created_at', $year)->whereMonth('created_at', $today->month)->sum('total_price');
        $salesCount = Sale::count();

        // ===== Monthly production trend (current year) =====
        $productionTrend = $this->productionTrend($year);

        // ===== Localized date/time header (Riyadh) =====
        $riyadhNow = Carbon::now('Asia/Riyadh');
        $riyadhTime = $riyadhNow->format('h:i A');
        $hijriDate = $this->hijriDate($riyadhNow);

        return view('gov.dashboard.index', compact(
            'annualProduction',
            'monthlyProduction',
            'dailyProduction',
            'productionValue',
            'registeredSailors',
            'activeSailors',
            'saudiSailors',
            'foreignSailors',
            'totalTrips',
            'activeTrips',
            'annualTrips',
            'monthlyTrips',
            'activeTripsPercent',
            'totalPorts',
            'govPorts',
            'privatePorts',
            'activeSeasons',
            'totalSeasons',
            'totalSales',
            'dailySales',
            'monthlySales',
            'salesCount',
            'productionTrend',
            'riyadhTime',
            'hijriDate',
        ));
    }

    /**
     * Localized Hijri date string (weekday + day + month + year), e.g.
     * "السبت، 21 ربيع الأول 1447 هـ". Uses the tabular Islamic calendar so it
     * works without the intl extension.
     */
    private function hijriDate(Carbon $date): string
    {
        [$hy, $hm, $hd] = $this->gregorianToHijri($date->year, $date->month, $date->day);

        $weekday = $date->locale(app()->getLocale())->translatedFormat('l');
        $months = (array) __('gov.dashboard.hijri_months');
        $monthName = $months[$hm - 1] ?? (string) $hm;
        $separator = app()->getLocale() === 'ar' ? '، ' : ', ';

        return $weekday.$separator.$hd.' '.$monthName.' '.$hy.' '.__('gov.dashboard.hijri');
    }

    /**
     * Convert a Gregorian date to the tabular Islamic (Hijri) calendar.
     *
     * @return array{0: int, 1: int, 2: int} [year, month, day]
     */
    private function gregorianToHijri(int $year, int $month, int $day): array
    {
        if ($month < 3) {
            $year -= 1;
            $month += 12;
        }

        $a = intdiv($year, 100);
        $b = 2 - $a + intdiv($a, 4);
        $jd = (int) (365.25 * ($year + 4716)) + (int) (30.6001 * ($month + 1)) + $day + $b - 1524;

        $l = $jd - 1948440 + 10632;
        $n = intdiv($l - 1, 10631);
        $l = $l - 10631 * $n + 354;
        $j = intdiv(10985 - $l, 5316) * intdiv(50 * $l, 17719) + intdiv($l, 5670) * intdiv(43 * $l, 15238);
        $l = $l - intdiv(30 - $j, 15) * intdiv(17719 * $j, 50) - intdiv($j, 16) * intdiv(15238 * $j, 43) + 29;
        $hijriMonth = intdiv(24 * $l, 709);
        $hijriDay = $l - intdiv(709 * $hijriMonth, 24);
        $hijriYear = 30 * $n + $j - 30;

        return [$hijriYear, $hijriMonth, $hijriDay];
    }

    /**
     * Base query joining catch line-items to their parent catch (for the date).
     */
    private function productionQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return CatchDetail::query()
            ->join('catch_models', 'catch_details.catch_id', '=', 'catch_models.id');
    }

    /**
     * Total caught weight for each month of the given year, for the trend chart.
     *
     * @return array{labels: array<int, string>, data: array<int, float>}
     */
    private function productionTrend(int $year): array
    {
        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create($year, $month, 1)->translatedFormat('M');
            $data[] = (float) $this->productionQuery()
                ->whereYear('catch_models.catch_date', $year)
                ->whereMonth('catch_models.catch_date', $month)
                ->sum('catch_details.weight');
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
