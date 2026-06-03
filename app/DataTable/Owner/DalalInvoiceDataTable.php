<?php

namespace App\DataTable\Owner;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DalalInvoiceDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $ownerId = Auth::id();

        // Filter parameters
        $paymentStatus = $request->input('payment_status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $dalalId = $request->input('dalal_id');

        // Base query: sales sent by dalals to this owner
        $query = Sale::with(['trip.owner', 'trip.boat', 'seller', 'customer', 'paymentMethod'])
            ->whereHas('trip', function ($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->where('seller_type', 'dalal')
            ->whereNotNull('invoice_sent_at');

        // Apply filters
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($fromDate) {
            $query->whereDate('invoice_sent_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('invoice_sent_at', '<=', $toDate);
        }

        if ($dalalId) {
            $query->where('seller_id', $dalalId);
        }

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('invoice_number', function ($row) {
                return $row->number ?? '-';
            })
            ->addColumn('dalal_name', function ($row) {
                return $row->seller ? $row->seller->name : __('owner.unknown');
            })
            ->addColumn('boat_name', function ($row) {
                return $row->trip && $row->trip->boat ? $row->trip->boat->name : __('owner.trips.no_boat');
            })
            ->addColumn('customer_name', function ($row) {
                return $row->customer_name ?? ($row->customer ? $row->customer->name : '-');
            })
            ->addColumn('total_price', function ($row) {
                return number_format($row->total_price ?? 0, 2);
            })
            ->addColumn('commission', function ($row) {
                return number_format($row->commission_amount ?? 0, 2);
            })
            ->addColumn('labor', function ($row) {
                return number_format($row->labor_amount ?? 0, 2);
            })
            ->addColumn('net_owner_amount', function ($row) {
                return number_format($row->net_owner_amount ?? 0, 2);
            })
            ->addColumn('sent_date', function ($row) {
                return $row->invoice_sent_at ?
                    Carbon::parse($row->invoice_sent_at)->format('Y-m-d H:i') : '-';
            })
            ->addColumn('sale_date', function ($row) {
                return $row->sale_datetime ?
                    Carbon::parse($row->sale_datetime)->format('Y-m-d') : '-';
            })
            ->addColumn('payment_status', function ($row) {
                if ($row->payment_status == 1) {
                    return '<span class="badge bg-success">'.__('owner.status.paid').'</span>';
                } else {
                    return '<span class="badge bg-warning">'.__('owner.status.pending').'</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                return view('owner.dalal-invoices.partials.actions', ['invoice' => $row])->render();
            })
            ->rawColumns(['payment_status', 'actions'])
            ->make(true);
    }
}
