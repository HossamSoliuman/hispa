<?php

namespace App\DataTable\Owner\Report;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SalesReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $owner_id = auth()->user()->id; // الصيّاد الحالي

        if ($request->ajax()) {
            $query = Sale::with(['details', 'paymentMethod', 'seller', 'customer'])
                ->where('seller_type', 'owner')
                ->where('seller_id', $owner_id); // فلترة حسب الصيّاد

            // فلترة حسب التاريخ (على عمود البيع لا الإنشاء)
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween(DB::raw('DATE(sale_datetime)'), [
                    $request->start_date,
                    $request->end_date,
                ]);
            }

            // فلترة حسب الحالة
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->get();

            // Calculate totals for summary cards
            $totalSales = $data->count();
            $totalWeight = $data->sum(function ($row) {
                return $row->details->sum('weight');
            });
            $totalRevenue = $data->sum('total_price');
            $netOwnerAmount = $data->sum('net_owner_amount');

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

                    return match ($row->seller_type) {
                        'dalal' => '<span class="badge bg-info">'.$name.' - دلال</span>',
                        'owner' => '<span class="badge bg-primary">'.$name.' - صيّاد</span>',
                        default => '<span class="badge bg-secondary">'.$name.' - غير معروف</span>',
                    };
                })
                ->addColumn('customer', fn ($row) => $row->customer_name ?? optional($row->customer)->name)
                ->addColumn('payment_method', fn ($row) => optional($row->paymentMethod)->name)
                ->addColumn('total_weight', fn ($row) => $row->details->sum('weight').' كغم')
                ->addColumn('commission_rate', fn ($row) => $row->commission_rate.'%')
                ->addColumn('labor_rate', fn ($row) => $row->labor_rate.'%')
                ->addColumn('total_price', fn ($row) => number_format($row->total_price, 2))
                ->addColumn('net_owner_amount', fn ($row) => number_format($row->net_owner_amount, 2))
                ->addColumn('remaining_total', fn ($row) => number_format($row->remaining_total, 2))
                ->addColumn('date', function ($row) {
                    return $row->sale_datetime
                        ? Carbon::parse($row->sale_datetime)->format('Y-m-d h:i A')
                        : '---';
                })
                ->addColumn('details', function ($row) {
                    return '<a href="'.route('owner.sales.show', $row->id).'" class="btn btn-sm btn-info">عرض</a>';
                })
                ->rawColumns(['status', 'seller', 'details'])
                ->with([
                    'total_sales' => $totalSales,
                    'total_weight' => $totalWeight,
                    'total_revenue' => $totalRevenue,
                    'net_owner_amount' => $netOwnerAmount,
                ])
                ->make(true);
        }
    }
}
