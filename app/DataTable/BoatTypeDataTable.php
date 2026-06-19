<?php

namespace App\DataTable;

use App\Models\BoatType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BoatTypeDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = BoatType::orderBy('created_at', 'desc');

            // DataTables غالبًا يرسل البحث كمصفوفة مثل ['value' => '...']
            if ($request->filled('search') || $request->filled('search.value')) {
                $searchInput = $request->input('search');

                if (is_array($searchInput)) {
                    $term = $searchInput['value'] ?? '';
                } else {
                    $term = $searchInput;
                }

                // في حال لم توجد قيمة بحث فعلية لا نطبق الفلتر
                if ($term !== null && $term !== '') {
                    $query->where(function ($q) use ($term) {
                        $q->where('name_ar', 'like', "%{$term}%")
                            ->orWhere('name_en', 'like', "%{$term}%");
                    });
                }
            }
            if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
                $query->where('status', (int) $request->status);
            }

            $data = $query->get();
            $total_count = $data->count();
            $active_count = $data->where('status', 1)->count();
            $inactive_count = $total_count - $active_count;

            $showRouteName = auth('admin')->check() ? 'admin.boat_types.show' : 'owner.boat_types.show';

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) use ($showRouteName) {
                    $url = route($showRouteName, $row->id);
                    $name = $row->name ?: ('#'.$row->id);

                    return '<a href="'.$url.'" class="text-primary fw-semibold text-decoration-none">'.e($name).'</a>';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('admin.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('admin.status.inactive').'</span>';
                    }
                })

                ->addColumn('action', function ($row) {
                    if (auth('admin')->check()) {
                        $showUrl = route('admin.boat_types.show', $row->id);
                        $editUrl = route('admin.boat_types.edit', $row->id);
                        $deleteUrl = route('admin.boat_types.destroy', $row->id);
                    } else {
                        $showUrl = route('owner.boat_types.show', $row->id);
                        $editUrl = route('owner.boat_types.edit', $row->id);
                        $deleteUrl = route('owner.boat_types.destroy', $row->id);
                    }

                    return '
                    <a href="'.$showUrl.'" class="btn btn-sm btn-outline-info me-1" title="'.__('admin.actions.view').'"><i class="bi bi-eye"></i></a>
                    <a href="'.$editUrl.'" class="btn btn-sm btn-outline-success me-1"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="deleteRecord('.$row->id.')" class="btn btn-danger btn-sm" title="'.__('admin.actions.delete').'"><i class="bi bi-trash"></i></a>
                    ';
                })

                ->with([
                    'total_count' => $total_count,
                    'active_count' => $active_count,
                    'inactive_count' => $inactive_count,
                ])
                ->rawColumns(['name', 'status', 'action'])
                ->make(true);

        }
    }
}
