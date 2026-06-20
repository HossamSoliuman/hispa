<?php

namespace App\Http\Controllers\Owner;

use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Category;
use App\Models\Expense;
use App\Models\MonthClosing;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\User;
use App\Service\Owner\MonthlyFinancialsService;
use App\Service\Owner\MonthlyReportsService;
use App\Service\Owner\OwnerAlertService;
use App\Support\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const ARABIC_MONTHS = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];

    private const ENGLISH_MONTHS = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    public function __construct(
        private MonthlyFinancialsService $financials,
        private MonthlyReportsService $reports,
        private OwnerAlertService $alerts,
    ) {}

    private function ownerId(): int
    {
        return (int) Auth::guard('owner')->id();
    }

    /**
     * Localized month name for the active application locale.
     */
    private function monthName(int $month): string
    {
        return app()->getLocale() === 'en'
            ? self::ENGLISH_MONTHS[$month]
            : self::ARABIC_MONTHS[$month];
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function ownerSaleIds(int $ownerId, ?string $from = null, ?string $to = null)
    {
        return Sale::where('seller_type', 'owner')->where('seller_id', $ownerId)
            ->when($from && $to, fn ($q) => $q->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]))
            ->pluck('id');
    }

    /**
     * The live current month window (plan: dashboard top numbers show the current
     * month until it is closed).
     *
     * @return array{0: string, 1: string}
     */
    private function currentMonthRange(): array
    {
        return [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function currentYearRange(): array
    {
        return [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()];
    }

    public function index()
    {
        $ownerId = $this->ownerId();
        [$from, $to] = $this->currentMonthRange();

        $currentMonthLabel = $this->monthName(now()->month).' '.now()->year;
        $currentMonthRangeLabel = now()->startOfMonth()->day.' – '.now()->endOfMonth()->day
            .' '.$this->monthName(now()->month).' '.now()->year;

        $ownerSales = fn () => Sale::where('seller_type', 'owner')->where('seller_id', $ownerId);
        $currentMonthSales = fn () => $ownerSales()->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]);

        // Top KPI cards reflect the live current month until it is closed.
        $currentMonthRevenue = (float) $currentMonthSales()->sum('total_price');
        $totalRevenue = $currentMonthRevenue;

        $previousMonthRevenue = (float) $ownerSales()
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [
                now()->subMonth()->startOfMonth()->toDateString(),
                now()->subMonth()->endOfMonth()->toDateString(),
            ])->sum('total_price');

        $percentageChange = $this->monthOverMonthChange($currentMonthRevenue, $previousMonthRevenue);

        $totalCatch = $this->currentMonthCatch($ownerId, $from, $to);

        $soldWeight = (float) SaleDetail::whereIn('sale_id', $this->ownerSaleIds($ownerId, $from, $to))->sum('weight');
        $averagePricePerKg = $soldWeight > 0 ? round($currentMonthRevenue / $soldWeight, 2) : 0;

        $activeBoats = Boat::where('owner_id', $ownerId)->where('status', 1)->count();
        $completedTrips = Trip::where('owner_id', $ownerId)->where('status', TripStatus::Sold->value)->count();

        $currentMonthProfit = $this->currentMonthProfit($ownerId);
        $profit = $currentMonthProfit;
        $profitMargin = $currentMonthRevenue > 0 ? round(($currentMonthProfit / $currentMonthRevenue) * 100, 2) : 0;

        $topFive = $this->topFive($ownerId);

        $alerts = $this->alerts->for($ownerId);
        $alertSummary = $this->alerts->summarize($alerts);

        return view('owner.dashboard.index', compact(
            'totalRevenue',
            'currentMonthRevenue',
            'currentMonthProfit',
            'percentageChange',
            'totalCatch',
            'averagePricePerKg',
            'profitMargin',
            'profit',
            'activeBoats',
            'completedTrips',
            'topFive',
            'currentMonthLabel',
            'currentMonthRangeLabel',
            'alerts',
            'alertSummary'
        ));
    }

    /**
     * Owner alerts as JSON for the dashboard card's optional background refresh.
     */
    public function alerts(): JsonResponse
    {
        $alerts = $this->alerts->for($this->ownerId());

        return response()->json([
            'alerts' => $alerts->map(fn (Alert $alert): array => $alert->toArray())->values(),
            'summary' => $this->alerts->summarize($alerts),
        ]);
    }

    /**
     * The client's "أهم 5" landing data for the current month (plan §4.3):
     * month profit (vs last month), crew dues, boat & trip profitability,
     * production by species. All money via the canonical services.
     *
     * @return array<string, mixed>
     */
    private function topFive(int $ownerId): array
    {
        $from = now()->startOfMonth()->toDateString();
        $to = now()->endOfMonth()->toDateString();
        $prevFrom = now()->subMonth()->startOfMonth()->toDateString();
        $prevTo = now()->subMonth()->endOfMonth()->toDateString();

        $current = $this->financials->compute($ownerId, $from, $to);
        $previous = $this->financials->compute($ownerId, $prevFrom, $prevTo);

        $profitChange = $this->monthOverMonthChange($current['net_profit'], $previous['net_profit']);

        $yearClosings = MonthClosing::where('owner_id', $ownerId)
            ->where('year', now()->year)
            ->get()
            ->keyBy('month');

        $closing = $yearClosings->get(now()->month);

        $unpaidDues = $closing
            ? (float) $closing->dues()->sum(DB::raw('due_amount - paid_amount'))
            : 0.0;

        $boats = $this->reports->boatProfitability($ownerId, $from, $to);
        $trips = $this->reports->tripProfitability($ownerId, $from, $to);
        $species = $this->reports->productionBySpecies($ownerId, $from, $to);

        return [
            'net_profit' => $current['net_profit'],
            'profit_change' => $profitChange,
            'crew_pool' => $current['crew_share'],
            'unpaid_dues' => round($unpaidDues, 2),
            'is_closed' => (bool) $closing,
            'boats' => array_slice($boats, 0, 5),
            'boats_max' => ! empty($boats) ? max(array_map(fn ($b) => abs($b['net_profit']), $boats)) : 0.0,
            'trips' => array_slice($trips, 0, 5),
            'species' => array_slice($species, 0, 5),
            'year' => now()->year,
            'months' => $this->monthsClosingStatus($yearClosings),
        ];
    }

    /**
     * Closing status for every month of the current year, used by the dashboard
     * month-closing overview card.
     *
     * @param  \Illuminate\Support\Collection<int, MonthClosing>  $yearClosings  closings keyed by month
     * @return array<int, array{month: int, name: string, is_closed: bool, is_future: bool, closing_id: int|null}>
     */
    private function monthsClosingStatus($yearClosings): array
    {
        $currentMonth = now()->month;

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $closing = $yearClosings->get($m);
            $months[] = [
                'month' => $m,
                'name' => $this->monthName($m),
                'is_closed' => $closing !== null,
                'is_future' => $m > $currentMonth,
                'closing_id' => $closing?->id,
            ];
        }

        return $months;
    }

    /**
     * Percentage change between this month and last month. When last month is
     * zero, a non-zero current month is treated as a full ±100% swing rather
     * than a misleading 0% (which would read as "no change").
     */
    private function monthOverMonthChange(float $current, float $previous): float
    {
        if ($previous != 0.0) {
            return round((($current - $previous) / abs($previous)) * 100, 1);
        }

        if ($current > 0.0) {
            return 100.0;
        }

        if ($current < 0.0) {
            return -100.0;
        }

        return 0.0;
    }

    /**
     * Total catch weight landed within the given window (falls back to catch
     * detail rows when the header total has not been rolled up yet).
     */
    private function currentMonthCatch(int $ownerId, string $from, string $to): float
    {
        $tripIds = Trip::where('owner_id', $ownerId)
            ->whereBetween(DB::raw('DATE(actual_end_datetime)'), [$from, $to])
            ->pluck('id');

        $catchIds = CatchModel::whereIn('trip_id', $tripIds)->pluck('id');

        $totalWeight = (float) CatchModel::whereIn('trip_id', $tripIds)->sum('total_weight');
        if ($totalWeight == 0.0) {
            $totalWeight = (float) CatchDetail::whereIn('catch_id', $catchIds)->sum('weight');
        }

        return $totalWeight;
    }

    private function currentMonthProfit(int $ownerId): float
    {
        $from = now()->startOfMonth()->toDateString();
        $to = now()->endOfMonth()->toDateString();

        $netRevenue = (float) Sale::where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to])
            ->sum('net_owner_amount');

        $expenses = (float) Expense::where('owner_id', $ownerId)
            ->whereBetween('date', [$from, $to])
            ->sum('final_price');

        return round($netRevenue - $expenses, 2);
    }

    public function overviewData()
    {
        $ownerId = $this->ownerId();
        $year = now()->year;
        [$yearFrom, $yearTo] = $this->currentYearRange();
        $saleIds = $this->ownerSaleIds($ownerId, $yearFrom, $yearTo);

        $monthly = $this->monthlySeries(
            $this->sumByMonth(
                Sale::where('seller_type', 'owner')->where('seller_id', $ownerId),
                'sale_datetime',
                'net_owner_amount',
                $year
            ),
            $this->sumByMonth(
                Expense::where('owner_id', $ownerId),
                'date',
                'final_price',
                $year
            )
        );

        $catchComposition = SaleDetail::whereIn('sale_id', $saleIds)
            ->selectRaw('fish_name, SUM(weight * price_per_kilo) as total_value')
            ->groupBy('fish_name')
            ->get();

        $totalValue = (float) $catchComposition->sum('total_value');
        $catchComposition->each(function ($item) use ($totalValue) {
            $item->percentage = $totalValue > 0 ? round(($item->total_value / $totalValue) * 100, 1) : 0;
        });

        $totalCatchKg = (float) SaleDetail::whereIn('sale_id', $saleIds)->sum('weight');
        $totalRevenue = (float) Sale::where('seller_type', 'owner')->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$yearFrom, $yearTo])->sum('net_owner_amount');
        $totalExpenses = (float) Expense::where('owner_id', $ownerId)
            ->whereBetween('date', [$yearFrom, $yearTo])->sum('final_price');

        return response()->json([
            'monthly' => $monthly,
            'catchComposition' => $catchComposition,
            'totalCatchKg' => $totalCatchKg,
            'summary' => [
                'revenue' => round($totalRevenue, 2),
                'profit' => round($totalRevenue - $totalExpenses, 2),
                'avgPricePerKg' => $totalCatchKg > 0 ? round($totalRevenue / $totalCatchKg, 2) : 0,
            ],
        ]);
    }

    public function getRecentActivities()
    {
        $ownerId = $this->ownerId();
        $activities = [];

        $trips = Trip::with('boat:id,name_ar,name_en')->where('owner_id', $ownerId)->latest('updated_at')->take(5)->get();
        foreach ($trips as $trip) {
            $boatName = $trip->boat?->name ?: $trip->boat_name;
            $activities[] = [
                'icon' => 'bi-clipboard-check text-info',
                'message' => "{$trip->status->label()} - {$boatName} - {$trip->number}",
                'timestamp' => $trip->updated_at,
                'badge_class' => 'bg-'.$trip->status->color(),
            ];
        }

        $sales = Sale::where('seller_type', 'owner')->where('seller_id', $ownerId)->latest('sale_datetime')->take(5)->get();
        foreach ($sales as $sale) {
            $activities[] = [
                'icon' => 'bi-cash-coin text-success',
                'message' => __('owner.dashboard.sale').' - '.($sale->customer_name ?? '').' - '.__('owner.dashboard.sar').number_format((float) $sale->total_price),
                'timestamp' => $sale->sale_datetime ?? $sale->updated_at,
                'badge_class' => 'bg-success',
            ];
        }

        $activities = collect($activities)
            ->sortByDesc('timestamp')
            ->take(5)
            ->map(function ($activity) {
                $activity['time'] = optional($activity['timestamp'])->diffForHumans();
                unset($activity['timestamp']);

                return $activity;
            })
            ->values();

        return response()->json($activities);
    }

    public function summary()
    {
        $ownerId = $this->ownerId();
        [$yearFrom, $yearTo] = $this->currentYearRange();

        $revenue = (float) Sale::where('seller_type', 'owner')->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$yearFrom, $yearTo])->sum('total_price');
        $expenses = (float) Expense::where('owner_id', $ownerId)
            ->whereBetween('date', [$yearFrom, $yearTo])->sum('final_price');

        $categories = Category::withSum(['expenses' => function ($q) use ($ownerId, $yearFrom, $yearTo) {
            $q->where('owner_id', $ownerId)->whereBetween('date', [$yearFrom, $yearTo]);
        }], 'final_price')->get();

        $netRevenue = $revenue;
        $profit = $netRevenue - $expenses;
        $margin = $netRevenue > 0 ? round(($profit / $netRevenue) * 100, 2) : 0;

        return response()->json([
            'revenue' => round($netRevenue, 2),
            'expenses' => round($expenses, 2),
            'profit' => round($profit, 2),
            'margin' => $margin,
            'categories' => $categories,
            'monthly' => $this->getMonthlyFinancials(),
        ]);
    }

    /**
     * @return array<int, array{month: int, month_name: string, revenue: float, expenses: float}>
     */
    private function getMonthlyFinancials(): array
    {
        $ownerId = $this->ownerId();
        $year = now()->year;

        $sales = $this->sumByMonth(
            Sale::where('seller_type', 'owner')->where('seller_id', $ownerId),
            'sale_datetime',
            'net_owner_amount',
            $year
        );
        $expenses = $this->sumByMonth(
            Expense::where('owner_id', $ownerId),
            'date',
            'final_price',
            $year
        );

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[] = [
                'month' => $m,
                'month_name' => $this->monthName($m),
                'revenue' => round((float) ($sales[$m] ?? 0), 2),
                'expenses' => round((float) ($expenses[$m] ?? 0), 2),
            ];
        }

        return $monthly;
    }

    public function getOperationsData()
    {
        $ownerId = $this->ownerId();

        $captains = User::where('owner_id', $ownerId)->where('role', 'captain')->get();

        $tripsByCaptain = Trip::where('owner_id', $ownerId)
            ->selectRaw('captain_id, COUNT(*) as trips_count')
            ->groupBy('captain_id')
            ->pluck('trips_count', 'captain_id');

        $revenueByCaptain = Sale::where('seller_type', 'owner')->where('seller_id', $ownerId)
            ->join('trips', 'sales.trip_id', '=', 'trips.id')
            ->selectRaw('trips.captain_id as captain_id, SUM(sales.total_price) as revenue')
            ->groupBy('trips.captain_id')
            ->pluck('revenue', 'captain_id');

        $catchByCaptain = CatchModel::join('trips', 'catch_models.trip_id', '=', 'trips.id')
            ->where('trips.owner_id', $ownerId)
            ->selectRaw('trips.captain_id as captain_id, SUM(catch_models.total_weight) as catch_weight')
            ->groupBy('trips.captain_id')
            ->pluck('catch_weight', 'captain_id');

        $maxRevenue = (float) ($revenueByCaptain->max() ?: 0);

        $sailorsData = $captains->map(function ($captain) use ($tripsByCaptain, $revenueByCaptain, $catchByCaptain, $maxRevenue) {
            $revenue = (float) ($revenueByCaptain[$captain->id] ?? 0);

            return [
                'name' => $captain->name,
                'boat_name' => $captain->boat_name,
                'trips' => (int) ($tripsByCaptain[$captain->id] ?? 0),
                'catch' => round((float) ($catchByCaptain[$captain->id] ?? 0), 2),
                'revenue' => round($revenue, 2),
                // Efficiency = revenue relative to the top captain (0-100).
                'efficiency' => $maxRevenue > 0 ? round(($revenue / $maxRevenue) * 100, 2) : 0,
            ];
        });

        $topCaptains = $sailorsData->sortByDesc('revenue')->take(5)->values();

        $dailyOperations = Trip::where('owner_id', $ownerId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as trips_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyCatch = CatchModel::join('trips', 'catch_models.trip_id', '=', 'trips.id')
            ->where('trips.owner_id', $ownerId)
            ->selectRaw('DATE(catch_models.catch_date) as date, SUM(catch_models.total_weight) as total_catch')
            ->groupBy('date')
            ->pluck('total_catch', 'date');

        $dailyOperations->each(function ($row) use ($dailyCatch) {
            $row->total_catch = round((float) ($dailyCatch[$row->date] ?? 0), 2);
        });

        return response()->json([
            'sailors' => $topCaptains,
            'dailyOperations' => $dailyOperations,
            'fleetStatus' => [
                'total' => Boat::where('owner_id', $ownerId)->count(),
                'active' => Boat::where('owner_id', $ownerId)->where('status', 1)->count(),
                'maintenance' => Boat::where('owner_id', $ownerId)->where('status', '!=', 1)->count(),
                'crew' => User::where('owner_id', $ownerId)->whereIn('role', ['crew', 'captain'])->count(),
            ],
        ]);
    }

    public function getAnalyticsData()
    {
        $ownerId = $this->ownerId();
        [$yearFrom, $yearTo] = $this->currentYearRange();
        $saleIds = $this->ownerSaleIds($ownerId, $yearFrom, $yearTo);

        $totalWeight = (float) SaleDetail::whereIn('sale_id', $saleIds)->sum('weight');

        $fishAnalysis = SaleDetail::whereIn('sale_id', $saleIds)
            ->selectRaw('fish_name, SUM(weight) as total_weight, SUM(total_price) as total_value')
            ->groupBy('fish_name')
            ->get()
            ->map(function ($item) use ($totalWeight) {
                return [
                    'fish_name' => $item->fish_name,
                    'total_value' => round((float) $item->total_value, 2),
                    'total_weight' => round((float) $item->total_weight, 2),
                    'percentage' => $totalWeight > 0 ? round(($item->total_weight / $totalWeight) * 100, 2) : 0,
                ];
            });

        $totalTrips = Trip::where('owner_id', $ownerId)
            ->whereBetween(DB::raw('DATE(created_at)'), [$yearFrom, $yearTo])->count();
        $totalRevenue = (float) Sale::where('seller_type', 'owner')->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$yearFrom, $yearTo])->sum('total_price');
        $captainCount = User::where('owner_id', $ownerId)->where('role', 'captain')->count();

        return response()->json([
            'fishAnalysis' => $fishAnalysis,
            'metrics' => [
                'avgCatchPerTrip' => $totalTrips > 0 ? round($totalWeight / $totalTrips, 2) : 0,
                'avgRevenuePerTrip' => $totalTrips > 0 ? round($totalRevenue / $totalTrips, 2) : 0,
                'avgTripsPerCaptain' => $captainCount > 0 ? round($totalTrips / $captainCount, 2) : 0,
                'avgPricePerKg' => $totalWeight > 0 ? round($totalRevenue / $totalWeight, 2) : 0,
            ],
            'comparison' => $this->getMonthlyComparison(),
        ]);
    }

    /**
     * @return array<int, array{month: int, label: string, catch: float, revenue: float}>
     */
    private function getMonthlyComparison(): array
    {
        $ownerId = $this->ownerId();
        $year = now()->year;
        $saleIds = $this->ownerSaleIds($ownerId);

        $monthlyCatch = $this->sumByMonth(
            SaleDetail::whereIn('sale_id', $saleIds),
            'created_at',
            'weight',
            $year
        );
        $monthlyRevenue = $this->sumByMonth(
            Sale::where('seller_type', 'owner')->where('seller_id', $ownerId),
            'sale_datetime',
            'total_price',
            $year
        );

        $comparison = [];
        for ($m = 1; $m <= 12; $m++) {
            $comparison[] = [
                'month' => $m,
                'label' => $this->monthName($m),
                'catch' => round((float) ($monthlyCatch[$m] ?? 0), 2),
                'revenue' => round((float) ($monthlyRevenue[$m] ?? 0), 2),
            ];
        }

        return $comparison;
    }

    /**
     * Sum a column grouped by month of a date column for a given year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $query
     * @return \Illuminate\Support\Collection<int, float>
     */
    private function sumByMonth($query, string $dateColumn, string $sumColumn, int $year)
    {
        $isMySQL = DB::connection()->getDriverName() === 'mysql';
        $monthExpr = $isMySQL
            ? "MONTH($dateColumn)"
            : "CAST(strftime('%m', $dateColumn) AS INTEGER)";

        return $query
            ->selectRaw("$monthExpr as month, SUM($sumColumn) as total")
            ->whereYear($dateColumn, $year)
            ->groupBy(DB::raw($monthExpr))
            ->pluck('total', 'month');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, float>  $sales
     * @param  \Illuminate\Support\Collection<int, float>  $expenses
     * @return array<int, array{month: int, month_name: string, revenue: float, profit: float}>
     */
    private function monthlySeries($sales, $expenses): array
    {
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = (float) ($sales[$m] ?? 0);
            $expense = (float) ($expenses[$m] ?? 0);
            $monthly[] = [
                'month' => $m,
                'month_name' => $this->monthName($m),
                'revenue' => round($revenue, 2),
                'profit' => round($revenue - $expense, 2),
            ];
        }

        return $monthly;
    }
}
