<?php

namespace App\DataTable\Owner;

use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class InspectionDataTable
{
    public function getData($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('boat_name', fn ($row) => $row->boat?->name ?? '-')
            ->addColumn('check_date', fn ($row) => Carbon::parse($row->check_date)->toDateString() ?? '-')
            ->addColumn('next_check', fn ($row) => Carbon::parse($row->next_check)->toDateString() ?? '-')
            ->addColumn('status_badge', function ($row) {
                return '<span class="badge bg-info">'.$row->status->label().'</span>';
            })
            ->addColumn('action', function ($row) {
                return view('owner.boats.inspections.actions', ['row' => $row])->render();
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }
}
