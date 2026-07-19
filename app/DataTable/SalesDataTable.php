<?php

namespace App\DataTable;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class SalesDataTable extends DataTables
{
    public function getData(Request $request)
    {
        Cache::forget('sidebar_sales_counts');

        if ($request->ajax()) {

            $query = Sale::with(['details.unit', 'paymentMethod']);

            if ($request->type == 'owner') {
                $query->where('seller_type', 'owner');
            } elseif ($request->type == 'dalal') {
                $query->where('seller_type', 'dalal');
            }

            // Apply filters from request
            if ($request->filled('seller_id')) {
                $query->where('seller_id', $request->seller_id);
            }

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->filled('payment_method_id')) {
                $query->where('payment_method_id', $request->payment_method_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('from_date')) {
                $from = Carbon::parse($request->from_date)->startOfDay();
                $query->where('sale_datetime', '>=', $from);
            }

            if ($request->filled('to_date')) {
                $to = Carbon::parse($request->to_date)->endOfDay();
                $query->where('sale_datetime', '<=', $to);
            }

            if ($request->filled('fish_id')) {
                $query->whereHas('details', function ($q) use ($request) {
                    $q->where('fish_id', $request->fish_id);
                });
            }

            if ($request->filled('boat_id')) {
                $query->whereHas('trip', function ($q) use ($request) {
                    $q->where('boat_id', $request->boat_id);
                });
            }

            // انسخ الاستعلام لحساب الإحصائيات
            $countQuery = clone $query;

            $totalItems = $countQuery->count();

            // لجلب المجموعات بشكل منفصل
            // حساب مجموع السعر
            $totalAmount = $query->sum('total_price');

            // جلب البيانات للـ DataTables
            $data = $query->get();
            $totalWeight = formatWeightByUnit($data->flatMap->details);

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('number', fn ($row) => $row->number)

                ->addColumn('status', function ($row) {
                    $text = Sale::statusText($row->status);
                    $class = match ($row->status) {
                        1 => 'badge bg-warning',
                        2 => 'badge bg-success',
                        default => 'badge bg-secondary',
                    };

                    return '<span class="'.$class.'">'.$text.'</span>';
                })

                ->addColumn('seller', function ($row) {
                    $name = optional($row->seller)->name ?? '---';
                    $type = $row->seller_type;

                    return match ($type) {
                        'dalal' => '<span class="badge bg-info">'.$name.' - '.__('admin.sales.dalal').'</span>',
                        'owner' => '<span class="badge bg-primary">'.$name.' - '.__('admin.sales.owner').'</span>',
                        default => '<span class="badge bg-secondary">'.$name.' - '.__('admin.sales.unknown').'</span>',
                    };
                })

                ->addColumn('customer', fn ($row) => $row->customer_name ?? optional($row->customer)->name)

                ->addColumn('payment_method', fn ($row) => optional($row->paymentMethod)->name)

                ->addColumn('total_weight', fn ($row) => formatWeightByUnit($row->details))

                ->addColumn('commission_rate', fn ($row) => $row->commission_rate.'%')
                ->addColumn('labor_rate', fn ($row) => $row->labor_rate.'%')

                ->addColumn('total_price', function ($row) {
                    return number_format($row->total_price, 2).' '.view('components.riyal-icon', ['size' => 'sm'])->render();
                })
                ->addColumn('net_owner_amount', function ($row) {
                    return number_format($row->net_owner_amount, 2).' '.view('components.riyal-icon', ['size' => 'sm'])->render();
                })
                ->addColumn('remaining_total', function ($row) {
                    return number_format($row->remaining_total, 2).' '.view('components.riyal-icon', ['size' => 'sm'])->render();
                })

                ->addColumn('date', function ($row) {
                    return $row->sale_datetime
                        ? Carbon::parse($row->sale_datetime)->format('Y-m-d H:i')
                        : '---';
                })

                ->addColumn('details', function ($row) {
                    return '<a href="'.route('admin.sales.show', $row->id).'" class="btn btn-sm btn-info">'.__('admin.actions.show').'</a>';
                })
                ->with([
                    'total_items' => $totalItems,
                    'total_weight' => $totalWeight,
                    'total_amount' => round($totalAmount, 2),
                ])
                ->rawColumns(['status', 'seller', 'total_price', 'net_owner_amount', 'remaining_total', 'details'])
                ->make(true);
        }
    }
}
