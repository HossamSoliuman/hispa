<?php

namespace App\DataTable\Owner;

use App\Models\DalalStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DalalStockDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $ownerId = auth()->user()->id;

            $dalalIds = DalalStock::where('owner_id', $ownerId)
                ->where('status', 1)
                ->distinct()
                ->pluck('dalal_id')
                ->toArray();

            if (empty($dalalIds)) {
                return DataTables::of(collect([]))
                    ->with([
                        'total_boats' => 0,
                        'total_dalals' => 0,
                        'total_weight' => 0,
                        'total_sold' => 0,
                        'total_remaining' => 0,
                        'total_revenue' => 0,
                    ])
                    ->make(true);
            }

            $stocks = DB::table('dalal_stocks as ds')
                ->join('dalal_stock_details as dsd', 'dsd.dalal_stock_id', '=', 'ds.id')
                ->join('trips as t', 't.id', '=', 'ds.trip_id')
                ->join('boats as b', 'b.id', '=', 't.boat_id')
                ->where('ds.owner_id', $ownerId)
                ->whereIn('ds.dalal_id', $dalalIds)
                ->select(
                    'b.id as boat_id',
                    'b.name_ar as boat_name',
                    't.id as trip_id',
                    't.name as trip_name',
                    DB::raw('SUM(dsd.weight) as weight_in_stock')
                )
                ->groupBy('b.id', 'b.name_ar', 't.id', 't.name')
                ->orderBy('t.id', 'desc')
                ->get();

            $salesData = DB::table('sales as s')
                ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
                ->whereIn('s.seller_id', $dalalIds)
                ->where('s.seller_type', 'dalal')
                ->select(
                    's.trip_id',
                    DB::raw('SUM(sd.weight) as sold_weight'),
                    DB::raw('SUM(s.total_price) as total_revenue')
                )
                ->groupBy('s.trip_id')
                ->get()
                ->keyBy('trip_id');

            // دمج بيانات البيع مع المخزون
            $stocks->map(function ($row) use ($salesData) {
                $sale = $salesData->get($row->trip_id);
                $sold_weight = $sale->sold_weight ?? 0;
                $row->total_weight = $row->weight_in_stock + $sold_weight; // الوزن قبل البيع
                $row->remaining_weight = $row->weight_in_stock;           // المتبقي
                $row->sold_weight = $sold_weight;                         // المباع
                $row->total_revenue = $sale->total_revenue ?? 0;

                return $row;
            });

            $totalBoats = $stocks->pluck('boat_name')->unique()->count();
            $totalDalals = DalalStock::where('owner_id', $ownerId)->distinct('dalal_id')->count('dalal_id');
            $totalWeight = $stocks->sum('total_weight');
            $totalRemaining = $stocks->sum('remaining_weight');
            $totalSold = $stocks->sum('sold_weight');
            $totalRevenue = $stocks->sum('total_revenue');

            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('boat_name', fn ($row) => $row->boat_name)
                ->addColumn('trip_id', fn ($row) => $row->trip_id)
                ->addColumn('trip_name', fn ($row) => $row->trip_name)
                ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' كجم')
                ->addColumn('sold_weight', fn ($row) => number_format($row->sold_weight, 2).' كجم')
                ->addColumn('remaining_weight', fn ($row) => number_format($row->remaining_weight, 2).' كجم')
                ->addColumn('total_revenue', fn ($row) => number_format($row->total_revenue, 2).' ر.س')
                ->addColumn('details', function ($row) {
                    return '<a href="'.route('owner.dalal.show-boat', $row->boat_id).'" class="btn btn-sm btn-info">عرض</a>';
                })
                ->with([
                    'total_boats' => $totalBoats,
                    'total_dalals' => $totalDalals,
                    'total_weight' => number_format($totalWeight, 2),
                    'total_sold' => number_format($totalSold, 2),
                    'total_remaining' => number_format($totalRemaining, 2),
                    'total_revenue' => number_format($totalRevenue, 2),
                ])
                ->rawColumns(['details'])
                ->make(true);
        }
    }

    public function showBoatData(Request $request, $boatId)
    {
        $ownerId = auth()->user()->id;

        // جلب المخزون لكل رحلة مرتبطة بالقارب
        $stocks = DB::table('dalal_stocks as ds')
            ->join('dalal_stock_details as dsd', 'dsd.dalal_stock_id', '=', 'ds.id')
            ->join('trips as t', 't.id', '=', 'ds.trip_id')
            ->join('boats as b', 'b.id', '=', 't.boat_id')
            ->where('ds.owner_id', $ownerId)
            ->where('t.boat_id', $boatId)
            ->select(
                'b.name_ar as boat_name',
                'b.id as boat_id',
                't.id as trip_id',
                't.name as trip_name',
                't.number as trip_number', // إضافة رقم الرحلة من جدول الرحلات
                DB::raw('SUM(dsd.weight) as weight_in_stock')
            )
            ->groupBy('b.id', 'b.name_ar', 't.id', 't.name', 't.number')
            ->get();

        // المبيعات لكل رحلة
        $salesData = DB::table('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->where('s.seller_type', 'dalal')
            ->whereIn('s.trip_id', $stocks->pluck('trip_id'))
            ->select(
                's.trip_id',
                DB::raw('SUM(sd.weight) as sold_weight'),
                DB::raw('SUM(s.total_price) as total_revenue')
            )
            ->groupBy('s.trip_id')
            ->get()
            ->keyBy('trip_id');

        // حساب الأوزان والإيرادات لكل رحلة وربط رقم الرحلة واسم الرحلة
        $stocks->map(function ($row) use ($salesData) {
            $sale = $salesData->get($row->trip_id);
            $sold_weight = $sale->sold_weight ?? 0;

            $row->total_weight = $row->weight_in_stock + $sold_weight;
            $row->remaining_weight = $row->weight_in_stock;
            $row->sold_weight = $sold_weight;
            $row->total_revenue = $sale->total_revenue ?? 0;

            return $row;
        });

        // الملخص
        $boatName = $stocks->first()?->boat_name ?? 'غير محدد';
        $tripCount = $stocks->count();
        $totalDalals = DalalStock::where('owner_id', $ownerId)
            ->whereIn('trip_id', $stocks->pluck('trip_id'))
            ->distinct('dalal_id')
            ->count('dalal_id');

        $totalWeight = $stocks->sum('total_weight');
        $totalRemaining = $stocks->sum('remaining_weight');
        $totalSold = $stocks->sum('sold_weight');
        $totalRevenue = $stocks->sum('total_revenue');

        return DataTables::of($stocks)
            ->addIndexColumn()
            ->addColumn('trip_number', fn ($row) => $row->trip_number)
            ->addColumn('trip_name', fn ($row) => $row->trip_name)
            ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' كجم')
            ->addColumn('sold_weight', fn ($row) => number_format($row->sold_weight, 2).' كجم')
            ->addColumn('remaining_weight', fn ($row) => number_format($row->remaining_weight, 2).' كجم')
            ->addColumn('total_revenue', fn ($row) => number_format($row->total_revenue, 2).' ر.س')
            ->addColumn('details', fn ($row) => '<a href="'.route('owner.dalal.show-trip', $row->trip_id).'" class="btn btn-sm btn-info">عرض</a>'
            )
            ->with([
                'boatname' => $boatName,
                'trip_count' => $tripCount,
                'total_dalals' => $totalDalals,
                'total_weight' => number_format($totalWeight, 2),
                'total_sold' => number_format($totalSold, 2),
                'total_remaining' => number_format($totalRemaining, 2),
                'total_revenue' => number_format($totalRevenue, 2),
            ])
            ->rawColumns(['details'])
            ->make(true);
    }

    public function showTripData(Request $request, $trip_id)
    {
        $ownerId = auth()->user()->id;

        // جلب المخزون لكل رحلة محددة
        $stocks = DB::table('dalal_stocks as ds')
            ->join('dalal_stock_details as dsd', 'dsd.dalal_stock_id', '=', 'ds.id')
            ->join('trips as t', 't.id', '=', 'ds.trip_id')
            ->join('boats as b', 'b.id', '=', 't.boat_id')
            ->join('users as d', 'd.id', '=', 'ds.dalal_id') // ربط الدلال
            ->where('ds.owner_id', $ownerId)
            ->where('ds.trip_id', $trip_id) // تصحيح هنا
            ->select(
                'b.name_ar as boat_name',
                'b.id as boat_id',
                'd.name as dalal_name',
                'ds.id as stock_id',
                DB::raw('SUM(dsd.weight) as weight_in_stock'),
                'ds.trip_id',
                'ds.dalal_id'
            )
            ->groupBy('b.id', 'b.name_ar', 'd.id', 'd.name', 'ds.id', 'ds.trip_id')
            ->get();

        // المبيعات لكل دلال في هذه الرحلة فقط
        $salesData = DB::table('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->where('s.seller_type', 'dalal')
            ->where('s.trip_id', $trip_id) // تصحيح هنا
            ->whereIn('s.seller_id', $stocks->pluck('dalal_id'))
            ->select(
                's.seller_id',
                's.trip_id',
                DB::raw('SUM(sd.weight) as sold_weight'),
                DB::raw('SUM(s.total_price) as total_revenue')
            )
            ->groupBy('s.seller_id', 's.trip_id')
            ->get()
            ->keyBy(fn ($row) => $row->seller_id.'_'.$row->trip_id);

        // حساب الأوزان والإيرادات لكل دلال
        $stocks->map(function ($row) use ($salesData) {
            $key = $row->dalal_id.'_'.$row->trip_id;
            $sale = $salesData->get($key);
            $sold_weight = $sale->sold_weight ?? 0;

            $row->total_weight = $row->weight_in_stock + $sold_weight;
            $row->remaining_weight = $row->weight_in_stock;
            $row->sold_weight = $sold_weight;
            $row->total_revenue = $sale->total_revenue ?? 0;

            return $row;
        });

        // الملخص
        $boatName = $stocks->first()?->boat_name ?? 'غير محدد';
        $totalDalals = $stocks->pluck('dalal_name')->unique()->count();

        $totalWeight = $stocks->sum('total_weight');
        $totalRemaining = $stocks->sum('remaining_weight');
        $totalSold = $stocks->sum('sold_weight');
        $totalRevenue = $stocks->sum('total_revenue');

        return DataTables::of($stocks)
            ->addIndexColumn()
            ->addColumn('dalal_name', fn ($row) => $row->dalal_name)
            ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' كجم')
            ->addColumn('sold_weight', fn ($row) => number_format($row->sold_weight, 2).' كجم')
            ->addColumn('remaining_weight', fn ($row) => number_format($row->remaining_weight, 2).' كجم')
            ->addColumn('total_revenue', fn ($row) => number_format($row->total_revenue, 2).' ر.س')
            ->addColumn('details', fn ($row) => '<a href="'.route('owner.dalal.show-dalal', $trip_id).'" class="btn btn-sm btn-info">عرض</a>'
            )
            ->with([
                'boatname' => $boatName,
                'total_dalals' => $totalDalals,
                'total_weight' => number_format($totalWeight, 2),
                'total_sold' => number_format($totalSold, 2),
                'total_remaining' => number_format($totalRemaining, 2),
                'total_revenue' => number_format($totalRevenue, 2),
            ])
            ->rawColumns(['details'])
            ->make(true);
    }

    public function showDalalTransaction(Request $request, $trip_id)
    {
        $ownerId = auth()->user()->id;

        // جلب المبيعات فقط للمخزون المخصص للصيّاد الحالي
        $salesData = Sale::where('seller_type', 'dalal')
            ->where('trip_id', $trip_id)
            ->whereHas('details.dalalStockDetail', function ($q) use ($ownerId) {
                $q->whereHas('dalalStock', function ($q2) use ($ownerId) {
                    $q2->where('owner_id', $ownerId);
                });
            })
            ->with(['details.dalalStockDetail.dalalStock', 'seller'])
            ->get();

        // جلب المدفوعات لكل عملية
        $paymentsData = DB::table('payments')
            ->select('sale_id', DB::raw('SUM(amount) as total_paid'))
            ->whereIn('sale_id', $salesData->pluck('id'))
            ->groupBy('sale_id')
            ->get()
            ->keyBy('sale_id');

        // إحصائيات عامة
        $stats = [
            'dalal_name' => $salesData->first()?->seller->name ?? '-',
            'total_fish_count' => $salesData->sum(fn ($s) => $s->details->count()),
            'total_sales_amount' => $salesData->sum('total_price'),
            'total_stock_weight' => $salesData->sum(fn ($s) => $s->details->sum('weight')),
            'total_remaining_weight' => $salesData->sum(fn ($s) => $s->details->sum(fn ($d) => max($d->dalalStockDetail->weight - $d->weight, 0))),
            'total_sold_weight' => $salesData->sum(fn ($s) => $s->details->sum('weight')),
        ];

        return DataTables::of($salesData)
            ->addIndexColumn()
            ->addColumn('number', fn ($sale) => $sale->number)
            ->addColumn('date', fn ($sale) => $sale->created_at?->format('Y-m-d') ?? '-')
            ->addColumn('dalal_name', fn ($sale) => $sale->seller->name ?? '-')
            ->addColumn('contact', fn ($sale) => $sale->seller->phone ?? '-')
            ->addColumn('fish_count', fn ($sale) => $sale->details->count())
            ->addColumn('total_stock_weight', fn ($sale) => number_format($sale->details->sum('weight'), 2))
            ->addColumn('remaining_stock_weight', function ($sale) {
                $totalWeight = $sale->details->sum(fn ($d) => $d->dalalStockDetail->weight ?? 0);
                $soldWeight = $sale->details->sum('weight');

                return number_format(max($totalWeight - $soldWeight, 0), 2);
            })
            ->addColumn('total_sales_amount', fn ($sale) => number_format($sale->total_price, 2))
            ->addColumn('commission_rate', fn ($sale) => number_format($sale->commission_rate ?? 0, 2).'%')
            ->addColumn('labor_rate', fn ($sale) => number_format($sale->labor_rate ?? 0, 2).'%')
            ->addColumn('total_dalal_commission', fn ($sale) => number_format($sale->remaining_total, 2))
            ->addColumn('total_owner_amount', fn ($sale) => number_format($sale->net_owner_amount, 2))
            ->addColumn('total_paid_amount', function ($sale) use ($paymentsData) {
                return number_format($paymentsData[$sale->id]->total_paid ?? 0, 2);
            })
            ->addColumn('payment_status', function ($sale) use ($paymentsData) {
                $totalOwnerAmount = $sale->net_owner_amount;
                $paidAmount = $paymentsData[$sale->id]->total_paid ?? 0;

                if ($totalOwnerAmount <= 0) {
                    return '<span class="badge bg-secondary">لا يوجد</span>';
                }
                if ($paidAmount >= $totalOwnerAmount) {
                    return '<span class="badge bg-success">مدفوع كلي</span>';
                }
                if ($paidAmount > 0) {
                    return '<span class="badge bg-warning">مدفوع جزئي</span>';
                }

                return '<span class="badge bg-danger">غير مدفوع</span>';
            })
            ->addColumn('action', function ($sale) {
                return '<button class="btn btn-sm btn-outline-primary showSaleDetailsBtn" data-id="'.$sale->id.'">
                <i class="bi bi-eye"></i> عرض
            </button>';
            })

            ->with($stats)
            ->rawColumns(['payment_status', 'action'])
            ->make(true);
    }
}
