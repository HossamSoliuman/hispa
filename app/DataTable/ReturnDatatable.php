<?php

namespace App\DataTable;

use App\Models\ReturnModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReturnDatatable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $query = ReturnModel::with('sale')->orderBy('created_at', 'desc');

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('sale_number', function ($row) {
                    return $row->sale->number;
                })
                ->addColumn('total_amount', function ($row) {
                    return $row->total_amount;
                })
                ->addColumn('returned_at', function ($row) {
                    return $row->returned_at;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'approved') {
                        return '<span class="badge bg-success">'.__('owner.returns.approved').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('owner.returns.rejected').'</span>';
                    }
                })

                ->rawColumns(['status'])
                ->make(true);

        }
    }
}
