<?php

namespace App\Repository\Owner;

use App\Interfaces\CRUD;
use App\Models\Boat;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Service\Owner\PayrollService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollRepository implements CRUD
{
    private $payrollService;

    public function __construct()
    {
        $this->payrollService = new PayrollService;
    }

    public function getList($request)
    {
        // TODO: Implement getList() method.
    }

    public function getDetail($id)
    {
        // TODO: Implement getDetail() method.
    }

    public function saveData($request)
    {
        DB::transaction(function () use ($request) {
            $boat = Boat::with('crews', 'captain')->findOrFail($request->boat_id);

            $from = Carbon::parse($request->period_from)->startOfDay();
            $to = Carbon::parse($request->period_to)->endOfDay();

            $existingPayroll = Payroll::where('boat_id', $boat->id)
                ->where(function ($q) use ($from, $to) {
                    $q->whereBetween('period_from', [$from, $to])
                        ->orWhereBetween('period_to', [$from, $to])
                        ->orWhere(function ($qq) use ($from, $to) {
                            $qq->where('period_from', '<=', $from)
                                ->where('period_to', '>=', $to);
                        });
                })->first();

            // إذا كان موجودًا ومغلق، نمنع الإنشاء
            if ($existingPayroll && $existingPayroll->status === 'closed') {
                throw new \Exception('تم حساب الرواتب والإيرادات لهذه الفترة مسبقاً.');
            }

            // حساب الرواتب
            $payrollData = $this->payrollService->calculateBoatPayroll($boat, $from, $to, $request->owner_percentage);

            // إذا كان موجودًا ومفتوح للترحيل فقط، نحدثه
            if ($existingPayroll && $existingPayroll->status === 'open') {
                $payroll = $existingPayroll;
                $payroll->total_revenues = $payrollData['total_revenues'];
                $payroll->total_expenses = $payrollData['total_expenses'];
                $payroll->owner_percentage = $payrollData['owner_profit_percent'];
                $payroll->owner_profit = $payrollData['owner_net_profit'];
                $payroll->crew_total = $payrollData['total_crew_salary'];
                $payroll->carry_over = $payrollData['carry_over'];
                $payroll->surplus = $payrollData['carry_over'] > 0 ? $payrollData['carry_over'] : 0;
                $payroll->deficit = $payrollData['carry_over'] < 0 ? abs($payrollData['carry_over']) : 0;
                $payroll->status = 'closed';
                $payroll->save();

                PayrollDetail::where('payroll_id', $payroll->id)->delete();
            } else {
                // إنشاء راتب جديد
                $payroll = Payroll::create([
                    'owner_id' => $boat->owner_id,
                    'boat_id' => $boat->id,
                    'period_from' => $from,
                    'period_to' => $to,
                    'total_revenues' => $payrollData['total_revenues'],
                    'total_expenses' => $payrollData['total_expenses'],
                    'owner_percentage' => $payrollData['owner_profit_percent'],
                    'owner_profit' => $payrollData['owner_net_profit'],
                    'crew_total' => $payrollData['total_crew_salary'],
                    'carry_over' => $payrollData['carry_over'],
                    'surplus' => $payrollData['carry_over'] > 0 ? $payrollData['carry_over'] : 0,
                    'deficit' => $payrollData['carry_over'] < 0 ? abs($payrollData['carry_over']) : 0,
                    'status' => 'closed',
                ]);
            }

            // حفظ تفاصيل الطاقم
            foreach ($payrollData['crew'] as $c) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'user_id' => $c['user_id'],
                    'salary_type' => $c['salary_type'],
                    'fixed_amount' => $c['fixed_amount'],
                    'percentage' => $c['percentage'],
                    'calculated_salary' => $c['calculated_salary'],
                    'is_captain' => $c['is_captain'],
                    'is_crew' => $c['is_crew'],
                ]);
            }

            // ترحيل الفائض/العجز للشهر القادم
            $nextMonthStart = $to->copy()->addDay()->startOfMonth();
            $nextMonthEnd = $to->copy()->addDay()->endOfMonth();

            $nextPayroll = Payroll::firstOrNew([
                'boat_id' => $boat->id,
                'owner_id' => $boat->owner_id,
                'period_from' => $nextMonthStart,
                'period_to' => $nextMonthEnd,
                'status' => 'open',
            ]);
            $nextPayroll->carry_over = $payrollData['carry_over'];
            $nextPayroll->save();
        });

        return response()->json([
            'success' => true,
            'message' => '✅ تم حفظ التوزيع بنجاح مع ترحيل العجز/الفائض',
        ]);
    }

    public function saveDataOld($request)
    {
        DB::transaction(function () use ($request) {
            $boat = Boat::with('crews', 'captain')->findOrFail($request->boat_id);

            $from = Carbon::parse($request->period_from)->startOfDay();
            $to = Carbon::parse($request->period_to)->endOfDay();

            $existingPayroll = Payroll::where('boat_id', $boat->id)
                ->where(function ($q) use ($from, $to) {
                    $q->whereBetween('period_from', [$from, $to])
                        ->orWhereBetween('period_to', [$from, $to])
                        ->orWhere(function ($qq) use ($from, $to) {
                            $qq->where('period_from', '<=', $from)
                                ->where('period_to', '>=', $to);
                        });
                })->first();

            if ($existingPayroll) {
                throw new \Exception('تم حساب الرواتب والإيرادات لهذه الفترة مسبقاً.');
            }

            // استدعاء الدالة المعدلة لحساب الرواتب
            /** @phpstan-ignore-next-line */
            $payrollData = $this->calculateBoatPayrollWithCarryOver($boat, $from, $to, $request->owner_percentage);

            // إنشاء الراتب للفترة الحالية
            $payroll = Payroll::create([
                'owner_id' => $boat->owner_id,
                'boat_id' => $boat->id,
                'period_from' => $from,
                'period_to' => $to,
                'total_revenues' => $payrollData['total_revenues'],
                'total_expenses' => $payrollData['total_expenses'],
                'owner_percentage' => $payrollData['owner_profit_percent'],
                'owner_profit' => $payrollData['owner_net_profit'],
                'crew_total' => $payrollData['total_crew_salary'],
                'carry_over' => $payrollData['carry_over'],
                'surplus' => $payrollData['carry_over'] > 0 ? $payrollData['carry_over'] : 0,
                'deficit' => $payrollData['carry_over'] < 0 ? abs($payrollData['carry_over']) : 0,
                'status' => 'closed',
            ]);

            // حفظ الرواتب لكل فرد من الطاقم
            foreach ($payrollData['crew'] as $c) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'user_id' => $c['user_id'],
                    'salary_type' => $c['salary_type'],
                    'fixed_amount' => $c['fixed_amount'],
                    'percentage' => $c['percentage'],
                    'calculated_salary' => $c['calculated_salary'],
                    'is_captain' => $c['is_captain'],
                    'is_crew' => $c['is_crew'],
                ]);
            }

            // إنشاء أو تحديث الراتب المفتوح للشهر القادم
            if ($payrollData['carry_over'] != 0) {
                $nextMonthStart = now()->addMonth()->startOfMonth();
                $nextMonthEnd = now()->addMonth()->endOfMonth();

                $nextPayroll = Payroll::firstOrNew([
                    'boat_id' => $boat->id,
                    'period_from' => $nextMonthStart,
                    'period_to' => $nextMonthEnd,
                    'status' => 'open',
                ]);
                $nextPayroll->carry_over = $payrollData['carry_over'];
                $nextPayroll->save();
            }
        });

        return response()->json([
            'success' => true,
            'message' => '✅ تم حفظ التوزيع بنجاح مع ترحيل العجز/الفائض',
        ]);
    }

    /**
     * نسخة معدلة لحساب الرواتب تشمل ترحيل الرصيد المفتوح من الشهر السابق
     */
    public function updateData($request, $id)
    {
        // TODO: Implement updateData() method.
    }

    public function deleteData($id)
    {
        // TODO: Implement deleteData() method.
    }
}
