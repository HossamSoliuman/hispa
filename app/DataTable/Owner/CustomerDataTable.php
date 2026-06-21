<?php

namespace App\DataTable\Owner;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $owner = auth()->user();

            $query = Customer::where('owner_id', $owner->id)
                ->withCount('sales')
                ->withSum('sales as total_purchases', 'total_price')
                ->withSum('sales as total_remaining', 'remaining_total')
                ->withMax('sales as last_order_at', 'sale_datetime')
                ->orderBy('created_at', 'desc');

            $data = $query->get();

            $customer_count = $data->count();
            $customer_count_active = $data->where('status', 1)->count();
            $total_sales = $data->sum('total_purchases');
            $total_orders = $data->sum('sales_count');

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($data) {
                    return $data->type ?: '—';
                })
                ->addColumn('total_sales', function ($data) {
                    return number_format($data->total_purchases ?? 0, 2);
                })
                ->addColumn('total_remaining', function ($data) {
                    $remaining = $data->total_remaining ?? 0;
                    $class = $remaining > 0 ? 'text-danger fw-bold' : 'text-success';

                    return '<span class="'.$class.'">'.number_format($remaining, 2).'</span>';
                })
                ->addColumn('order_count', function ($data) {
                    return $data->sales_count;
                })
                ->addColumn('last_order', function ($data) {
                    return $data->last_order_at ? date('Y-m-d', strtotime($data->last_order_at)) : '—';
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-success">'.__('owner.status.active').'</span>'
                        : '<span class="badge bg-danger">'.__('owner.status.inactive').'</span>';
                })
                ->addColumn('action', function (Customer $customer) {
                    $btn = '<a href="'.route('owner.customers.show', $customer->id).'"
                            class="btn btn-outline-info btn-sm mx-1" title="'.__('owner.actions.show').'">
                            <i class="bi bi-eye"></i>
                        </a>';

                    $btn .= '<a data-bs-effect="effect-scale" data-bs-toggle="modal" href="#modelEdit"
                            data-id="'.$customer->id.'"
                            data-name="'.e($customer->name).'"
                            data-status="'.e($customer->status).'"
                            data-phone="'.e($customer->phone).'"
                            data-email="'.e($customer->email).'"
                            data-type="'.e($customer->type).'"
                            data-notes="'.e($customer->notes).'"
                            class="edit btn btn-outline-primary btn-sm editBtn" title="'.__('owner.actions.edit').'">
                            <i class="bi bi-pencil"></i>
                        </a>';

                    $btn .= '<a href="#" onclick="deleteRecord('.$customer->id.')" class="edit btn btn-outline-danger btn-sm mx-1" title="'.__('owner.actions.delete').'"><i class="bi bi-trash"></i></a>';

                    return $btn;
                })
                ->with([
                    'customer_count' => $customer_count,
                    'customer_count_active' => $customer_count_active,
                    'total_sales' => number_format($total_sales, 2),
                    'total_orders' => $total_orders,
                ])
                ->rawColumns(['action', 'status', 'total_remaining'])
                ->make(true);
        }
    }
}
