<?php

namespace App\DataTable\Owner;

use App\Models\Boat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BoatDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $owner_id = auth()->id();

            $query = Boat::with(['owner', 'captain'])
                ->where('owner_id', $owner_id)
                ->orderBy('created_at', 'desc');

            $data = $query->get();
            $boat_count = $query->count();
            $boat_active_count = $query->where('status', 1)->count();

            $boats_upcoming_task = $query->whereHas('inspections', function ($q) {
                $q->whereBetween('next_check', [
                    Carbon::now(),
                    Carbon::now()->addDays(10),
                ]);
            })->count();
            $boats_no_task = Boat::has('inspections', '=', 0)->count();
            $boats_upcoming_tasks = $boats_upcoming_task + $boats_no_task;

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('owner.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('owner.status.inactive').'</span>';
                    }
                })
                ->addColumn('type', function (Boat $boat) {

                    return $boat->type;
                })
                ->addColumn('category', function (Boat $boat) {

                    return $boat->boat_type->name ?? '';
                })
                ->addColumn('captain', function (Boat $boat) {

                    return $boat->captain->name ?? __('owner.boats.captain_not_found');
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('owner.boats.show', $row->id);
                    $editUrl = route('owner.boats.edit', $row->id);

                    $btn = '<a href="'.$showUrl.'" class="btn btn-sm btn-outline-primary mx-1" title="'.__('owner.actions.show').'"><i class="bi bi-eye"></i></a>';
                    $btn .= '<a href="'.$editUrl.'" class="btn btn-sm btn-outline-warning mx-1" title="'.__('owner.actions.edit').'"><i class="bi bi-pencil"></i></a>';

                    return $btn;
                })
                ->with([
                    'boat_active_count' => $boat_active_count,
                    'boats_upcoming_tasks' => $boats_upcoming_tasks,
                    'boat_count' => $boat_count,

                ])
                ->rawColumns(['status', 'captain', 'number', 'action']) // أضف 'action' هنا لأن به HTML
                ->make(true);
        }
    }
}
