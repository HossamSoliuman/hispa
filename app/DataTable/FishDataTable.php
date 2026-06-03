<?php

namespace App\DataTable;

use App\Models\Fish;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FishDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Fish::orderBy('created_at', 'desc');

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">مفعل</span>';
                    } else {
                        return '<span class="badge bg-danger">غير مفعل</span>';
                    }
                })
                ->addColumn('region', function (Fish $fish) {
                    return $fish->region->name ?? '--';
                })
                ->addColumn('governorate', function (Fish $fish) {
                    return $fish->governorate->name ?? '--';
                })
                ->addColumn('action', function (Fish $fish) {
                    $btn = '';

                    $btn .= '<a data-bs-effect="effect-scale" data-bs-toggle="modal" href="#modelEdit"
            data-id="'.$fish->id.'"
            data-code="'.$fish->code.'"
            data-red_sea_name="'.$fish->red_sea_name.'"
            data-arabian_gulf_name="'.$fish->arabian_gulf_name.'"
            data-scientific_name="'.$fish->scientific_name.'"
            data-english_name="'.$fish->english_name.'"
            data-local_name_primary="'.$fish->local_name_primary.'"
            data-local_name_secondary="'.$fish->local_name_secondary.'"
            data-region_id ="'.$fish->region_id.'"
            data-governorate_id  ="'.$fish->governorate_id.'"
            data-status="'.$fish->status.'"
            class="btn btn-sm btn-outline-success me-1 editBtn">
            <i class="bi bi-pencil"></i>
        </a>';

                    $btn .= '<a href="#" onclick="deleteRecord('.$fish->id.')" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'region', 'governorate'])
                ->make(true);
        }

    }
}
