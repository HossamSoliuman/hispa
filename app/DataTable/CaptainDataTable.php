<?php

namespace App\DataTable;

use App\Models\FishStock;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CaptainDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = User::CaptainRole()->with(['owner', 'boat', 'region', 'governorate', 'port'])->orderBy('created_at', 'desc');

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

            $captainActive = (clone $query)->where('status', 1)->count();
            $captainDisable = (clone $query)->where('status', 0)->count();
            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo
                        ? asset($user->logo)
                        : asset('default-logo.png'); // صورة افتراضية
                    $profileUrl = '#';
                    // رابط الصفحة (تغيير حسب رابط صفحة البروفايل عندك)
                    if (auth()->user()->can('read_captain')) {

                        $profileUrl = route('admin.captain.show', $user->id); // لو ثابتة صفحة صاحب الجلسة
                    }
                    // رابط الصفحة (تغيير حسب رابط صفحة البروفايل عندك)
                    // أو لو لكل مستخدم رابط خاص:
                    // $profileUrl = route('users.show', $user->id);

                    return '<div class="d-flex align-items-center">
        <a href="'.$profileUrl.'" class="d-flex align-items-center text-decoration-none">
            <img src="'.$logoUrl.'" alt="logo" width="30" height="30" class="rounded-circle me-2">
            <span>'.e($user->name).'</span>
        </a>
    </div>';
                })
                ->addColumn('owner', function (User $user) {
                    return optional($user->owner)->name ?? '--';
                })
                ->addColumn('crew_count', function (User $user) {
                    return $user->boat_id ? ($user->boat->crews->count() ?? '0') : '0';
                })
                ->addColumn('boat_name', function (User $user) {
                    return $user->boat?->name ?: '--';
                })
                ->addColumn('region', function (User $user) {
                    return optional($user->region)->name ?? '--';
                })
                ->addColumn('governorate', function (User $user) {
                    return optional($user->governorate)->name ?? '--';
                })
                ->addColumn('port', function (User $user) {
                    return $user->port_id ? (optional($user->port)->name ?? '--') : '--';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('admin.captains.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('admin.captains.status.inactive').'</span>';
                    }
                })
                ->addColumn('action', function (User $user) {
                    $btn = '';

                    return $btn;
                })
                ->with([
                    'captain_count' => $data->count(),
                    'captain_active' => $captainActive,
                    'captain_disable' => $captainDisable,
                ])
                ->rawColumns(['action', 'status', 'name', 'region', 'governorate', 'port', 'owner'])
                ->make(true);

        }

    }

    public function showData(Request $request, $id)
    {

        $query = FishStock::where('added_by', $id)->with('trip', 'fish');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('trip_name', fn ($row) => optional($row->trip)->name ?? '---')
            ->addColumn('fish_name', fn ($row) => optional($row->fish)->scientific_name ?? '---')
            ->make(true);
    }
}
