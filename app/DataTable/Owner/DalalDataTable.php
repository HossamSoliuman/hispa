<?php

namespace App\DataTable\Owner;

use App\Models\DalalStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DalalDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $ownerId = auth()->user()->id;

        $statusFilter = $request->input('payment_status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $dalalIds = DalalStock::where('owner_id', $ownerId)
            ->where('status', 1)
            ->distinct()
            ->pluck('dalal_id')
            ->toArray();

        $query = DalalStock::with(['dalal', 'details'])
            ->where('owner_id', $ownerId)
            ->where('status', 1)
            ->whereIn('dalal_id', $dalalIds);

        $dalalData = $query->get();

        $salesQuery = Sale::select('seller_id',
            DB::raw('COUNT(*) as total_sales'),
            DB::raw('SUM(total_price) as total_sales_amount'),
            DB::raw('AVG(commission_rate) as avg_commission_rate'),
            DB::raw('AVG(labor_rate) as avg_labor_rate'),
            DB::raw('SUM(remaining_total) as total_dalal_commission'),
            DB::raw('SUM(net_owner_amount) as total_owner_amount')
        )
            ->whereIn('seller_id', $dalalIds)
            ->where('seller_type', 'dalal');

        if ($fromDate) {
            $salesQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $salesQuery->whereDate('created_at', '<=', $toDate);
        }

        $salesData = $salesQuery
            ->groupBy('seller_id')
            ->get()
            ->keyBy('seller_id');

        $filteredDalalIds = $salesData->keys()->toArray();

        $dalalData = $dalalData->filter(function ($dalal) use ($filteredDalalIds) {
            return in_array($dalal->dalal_id, $filteredDalalIds);
        })->values();

        $paymentsQuery = DB::table('payments')
            ->join('sales', 'sales.id', '=', 'payments.sale_id')
            ->whereIn('sales.seller_id', $filteredDalalIds)  // فقط الدلال المفلترين
            ->where('sales.seller_type', 'dalal');

        //        if ($fromDate) {
        //            $paymentsQuery->whereDate('payments.created_at', '>=', $fromDate);
        //        }
        //        if ($toDate) {
        //            $paymentsQuery->whereDate('payments.created_at', '<=', $toDate);
        //        }

        $paymentsData = $paymentsQuery
            ->select('sales.seller_id', DB::raw('SUM(payments.amount) as total_paid'))
            ->groupBy('sales.seller_id')
            ->get()
            ->keyBy('seller_id');

        if ($statusFilter) {
            $dalalData = $dalalData->filter(function ($row) use ($salesData, $paymentsData, $statusFilter) {
                $totalOwnerAmount = $salesData[$row->dalal_id]->total_owner_amount ?? 0;
                $paidAmountForDalal = $paymentsData[$row->dalal_id]->total_paid ?? 0;

                if ($statusFilter == 'paid') {
                    return $paidAmountForDalal >= $totalOwnerAmount && $totalOwnerAmount > 0;
                } elseif ($statusFilter == 'partial') {
                    return $paidAmountForDalal > 0 && $paidAmountForDalal < $totalOwnerAmount;
                } elseif ($statusFilter == 'unpaid') {
                    return $paidAmountForDalal == 0 && $totalOwnerAmount > 0;
                }

                return true;
            })->values();
        }

        $totalDalals = count($dalalIds);
        $activeDalals = $dalalData->count();

        $totalOwnerAmountAll = $salesData->sum('total_owner_amount');
        $paidAmount = $paymentsData->sum('total_paid');

        return DataTables::of($dalalData)
            ->addIndexColumn()
            ->addColumn('dalal_name', fn ($row) => $row->dalal->name ?? '-')
            ->addColumn('contact', fn ($row) => $row->dalal->phone ?? '-')
            ->addColumn('fish_count', fn ($row) => $row->details->count())
            ->addColumn('total_stock_weight', fn ($row) => number_format($row->details->sum('weight'), 2))
            ->addColumn('remaining_fish_count', function ($row) {
                $remainingCount = 0;
                foreach ($row->details as $detail) {
                    $totalWeight = $detail->weight;
                    $soldWeight = DB::table('sale_details')
                        ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->where('sale_details.dalal_stock_detail_id', $detail->id)
                        ->where('sales.seller_id', $row->dalal_id)
                        ->where('sales.seller_type', 'dalal')
                        ->sum('sale_details.weight');

                    if (($totalWeight - $soldWeight) > 0) {
                        $remainingCount++;
                    }
                }

                return $remainingCount;
            })
            ->addColumn('remaining_stock_weight', function ($row) {
                $totalWeight = $row->details->sum('weight');
                $soldWeight = 0;
                foreach ($row->details as $detail) {
                    $sold = DB::table('sale_details')
                        ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->where('sale_details.dalal_stock_detail_id', $detail->id)
                        ->where('sales.seller_id', $row->dalal_id)
                        ->where('sales.seller_type', 'dalal')
                        ->sum('sale_details.weight');
                    $soldWeight += $sold;
                }

                return number_format(max($totalWeight - $soldWeight, 0), 2);
            })
            ->addColumn('total_sales', function ($row) use ($salesData) {
                return $salesData[$row->dalal_id]->total_sales ?? 0;
            })
            ->addColumn('total_sales_amount', function ($row) use ($salesData) {
                return number_format($salesData[$row->dalal_id]->total_sales_amount ?? 0, 2);
            })
            ->addColumn('commission_rate', function ($row) use ($salesData) {
                $avg = $salesData[$row->dalal_id]->avg_commission_rate ?? 0;

                return number_format($avg, 2).'%';
            })
            ->addColumn('labor_rate', function ($row) use ($salesData) {
                $avg = $salesData[$row->dalal_id]->avg_labor_rate ?? 0;

                return number_format($avg, 2).'%';
            })
            ->addColumn('total_dalal_commission', function ($row) use ($salesData) {
                $sum = $salesData[$row->dalal_id]->total_dalal_commission ?? 0;

                return number_format($sum, 2);
            })
            ->addColumn('total_owner_amount', function ($row) use ($salesData) {
                $sum = $salesData[$row->dalal_id]->total_owner_amount ?? 0;

                return number_format($sum, 2);
            })
            ->addColumn('total_paid_amount', function ($row) use ($paymentsData) {
                return number_format($paymentsData[$row->dalal_id]->total_paid ?? 0, 2);
            })
            ->addColumn('payment_status', function ($row) use ($salesData, $paymentsData) {
                $totalOwnerAmount = $salesData[$row->dalal_id]->total_owner_amount ?? 0;
                $paidAmountForDalal = $paymentsData[$row->dalal_id]->total_paid ?? 0;

                if ($totalOwnerAmount <= 0) {
                    return '<span class="badge bg-secondary">'.__('owner.dalal.payment_status.none').'</span>';
                } elseif ($paidAmountForDalal >= $totalOwnerAmount) {
                    return '<span class="badge bg-success">'.__('owner.dalal.payment_status.fully_paid').'</span>';
                } elseif ($paidAmountForDalal > 0) {
                    return '<span class="badge bg-warning">'.__('owner.dalal.payment_status.partially_paid').'</span>';
                } else {
                    return '<span class="badge bg-danger">'.__('owner.dalal.payment_status.unpaid').'</span>';
                }
            })
            ->addColumn('date', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d');
            })
            ->addColumn('action', function ($row) {
                return view('owner.dalal.partials.actions', ['id' => $row->dalal_id])->render();
            })
            ->with([
                'total_dalals' => $totalDalals,
                'active_dalals' => $activeDalals,
                'amount_due' => number_format($totalOwnerAmountAll, 2),
                'paid_amount' => number_format($paidAmount, 2),
            ])
            ->rawColumns(['payment_status', 'action'])
            ->make(true);
    }

    public function getDalalPaymentData(Request $request)
    {
        $boatNameExpr = app()->getLocale() === 'en'
            ? "COALESCE(NULLIF(boats.name_en, ''), boats.name_ar)"
            : 'boats.name_ar';

        $query = Sale::select(
            'sales.*',
            DB::raw($boatNameExpr.' as boat_name')
        )
            ->leftJoin('trips', 'trips.id', '=', 'sales.trip_id')
            ->leftJoin('boats', 'boats.id', '=', 'trips.boat_id')
            ->with(['payments.payment_method'])
            ->where('seller_type', 'dalal');

        // Date filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('sales.created_at', [$request->from_date, $request->to_date]);
        }

        $data = $query->get();

        // Summary totals
        $totalSalesCount = $data->count();
        $totalOwnerAmount = $data->sum('net_owner_amount');
        $totalPaid = $data->sum(fn ($sale) => $sale->payments->sum('amount'));
        $totalRemaining = $totalOwnerAmount - $totalPaid;

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('sale_id', fn ($row) => $row->id)
            ->addColumn('boat', fn ($row) => $row->boat_name ?? '-')
            ->addColumn('sale_date', fn ($row) => $row->created_at->format('Y-m-d'))
            ->addColumn('total_price', fn ($row) => number_format($row->total_price, 2))
            ->addColumn('commission_rate', fn ($row) => number_format($row->commission_rate, 2).'%')
            ->addColumn('labor_rate', fn ($row) => number_format($row->labor_rate, 2).'%')
            ->addColumn('dalal_total', fn ($row) => number_format($row->remaining_total, 2))
            ->addColumn('owner_total', fn ($row) => number_format($row->net_owner_amount, 2))
            ->addColumn('paid_amount', fn ($row) => number_format($row->payments->sum('amount'), 2))
            ->addColumn('remaining', function ($row) {
                $remaining = $row->net_owner_amount - $row->payments->sum('amount');

                return number_format(max($remaining, 0), 2);
            })
            ->addColumn('payment_status', function ($row) {
                $paidSum = $row->payments->sum('amount');
                $remaining = $row->net_owner_amount - $paidSum;
                if ($remaining <= 0) {
                    return '<span class="badge bg-success">'.__('owner.status.paid').'</span>';
                } elseif ($paidSum > 0) {
                    return '<span class="badge bg-warning text-dark">'.__('owner.dalal.payment_status.partially_paid').'</span>';
                }

                return '<span class="badge bg-danger">'.__('owner.status.unpaid').'</span>';
            })
            ->addColumn('payments', function ($row) {
                if ($row->payments->isEmpty()) {
                    return '<small class="text-muted">'.__('owner.dalal.payments.no_payments').'</small>';
                }
                $list = '<ul class="list-group list-group-flush">';
                foreach ($row->payments as $payment) {
                    $list .= '<li class="list-group-item p-1">
                   <strong>'.__('owner.dalal.table.date').':</strong> '.optional(\Carbon\Carbon::parse($payment->paid_at))->format('Y-m-d').' |
                    <strong>'.__('owner.dalal.payments.amount').':</strong> '.number_format($payment->amount, 2).
                        ($payment->payment_method->name ? ' | <strong>'.__('owner.dalal.payments.payment_method').':</strong> '.$payment->payment_method->name : '').
                        '</li>';
                }
                $list .= '</ul>';

                return $list;
            })
            ->addColumn('actions', function ($row) {
                $paidSum = $row->payments->sum('amount');
                $remaining = $row->net_owner_amount - $paidSum;
                $buttons = '';

                $btnClassAdd = 'btn btn-primary btn-sm py-0 px-1 addPayment';
                $btnClassShow = 'btn btn-info btn-sm py-0 px-1 showPayments';

                if ($remaining <= 0) {
                    $buttons .= '<button class="'.$btnClassShow.'" data-id="'.$row->id.'"><i class="fas fa-eye"></i> '.__('owner.actions.show').'</button>';
                } elseif ($paidSum > 0) {
                    $buttons .= '<button class="'.$btnClassAdd.'" data-id="'.$row->id.'"><i class="fas fa-plus"></i> '.__('owner.dalal.payments.pay').'</button> ';
                    $buttons .= '<button class="'.$btnClassShow.'" data-id="'.$row->id.'"><i class="fas fa-eye"></i> '.__('owner.actions.show').'</button>';
                } else {
                    $buttons .= '<button class="'.$btnClassAdd.'" data-id="'.$row->id.'"><i class="fas fa-plus"></i> '.__('owner.dalal.payments.pay').'</button>';
                }

                return $buttons;
            })
            ->with([
                'total_sales_count' => $totalSalesCount,
                'total_owner_amount' => number_format($totalOwnerAmount, 2),
                'total_paid' => number_format($totalPaid, 2),
                'total_remaining' => number_format($totalRemaining, 2),
            ])
            ->rawColumns(['payment_status', 'payments', 'actions'])
            ->make(true);
    }
}
