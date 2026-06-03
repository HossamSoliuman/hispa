<?php

namespace App\DataTable\Owner;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployeeDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $ownerId = $user->id;

            $query = User::EmployeeRole()->where('owner_id', $ownerId);

            $employee_active = (clone $query)->active()->count();
            $employee_disable = (clone $query)->disable()->count();
            $data = $query->orderBy('created_at', 'desc')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo;

                    $profileUrl = route('owner.employee.show', $user->id);

                    return '<div class="d-flex align-items-center">
        <a href="' . $profileUrl . '" class="d-flex align-items-center text-decoration-none">
            <img src="' . $logoUrl . '" alt="logo" width="30" height="30" class="rounded-circle me-2">
            <span>' . e($user->name) . '</span>
        </a>
    </div>';
                })
                ->addColumn('id_number', function (User $user) {
                    return $user->id_number ?? $user->passport_number;
                })
                ->addColumn('job_title', function (User $user) {
                    return $user->job_title ?? '--';
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

                    $btn .= '<a  href="' . route('owner.employee.edit', $user->id) . '"
        class="edit btn btn-outline-primary btn-sm editBtn" title="تعديل">
        <i class="bi bi-pencil"></i>
    </a> ';

                    $btn .= '<a href="#" onclick="deleteRecord(' . $user->id . ')"
        class="btn btn-outline-danger btn-sm" title="حذف">
        <i class="bi bi-trash"></i>
    </a>';

                    return $btn;
                })->with([
                    'employee_count' => $data->count(),
                    'employee_active' => $employee_active,
                    'employee_disable' => $employee_disable,
                ])

                ->rawColumns(['action', 'status', 'name', 'job_title'])
                ->make(true);
        }
    }
}
