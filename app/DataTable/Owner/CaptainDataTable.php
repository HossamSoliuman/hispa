<?php

namespace App\DataTable\Owner;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CaptainDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $user = auth()->user();
        $query = User::CaptainRole()->where('owner_id', $user->id)->orderBy('created_at', 'desc');

        $captain_active = (clone $query)->active()->count();
        $captain_disable = (clone $query)->disable()->count();
        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('name', function (User $user) {
                $logoUrl = $user->logo
                    ? asset($user->logo)
                    : asset('default-logo.png'); // صورة افتراضية

                // رابط الصفحة (تغيير حسب رابط صفحة البروفايل عندك)
                // أو لو لكل مستخدم رابط خاص:
                $profileUrl = route('owner.captain.show', $user->id);

                return '<div class="d-flex align-items-center">
        <a href="' . $profileUrl . '" class="d-flex align-items-center text-decoration-none">
            <img src="' . $logoUrl . '" alt="logo" width="30" height="30" class="rounded-circle me-2">
            <span>' . e($user->name) . '</span>
        </a>
    </div>';
            })
            ->addColumn('boat_name', function (User $user) {
                return $user->boat->name ?? '--';
            })
            ->addColumn('owner', function (User $user) {
                return $user->owner->name ?? '--';
            })
            ->addColumn('region', function (User $user) {
                return $user->region->name ?? '--';
            })
            ->addColumn('governorate', function (User $user) {
                return $user->governorate->name ?? '--';
            })
            ->addColumn('city', function (User $user) {
                return $user->city->name ?? '--';
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
            ->addColumn('action', function (User $user) {
                $btn = '';

                // زر التعديل
                $btn .= '<a  href="' . route('owner.captain.edit', $user->id) . '"
        class="edit btn btn-outline-primary btn-sm editBtn" title="تعديل">
        <i class="bi bi-pencil"></i>
    </a> ';

                // زر الحذف
                $btn .= '<a href="#" onclick="deleteRecord(' . $user->id . ')"
        class="btn btn-outline-danger btn-sm" title="حذف">
        <i class="bi bi-trash"></i>
    </a>';

                return $btn;
            })->with([
                'captain_count' => $data->count(),
                'captain_active' => $captain_active,
                'captain_disable' => $captain_disable,
            ])
            ->rawColumns(['action', 'status', 'name', 'region', 'governorate', 'city', 'port', 'owner']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
            ->make(true);
    }

    public function showData(Request $request, $id)
    {
        $query = User::where('id', $id)->with(['boat', 'boat.stocks'])->first();

        return DataTables::of($query->boat->stocks)
            ->addIndexColumn()
            ->addColumn('trip_name', fn($row) => optional($row->trip)->name ?? '---')
            ->addColumn('fish_name', fn($row) => optional($row->fish)->scientific_name ?? '---')
            ->addColumn('weight', fn($row) => $row->quantity ?? '---')

            ->make(true);
    }
}
