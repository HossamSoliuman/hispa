<?php

namespace App\DataTable\Owner;

use Yajra\DataTables\DataTables;

class MaintenanceDataTable
{
    public function getData($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('boat_name', fn($row) => $row->boat?->name ?? '-')
            ->addColumn('category_name', fn($row) => $row->category?->name ?? '-')
            ->addColumn('action', function ($row) {
                return view('owner.boats.maintenance.actions', ['row' => $row])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
