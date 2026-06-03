<?php

namespace App\DataTable\Report;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class SalesReportDataTable extends DataTables
{
    public function getData(Request $request)
    {

        if ($request->ajax()) {
            $query = Sale::with(['details', 'paymentMethod', 'seller', 'customer']);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date,
                ]);
            }

            // فلترة حسب الحالة
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            $data = $query->get();

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
                        'dalal' => '<span class="badge bg-info">'.$name.' - '.__('admin.sales.dalal').'</span>',
                        'owner' => '<span class="badge bg-primary">'.$name.' - '.__('admin.sales.owner').'</span>',
                        default => '<span class="badge bg-secondary">'.$name.' - '.__('admin.sales.unknown').'</span>',
                    };
                })
                ->addColumn('customer', fn ($row) => $row->customer_name ?? optional($row->customer)->name)
                ->addColumn('payment_method', fn ($row) => optional($row->paymentMethod)->name)
                ->addColumn('total_weight', fn ($row) => $row->details->sum('weight').' '.__('admin.units.kg'))
                ->addColumn('commission_rate', fn ($row) => $row->commission_rate.'%')
                ->addColumn('labor_rate', fn ($row) => $row->labor_rate.'%')
                ->addColumn('total_price', fn ($row) => number_format($row->total_price, 2))
                ->addColumn('net_owner_amount', fn ($row) => number_format($row->net_owner_amount, 2))
                ->addColumn('remaining_total', fn ($row) => number_format($row->remaining_total, 2))
                ->addColumn('date', function ($row) {
                    if (! $row->sale_datetime) {
                        return '---';
                    }
                    // Use Hijri formatting helper if available; include time
                    try {
                        return formatHijriDate($row->sale_datetime, 'dd/MM/yyyy HH:mm');
                    } catch (\Throwable $e) {
                        return Carbon::parse($row->sale_datetime)->format('Y-m-d h:i A');
                    }
                })
                ->addColumn('details', function ($row) {
                    return '<a href="'.route('admin.sales.show', $row->id).'" class="btn btn-sm btn-info"> '.__('admin.actions.show').'</a>';
                })
                ->rawColumns(['status', 'seller', 'details'])
                ->make(true);
        }
    }
}
