<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait CatchStatistics
{
    public function getRevenueBySpecies()
    {
        $sellerId = auth()->user()->id;

        $data = DB::table('sale_details')
            ->select(
                'fish_name',
                DB::raw('SUM(sale_details.total_price) as total_revenue')
            )
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.seller_id', $sellerId)
            ->groupBy('fish_name')
            ->orderByDesc('total_revenue')
            ->get();

        return response()->json($data);
    }

    public function getWeightBySpecies()
    {
        $sellerId = auth()->user()->id;

        $data = DB::table('sale_details')
            ->select(
                'fish_name',
                DB::raw('SUM(weight) as total_weight_kg')
            )
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.seller_id', $sellerId)
            ->groupBy('fish_name')
            ->orderByDesc('total_weight_kg')
            ->get()
            ->map(function ($item) {
                // تحويل الوزن من كجم إلى رطل
                $item->total_weight_lb = round($item->total_weight_kg * 2.20462, 2);

                return $item;
            });

        return response()->json($data);
    }

    public function getMonthlyPerformance()
    {
        $sellerId = auth()->user()->id;

        $isMySQL = DB::connection()->getDriverName() === 'mysql';
        $monthExpr = $isMySQL
            ? "DATE_FORMAT(sales.created_at, '%Y-%m') as month"
            : "strftime('%Y-%m', sales.created_at) as month";
        $groupExpr = $isMySQL
            ? "DATE_FORMAT(sales.created_at, '%Y-%m')"
            : "strftime('%Y-%m', sales.created_at)";

        $data = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select(
                DB::raw($monthExpr),
                DB::raw('COUNT(DISTINCT sale_details.sale_id) as catch_count'),
                DB::raw('SUM(sale_details.total_price) as total_revenue'),
                DB::raw('SUM(sale_details.weight) as total_weight_kg')
            )
            ->where('sales.seller_id', $sellerId)
            ->groupBy(DB::raw($groupExpr))
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                // تحويل الوزن من كجم إلى رطل
                $item->total_weight_lb = round($item->total_weight_kg * 2.20462, 2);

                // صيغة الشهر بالعربي
                $monthNames = [
                    '01' => 'يناير', '02' => 'فبراير', '03' => 'مارس',
                    '04' => 'أبريل', '05' => 'مايو', '06' => 'يونيو',
                    '07' => 'يوليو', '08' => 'أغسطس', '09' => 'سبتمبر',
                    '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر',
                ];
                [$year, $month] = explode('-', $item->month);
                $item->month_name = $monthNames[$month].' '.$year;

                return $item;
            });

        return response()->json($data);
    }

    public function getStatsSummary()
    {
        $seller_id = auth()->user()->id;

        // بيانات الصيد حسب النوع
        $fishStats = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.seller_id', $seller_id)
            ->select(
                'sale_details.fish_id',
                'sale_details.fish_name',
                DB::raw('COUNT(DISTINCT sale_details.sale_id) as catch_count'),
                DB::raw('SUM(sale_details.weight) as total_weight_kg'),
                DB::raw('SUM(sale_details.total_price) as total_revenue')
            )
            ->groupBy('sale_details.fish_id', 'sale_details.fish_name')
            ->get();

        $average_weight_kg = $fishStats->avg('total_weight_kg');

        $fishStats->transform(function ($item) use ($average_weight_kg) {
            $item->performance = 'ضعيف';
            if ($item->total_weight_kg > $average_weight_kg * 1.2) {
                $item->performance = 'ممتاز';
            } elseif ($item->total_weight_kg >= $average_weight_kg * 0.8) {
                $item->performance = 'جيد';
            }

            return $item;
        });

        $bestFish = $fishStats->sortByDesc('total_weight_kg')->first();

        // الموقع الأكثر إنتاجًا (حسب عدد الرحلات في كل ميناء)
        $topPort = DB::table('sales')
            ->join('trips', 'sales.trip_id', '=', 'trips.id')
            ->join('ports', 'trips.port_id', '=', 'ports.id')
            ->where('sales.seller_id', $seller_id)
            ->select('ports.name as port_name', DB::raw('COUNT(DISTINCT sales.trip_id) as trip_count'))
            ->groupBy('ports.name')
            ->orderByDesc('trip_count')
            ->first();

        // أداء القوارب (عدد الرحلات لكل قارب)
        $boatNameExpr = app()->getLocale() === 'en'
            ? "COALESCE(NULLIF(boats.name_en, ''), boats.name_ar)"
            : 'boats.name_ar';

        $boatsStats = DB::table('sales')
            ->join('trips', 'sales.trip_id', '=', 'trips.id')
            ->join('boats', 'trips.boat_id', '=', 'boats.id')
            ->where('sales.seller_id', $seller_id)
            ->select(DB::raw($boatNameExpr.' as boat_name'), DB::raw('COUNT(DISTINCT sales.trip_id) as trip_count'))
            ->groupBy(DB::raw($boatNameExpr))
            ->orderByDesc('trip_count')
            ->get();

        $bestBoat = $boatsStats->sortByDesc('trip_count')->first();

        // مؤشرات التشغيل العامة
        $activeBoatsCount = $boatsStats->count();
        $activePortsCount = DB::table('trips')
            ->join('sales', 'sales.trip_id', '=', 'trips.id')
            ->where('sales.seller_id', $seller_id)
            ->distinct('trips.port_id')
            ->count('trips.port_id');

        $distinctFishTypes = $fishStats->count();

        $totalRevenue = $fishStats->sum('total_revenue');
        $totalWeightKg = $fishStats->sum('total_weight_kg');
        $totalTrips = DB::table('sales')
            ->where('seller_id', $seller_id)
            ->distinct('trip_id')
            ->count('trip_id');

        $revenuePerTrip = $totalTrips > 0 ? round($totalRevenue / $totalTrips, 2) : 0;
        $weightPerTrip = $totalTrips > 0 ? round($totalWeightKg / $totalTrips, 2) : 0;
        $avgPricePerKg = $totalWeightKg > 0 ? round($totalRevenue / $totalWeightKg, 2) : 0;

        return response()->json([
            'best_fish' => [
                'name' => $bestFish->fish_name ?? '-',
                'revenue' => number_format($bestFish->total_revenue ?? 0, 2).' ر.س',
                'catch_count' => $bestFish->catch_count ?? 0,
                'weight_kg' => number_format($bestFish->total_weight_kg ?? 0, 2).' كجم',
                'performance' => $bestFish->performance ?? '-',
            ],
            'top_port' => [
                'name' => $topPort->port_name ?? '-',
                'trip_count' => $topPort->trip_count ?? 0,
            ],
            'boats_performance' => [
                'boats' => $boatsStats->map(fn ($b) => ['name' => $b->boat_name, 'trips' => $b->trip_count]),
                'best_boat' => [
                    'name' => $bestBoat->boat_name ?? '-',
                    'trips' => $bestBoat->trip_count ?? 0,
                ],
            ],
            'performance_indicators' => [
                'total_trips' => $totalTrips,
                'active_boats' => $activeBoatsCount,
                'active_ports' => $activePortsCount,
                'distinct_fish_types' => $distinctFishTypes,
                'total_revenue' => number_format($totalRevenue, 2).' ر.س',
                'total_weight_kg' => number_format($totalWeightKg, 2).' كجم',
                'revenue_per_trip' => $revenuePerTrip.' ر.س / رحلة',
                'weight_per_trip_kg' => $weightPerTrip.' كجم / رحلة',
                'avg_price_per_kg' => $avgPricePerKg.' ر.س / كجم',
            ],
        ]);
    }
}
