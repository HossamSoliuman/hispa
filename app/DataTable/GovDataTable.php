<?php

namespace App\DataTable;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GovDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = User::GovRole()->orderBy('created_at', 'desc');

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo
                        ? asset($user->logo)
                        : asset('default-logo.png'); // صورة افتراضية

                    // رابط الصفحة (تغيير حسب رابط صفحة البروفايل عندك)
                    $profileUrl = route('admin.gov.show', $user->id); // لو ثابتة صفحة صاحب الجلسة
                    // أو لو لكل مستخدم رابط خاص:
                    // $profileUrl = route('users.show', $user->id);

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
                ->addColumn('roles', function (User $admin) {
                    return $admin->getRoleNames()->map(fn ($v) => "<span class='badge bg-success'>{$v}</span>")->implode(' ');
                })

                ->addColumn('governorate', function (User $user) {
                    return $user->governorate->name ?? '--';
                })
//                ->addColumn('city', function(User $user) {
//                    return $user->city->name ?? "--";
//                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success"> '.__('admin.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger"> '.__('admin.status.inactive').'</span>';
                    }
                })
                ->addColumn('action', function (User $user) {
                    $btn = '';

                    if (auth()->user()->can('update_gov')) {
                        $btn .= '<a href="'.route('admin.gov.edit', $user->id).'"
            class="edit btn btn-primary btn-sm editBtn" title="تعديل">
            <i class="fas fa-edit"></i>
        </a> ';
                    }

                    if (auth()->user()->can('delete_gov')) {
                        $btn .= '<a href="#" onclick="deleteRecord('.$user->id.')"
            class="btn btn-danger btn-sm" title="حذف">
            <i class="fas fa-trash"></i>
        </a>';
                    }

                    return $btn;
                })

                ->rawColumns(['action', 'roles', 'status', 'name', 'region', 'governorate']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);

        }

    }
}
