<?php

namespace App\DataTable\Owner;

use Yajra\DataTables\DataTables;

class VendorsDataTable
{
    public function getData($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status', function ($vendor) {
                return $vendor->status ? '<span class="badge bg-success">نشط</span>' : '<span class="badge bg-danger">متوقف</span>';
            })
            ->addColumn('actions', function ($vendor) {
                return view('owner.vendors.partials.actions', compact('vendor'));
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }
}
