<?php

namespace App\DataTable;

use App\Models\Boat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BoatDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Boat::with(['owner', 'captain', 'boat_type'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
                $query->where('status', (int) $request->status);
            }
            if ($request->filled('search')) {
                $term = $request->search;
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%{$term}%"));
                });
            }
            if ($request->filled('boat_type_id')) {
                $query->where('boat_type_id', $request->boat_type_id);
            }

            $data = $query->get();
            $boat_count = $data->count();
            $boat_active_count = $data->where('status', 1)->count();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('admin.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('admin.status.inactive').'</span>';
                    }
                })
                ->addColumn('category', function (Boat $boat) {

                    return $boat->boat_type->name ?? '';
                })
                ->addColumn('type', function (Boat $boat) {

                    return $boat->type;
                })
                ->addColumn('owner', function (Boat $boat) {

                    return $boat->owner->name ?? __('admin.boats.no_owner');
                })
                ->addColumn('captain', function (Boat $boat) {

                    return $boat->captain->name ?? __('admin.boats.no_captain');
                })
                ->addColumn('action', function ($row) {
                    $buttons = '';

                    if (auth('admin')->check() && auth('admin')->user()->can('read_boats')) {
                        $showUrl = route('admin.boats.show', $row->id);
                        $buttons .= '<a href="'.$showUrl.'" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-eye"></i>
                     </a>';
                    } elseif (auth()->check() && auth()->user()->can('read_boats')) {
                        $showUrl = route('owner.boats.show', $row->id);
                        $buttons .= '<a href="'.$showUrl.'" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-eye"></i>
                     </a>';
                    }

                    if (auth('admin')->check() && auth('admin')->user()->can('update_boats')) {
                        $editUrl = route('admin.boats.edit', $row->id);
                        $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-outline-success me-1">
                        <i class="bi bi-pencil"></i>
                     </a>';
                    } elseif (auth()->check() && auth()->user()->can('update_boats')) {
                        $editUrl = route('owner.boats.edit', $row->id);
                        $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-outline-success me-1">
                        <i class="bi bi-pencil"></i>
                     </a>';
                    }

                    if ((auth('admin')->check() && auth('admin')->user()->can('delete_boats')) || 
                        (auth()->check() && auth()->user()->can('delete_boats'))) {
                        $buttons .= '<a href="#" onclick="deleteRecord('.$row->id.')"
                        class="btn btn-danger btn-sm" title="حذف">
                        <i class="bi bi-trash"></i>
                     </a>';
                    }

                    return $buttons;
                })

                ->with([
                    'boat_active_count' => $boat_active_count,
                    'boat_count' => $boat_count,

                ])
                ->rawColumns(['status', 'captain', 'number', 'action']) // أضف 'action' هنا لأن به HTML
                ->make(true);

        }
    }
}
