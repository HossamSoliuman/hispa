<?php

namespace App\DataTable\Owner;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CustomerDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $owner = auth()->user();
            $owner_id = $owner->id;

            $query = Customer::where('owner_id', $owner_id)
                ->withCount([
                    'sales as total_sales' => function ($q) {
                        $q->select(DB::raw('COALESCE(SUM(remaining_total),0)'));
                    },
                    'sales',  // عدد الطلبات
                ])
                ->withMax('sales', 'created_at')
                ->orderBy('created_at', 'desc');

            $data = $query->get();

            $customer_count = $data->count();
            $customer_count_active = $data->where('status', 1)->count();
            //            $total_sales = $data->sum('total_sales');
            $total_sales = $data->sum(function ($customer) {
                return $customer->sales->sum('total_price');
            });
            $total_orders = $data->sum('sales_count'); // مجموع الطلبات لجميع العملاء

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('total_sales', function ($data) {
                    $total_sales = $data->sales->sum('total_price');

                    return number_format($total_sales ?? 0, 2);
                })
                ->addColumn('order_count', function ($data) {
                    return $data->sales_count;  // عدد الطلبات لكل عميل
                })
                ->addColumn('last_order', function ($data) {
                    return $data->sales_max_created_at ? date('Y-m-d', strtotime($data->sales_max_created_at)) : '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-success">مفعل</span>'
                        : '<span class="badge bg-danger">غير مفعل</span>';
                })
                ->addColumn('action', function (Customer $customer) {
                    $btn = '<a data-bs-effect="effect-scale" data-bs-toggle="modal" href="#modelEdit"
                            data-id="'.$customer->id.'"
                            data-name="'.e($customer->name).'"
                            data-status="'.e($customer->status).'"
                            data-phone="'.e($customer->phone).'"
                            data-email="'.e($customer->email).'"
                            data-type="'.e($customer->type).'"
                            data-notes="'.e($customer->notes).'"
                            class="edit btn btn-outline-primary mx-1 btn-sm editBtn">
                            <i class="bi bi-pencil"></i>
                        </a>';

                    $btn .= '<a href="#" onclick="deleteRecord('.$customer->id.')" class="edit btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></a>';

                    return $btn;
                })
                ->with([
                    'customer_count' => $customer_count,
                    'customer_count_active' => $customer_count_active,
                    'total_sales' => number_format($total_sales, 2),
                    'total_orders' => $total_orders,
                ])
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }
}
