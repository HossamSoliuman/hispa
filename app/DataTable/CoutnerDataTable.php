<?php

namespace App\DataTable;

use App\Models\FishStock;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CoutnerDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = User::CounterRole()->with(['region', 'governorate'])->orderBy('created_at', 'desc');

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

            $data = $query->get();
            $total_count = $data->count();
            $active_count = $data->where('status', 1)->count();
            $inactive_count = $total_count - $active_count;

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo
                        ? asset($user->logo)
                        : asset('default-logo.png'); // صورة افتراضية
                    // يمكن لاحقاً ربطه بصفحة تفصيلية للعداد
                    $profileUrl = '#';

                    return '<div class="d-flex align-items-center">
        <a href="'.$profileUrl.'" class="d-flex align-items-center text-decoration-none">
            <img src="'.$logoUrl.'" alt="logo" width="30" height="30" class="rounded-circle me-2">
            <span>'.e($user->name).'</span>
        </a>
    </div>';
                })
                ->addColumn('region', function (User $user) {
                    return $user->region->name ?? '--';
                })
                ->addColumn('governorate', function (User $user) {
                    return $user->governorate->name ?? '--';
                })
//                ->addColumn('city', function(User $user) {
//                    return $user->city->name ?? "--";
//                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('admin.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('admin.status.inactive').'</span>';
                    }
                })
                ->addColumn('action', function (User $user) {
                    $btn = '';
                    if (auth('admin')->check() && auth('admin')->user()->can('update_counter')) {

                        // زر التعديل
                        $btn .= '<a  href="' . route('admin.counter.edit', $user->id) . '"
        class="edit btn btn-primary btn-sm editBtn" title="تعديل">
        <i class="fas fa-edit"></i>
    </a> ';
                    }
                    if (auth('admin')->check() && auth('admin')->user()->can('delete_counter')) {

                        // زر الحذف
                        $btn .= '<a href="#" onclick="deleteRecord('.$user->id.')"
        class="btn btn-danger btn-sm" title="حذف">
        <i class="fas fa-trash"></i>
    </a>';
                    }

                    return $btn;
                })
                ->with([
                    'total_count' => $total_count,
                    'active_count' => $active_count,
                    'inactive_count' => $inactive_count,
                ])
                ->rawColumns(['action', 'status', 'name', 'region', 'governorate'])
                ->make(true);

        }

    }

    public function showData(Request $request, $id)
    {

        $query = FishStock::where('corrected_by', $id)->with('trip', 'fish');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('trip_name', fn ($row) => optional($row->trip)->name ?? '---')
            ->addColumn('fish_name', fn ($row) => optional($row->fish)->scientific_name ?? '---')
            ->addColumn('captain_name', fn ($row) => optional($row->addedBy)->name ?? '---')

            ->make(true);
    }
}
