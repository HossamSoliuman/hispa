<?php

namespace App\DataTable;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                $term = $request->search;
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            }
            if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
                $query->where('status', (int) $request->status);
            }

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('added_by', function ($row) {
                    if (isset($row->dalal_id) && isset($row->dalal)) {
                        return '<span class="badge bg-primary"> '.__('admin.customer.dalal').'-'.htmlspecialchars($row->dalal->name).'</span>';
                    } elseif (isset($row->owner_id) && isset($row->owner)) {
                        return '<span class="badge bg-success"> '.__('admin.customer.owner').'-'.htmlspecialchars($row->owner->name).'</span>';
                    } else {
                        return '<span class="badge bg-danger"> '.__('admin.customer.system').'</span>';
                    }
                })

                ->addColumn('action', function (Customer $customer) {
                    $btn = '';
                    if (auth()->user()->can('update_customers')) {

                        $btn .= '<a data-bs-effect="effect-scale" data-bs-toggle="modal" href="#modelEdit"
            data-id="'.$customer->id.'"
            data-name="'.$customer->name.'"
            data-phone="'.$customer->phone.'"
            data-email="'.$customer->email.'"
            data-type="'.$customer->type.'"
            data-notes="'.$customer->notes.'"
            class="edit btn btn-primary btn-sm editBtn">
            <li class="fas fa-edit"></li>
        </a>';
                    }
                    if (auth()->user()->can('delete_customers')) {

                        $btn .= '<a href="#" onclick="deleteRecord('.$customer->id.')" class="edit btn btn-danger btn-sm"><li class="fas fa-trash"></li></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'added_by'])
                ->make(true);
        }

    }
}
