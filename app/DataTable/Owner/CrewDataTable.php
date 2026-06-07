<?php

namespace App\DataTable\Owner;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CrewDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $ownerId = $user->id;
            if ($request->has('boat_id')) {
                $boatId = $request->boat_id;
                $query = User::CrewRole()->where('owner_id', $ownerId)->where('boat_id', $boatId);
            } else {
                $query = User::CrewRole()->where('owner_id', $ownerId);
            }

            $crew_active = (clone $query)->active()->count();
            $crew_disable = (clone $query)->disable()->count();
            $data = $query->orderBy('created_at', 'desc')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    // Render avatar + name using a Blade partial to avoid embedding large HTML
                    // blobs inside PHP strings. The partial will handle uploaded images vs the
                    // project-standard initial-letter colored avatar.
                    return view('owner.partials._avatar', compact('user'))->render();
                })
                ->addColumn('id_number', function (User $user) {
                    return $user->id_number ?? $user->passport_number;
                })
                ->addColumn('job_title', function (User $user) {
                    return $user->job_title ?? '--';
                })

                ->addColumn('boat', function (User $user) {
                    return $user->boat?->name ?? '--';
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
                        return '<span class="badge bg-success">مفعل</span>';
                    } else {
                        return '<span class="badge bg-danger">غير مفعل</span>';
                    }
                })
                ->addColumn('action', function (User $user) use ($request) {
                    // On the boat profile page the crew list is read-only: only a view
                    // button that opens a details modal. Management lives in settings.
                    if ($request->has('boat_id')) {
                        return view('owner.boats.crew._view_button', compact('user'))->render();
                    }

                    $btn = '';

                    // زر التعديل
                    $btn .= '<a  href="'.route('owner.crew.edit', $user->id).'"
        class="edit btn btn-outline-primary btn-sm editBtn" title="تعديل">
        <i class="bi bi-pencil"></i>
    </a> ';

                    // زر الحذف
                    $btn .= '<a href="#" onclick="deleteCrewRecord('.$user->id.')"
        class="btn btn-outline-danger btn-sm" title="حذف">
        <i class="bi bi-trash"></i>
    </a>';

                    return $btn;
                })->with([
                    'crew_count' => $data->count(),
                    'crew_active' => $crew_active,
                    'crew_disable' => $crew_disable,
                ])

                ->rawColumns(['action', 'status', 'name', 'region', 'port', 'captain', 'boat', 'job_title']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);
        }
    }

    public function showData(Request $request, $id)
    {
        // نجيب الكابتن مع القارب والرحلات
        $query = User::with('boat.trips')->where('id', $id);

        $data = $query->get();

        $boatName = optional($data->first()->boat)->name ?? '---';
        $tripsCount = $data->first()->boat?->trips->count() ?? 0;

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('boat_name', fn ($row) => optional($row->boat)->name ?? '---')
            ->addColumn(
                'trip_name',
                fn ($row) => $row->boat && $row->boat->trips->count()
                    ? $row->boat->trips->pluck('name')->implode(', ')
                    : '---'
            )
            ->with([
                'boat_name' => $boatName,
                'trips_count' => $tripsCount,
            ])
            ->rawColumns(['trip_name', 'boat_name'])
            ->make(true);
    }
}
