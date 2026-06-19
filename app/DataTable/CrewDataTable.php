<?php

namespace App\DataTable;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CrewDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = User::CrewRole()->with(['owner', 'boat', 'region', 'governorate', 'port']);

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

            $crewActive = (clone $query)->where('status', 1)->count();
            $crewDisable = (clone $query)->where('status', 0)->count();
            $data = $query->orderBy('created_at', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo
                        ? asset($user->logo)
                        : asset('default-logo.png'); // صورة افتراضية

                    // رابط الصفحة (تغيير حسب رابط صفحة البروفايل عندك)
                    // أو لو لكل مستخدم رابط خاص:
                    // $profileUrl = route('users.show', $user->id);

                    $profileUrl = route('admin.crew.show', $user->id);

                    return '<div class="d-flex align-items-center">
        <a href="'.e($profileUrl).'" class="d-flex align-items-center text-decoration-none">
            <img src="'.e($logoUrl).'" alt="logo" width="30" height="30" class="rounded-circle me-2">
            <span>'.e($user->name).'</span>
        </a>
    </div>';
                })
                ->addColumn('id_number', function (User $user) {
                    return $user->id_number ?? $user->passport_number;
                })
                ->addColumn('job_title', function (User $user) {
                    return $user->job_title ?? '--';
                })
                ->addColumn('owner', function (User $user) {
                    return $user->owner->name ?? '--';
                })
                ->addColumn('boat', function (User $user) {
                    return $user->boat?->name ?: '--';
                })
                ->addColumn('region', function (User $user) {
                    return $user->region->name ?? '--';
                })
                ->addColumn('governorate', function (User $user) {
                    return $user->governorate->name ?? '--';
                })
                ->addColumn('port', function (User $user) {
                    return $user->port->name ?? '--';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('admin.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('admin.status.inactive').'</span>';
                    }
                })
                ->addColumn('action', function (User $user) {
                    $showUrl = route('admin.crew.show', $user->id);

                    return '<a href="'.e($showUrl).'" class="btn btn-outline-info btn-sm" title="'.e(__('admin.actions.view')).'"><i class="bi bi-eye"></i></a>';
                })
                ->with([
                    'crew_count' => $data->count(),
                    'crew_active' => $crewActive,
                    'crew_disable' => $crewDisable,
                ])
                ->rawColumns(['action', 'status', 'name', 'region', 'captain', 'boat', 'port', 'job_title'])
                ->make(true);

        }

    }
}
