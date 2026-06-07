<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\FishQuantityStock;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // إجمالي الإيرادات (كل الوقت)
        $totalRevenue = Sale::sum('total_price');

        // الإيرادات الشهر الحالي
        $currentMonthRevenue = Sale::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->sum('total_price');

        // الإيرادات الشهر الماضي
        $previousMonthRevenue = Sale::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ])->sum('total_price');

        // نسبة النمو/النقصان
        $percentageChange = 0;
        if ($previousMonthRevenue > 0) {
            $percentageChange = (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100;
        }

        // مجموع الأوزان (كجم)
        $totalCatch = 0;
        // --------------------------------
        // متوسط السعر لكل كغم
        $averagePricePerKg = $totalCatch > 0 ? $totalRevenue / $totalCatch : 0;
        // عدد القوارب النشطة
        $activeBoats = Boat::where('status', 1)->count();

        // عدد الرحلات المكتملة
        $completedTrips = Trip::where('status', 8)->count();

        // -----------------------
        // إجمالي التكاليف (غير معروف عندك الجدول بالضبط، بفرض اسمه expenses)
        $totalCosts = Expense::sum('final_price');

        // حساب الربح
        $profit = $totalRevenue - $totalCosts;

        // حساب هامش الربح %
        $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;

        return view('owner.dashboard.index', compact(
            'totalRevenue',
            'percentageChange',
            'totalCatch',
            'averagePricePerKg',
            'profitMargin',
            'profit',
            'activeBoats',
            'completedTrips'
        ));
    }

    public function overviewData()
    {
        $year = now()->year;
        $isMySQL = DB::connection()->getDriverName() === 'mysql';
        $monthExpr = $isMySQL ? 'MONTH(created_at)' : "CAST(strftime('%m', created_at) AS INTEGER)";

        $monthlySales = Sale::selectRaw("$monthExpr as month, SUM(net_owner_amount) as revenue")
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw($monthExpr))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyExpenses = Expense::selectRaw("$monthExpr as month, SUM(final_price) as expenses")
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw($monthExpr))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $arabicMonths = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = (float) ($monthlySales->get($m)?->revenue ?? 0);
            $expenses = (float) ($monthlyExpenses->get($m)?->expenses ?? 0);
            $monthly[] = [
                'month' => $m,
                'month_name' => $arabicMonths[$m],
                'revenue' => round($revenue, 2),
                'profit' => round($revenue - $expenses, 2),
            ];
        }

        $catchComposition = DB::table('sale_details')
            ->selectRaw('fish_name, SUM(weight * price_per_kilo) as total_value')
            ->groupBy('fish_name')
            ->get();

        $totalValue = $catchComposition->sum('total_value');
        $catchComposition->map(function ($item) use ($totalValue) {
            $item->percentage = $totalValue > 0 ? round(($item->total_value / $totalValue) * 100, 1) : 0;

            return $item;
        });

        $totalCatchKg = DB::table('sale_details')->sum('weight');
        $totalRevenue = Sale::sum('net_owner_amount');
        $totalExpenses = Expense::sum('final_price');

        // الأنشطة الأخيرة (آخر 5 أحداث)
        $activities = Trip::latest()->take(5)->get();

        return response()->json([
            'monthly' => $monthly,
            'catchComposition' => $catchComposition,
            'activities' => $activities,
            'totalCatchKg' => (float) $totalCatchKg,
            'summary' => [
                'revenue' => round((float) $totalRevenue, 2),
                'profit' => round((float) ($totalRevenue - $totalExpenses), 2),
                'avgPricePerKg' => $totalCatchKg > 0 ? round((float) $totalRevenue / (float) $totalCatchKg, 2) : 0,
            ],
        ]);
    }

    public function getRecentActivities()
    {
        $activities = [];

        // Example: Trips
        $trips = Trip::latest()->take(5)->get();
        foreach ($trips as $trip) {
            $activities[] = [
                'icon' => 'bi-clipboard-check text-success',
                'message' => "اكتمال رحلة البحار {$trip->boat_name} - {$trip->trip_number}",
                'time' => $trip->updated_at->diffForHumans(),
                'badge_class' => 'bg-success',
            ];
        }

        // Example: Sales
        $sales = Sale::latest()->take(5)->get();
        foreach ($sales as $sale) {
            $activities[] = [
                'icon' => 'bi-cash-coin text-success',
                'message' => "بيع لمطعم {$sale->customer_name} - ر.س".number_format($sale->total_price),
                'time' => $sale->updated_at->diffForHumans(),
                'badge_class' => 'bg-success',
            ];
        }

        // Stock alerts
        $stocks = FishQuantityStock::where('quantity', '<', 10)->latest()->take(5)->get();
        foreach ($stocks as $stock) {
            $activities[] = [
                'icon' => 'bi-thermometer-low text-warning',
                'message' => "مخزون {$stock->item_name} منخفض (متبقي {$stock->quantity})",
                'time' => $stock->updated_at->diffForHumans(),
                'badge_class' => 'bg-warning text-dark',
            ];
        }

        // Sort by latest time
        $activities = collect($activities)->sortByDesc('time')->take(5);

        return response()->json($activities);
    }

    public function summary()
    {
        // Get total revenue (example)
        $revenue = Sale::sum('total_price'); // adjust according to your table

        // Get total expenses
        $expenses = Expense::sum('final_price');

        // Get dynamic categories and sum by each
        $categories = Category::withSum('expenses', 'final_price')->get();
        // This assumes you have `expenses()` relationship in Category model:
        // public function expenses() { return $this->hasMany(Expense::class); }

        // Calculate profit and margin
        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;

        return response()->json([
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'margin' => $margin,
            'categories' => $categories, // return category names and sums
        ]);
    }

    public function getOperationsData()
    {
        // جلب كل الكباتن
        $captains = User::where('role', 'captain')->get();

        $sailorsData = [];

        foreach ($captains as $captain) {
            $trips = Trip::where('captain_id', $captain->id)->get();

            $totalTrips = $trips->count();

            // Sum total weight from trip_details
            $totalCatch = 0;

            // Sum total revenue from sales table
            $totalRevenue = Sale::whereIn('trip_id', $trips->pluck('id'))->sum('total_price');

            // Example efficiency calculation
            $efficiency = $totalTrips > 0 ? round(($totalRevenue / ($totalTrips * 1000)) * 100, 2) : 0;

            $sailorsData[] = [
                'name' => $captain->name,
                'trips' => $totalTrips,
                'catch' => $totalCatch,
                'revenue' => $totalRevenue,
                'efficiency' => $efficiency,
            ];
        }

        // Sort by revenue descending and take top 5
        $topCaptains = collect($sailorsData)
            ->sortByDesc('revenue')
            ->take(5)
            ->values(); // reset array keys

        // Example daily operations
        $dailyOperations = Trip::selectRaw('DATE(created_at) as date, COUNT(*) as trips_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'sailors' => $topCaptains,
            'dailyOperations' => $dailyOperations,
        ]);
    }

    public function getAnalyticsData()
    {
        // إجمالي الوزن لجميع الأنواع
        $totalWeight = SaleDetail::sum('weight');

        // تحليل الأنواع: إجمالي السعر والوزن لكل نوع سمك
        $fishAnalysis = SaleDetail::selectRaw('fish_name, SUM(weight) as total_weight, SUM(total_price) as total_value')
            ->groupBy('fish_name')
            ->get()
            ->map(function ($item) use ($totalWeight) {
                $percentage = $totalWeight > 0 ? round(($item->total_weight / $totalWeight) * 100, 2) : 0;

                return [
                    'fish_name' => $item->fish_name,
                    'total_value' => $item->total_value,
                    'total_weight' => $item->total_weight,
                    'percentage' => $percentage,
                ];
            });

        // المؤشرات الرئيسية
        $totalTrips = Trip::count();
        $totalRevenue = Sale::sum('total_price');
        $avgCatchPerTrip = 0;
        $avgRevenuePerTrip = 0;
        if ($totalTrips > 0) {
            $avgCatch = $totalWeight / $totalTrips;
            $avgRevenue = $totalRevenue / $totalTrips;
            $avgCatchPerTrip = floor($avgCatch);
            $avgRevenuePerTrip = floor($avgRevenue);
        }

        $avgTripsPerCaptain = User::where('role', 'captain')->count() > 0
            ? floor($totalTrips / User::where('role', 'captain')->count())
            : 0;
        $avgPricePerKg = $totalWeight > 0 ? floor($totalRevenue / $totalWeight) : 0;

        return response()->json([
            'fishAnalysis' => $fishAnalysis,
            'metrics' => [
                'avgCatchPerTrip' => $avgCatchPerTrip,
                'avgRevenuePerTrip' => $avgRevenuePerTrip,
                'avgTripsPerCaptain' => $avgTripsPerCaptain,
                'avgPricePerKg' => $avgPricePerKg,
            ],
        ]);
    }
}
