<?php

namespace App\DataTable\Owner;

use App\Models\PayrollDetail;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PayrollDataTable extends DataTables
{
    public function getData(Request $request)
    {

        $payrolls = PayrollModel::with('details')->where('type', $request->type)->get();
        $totalPayrolls = $payrolls->count();
        $paidPayrolls = $payrolls->where('is_paid', 1)->count();
        $paidPayroll = PayrollModel::where('is_paid', 1)->where('type', $request->type)->pluck('id');
        $paidAmount = PayrollDetailsModel::whereIn('payroll_id', $paidPayroll)->sum('final_salary');
        $pendingApproval = $payrolls->where('is_paid', 0)->count();
        $avgPerCrew = 0;

        return DataTables::of($payrolls)
            ->addIndexColumn()
            ->addColumn('year', fn ($row) => $row->year ?? '-')
            ->addColumn('month', fn ($row) => $row->month ?? '-')
            ->addColumn('is_paid', fn ($row) => $row->is_paid == 1
                ? '<span class="badge bg-success">مدفوعة</span>'
                : '<span class="badge bg-warning">قيد التدقيق</span>'
            )
            ->addColumn('status', fn ($row) => $row->status == 'approved'
                ? '<span class="badge bg-success">معتمدة</span>'
                : '<span class="badge bg-warning">مسودة</span>'
            )
            ->addColumn('paid_at', fn ($row) => $row->paid_at ?? '-')
            ->addColumn('action', function ($row) {

                return '
         <a href="#" onclick="deleteRecord('.$row->id.')"
        class="btn btn-danger btn-sm" title="حذف">
        <i class="bi bi-trash"></i></a>';
            })

            ->rawColumns(['status', 'is_paid', 'action'])
            ->with([
                'total_payrolls' => $totalPayrolls,
                'paid_payrolls' => $paidPayrolls,
                'paid_amount' => number_format($paidAmount, 2),
                'pending_approval' => $pendingApproval,
                'avg_per_crew' => number_format($avgPerCrew, 2),
            ])
            ->make(true);

    }

    public function getDetails($payrollId)
    {
        $details = PayrollDetail::with('user')
            ->where('payroll_id', $payrollId)
            ->get();

        return DataTables::of($details)
            ->addIndexColumn()
            ->addColumn('user', fn ($row) => $row->user->name ?? '-')
            ->addColumn('salary_type', fn ($row) => $row->salary_type == 'fixed' ? 'راتب ثابت' : 'نسبة')
            ->addColumn('fixed_amount', fn ($row) => number_format($row->fixed_amount, 2))
            ->addColumn('percentage', fn ($row) => $row->percentage ? $row->percentage.'%' : '-')
            ->addColumn('calculated_salary', fn ($row) => number_format($row->calculated_salary, 2))
            ->addColumn('is_captain', fn ($row) => $row->is_captain ? '✔️' : '')
            ->addColumn('is_crew', fn ($row) => $row->is_crew ? '✔️' : '')
            ->addColumn('notes', fn ($row) => $row->notes ?? '-')
            ->make(true);
    }
}
