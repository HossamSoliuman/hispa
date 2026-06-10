<?php

namespace App\Service\Owner;

use App\Models\Boat;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function calculateBoatPayroll(Boat $boat, Carbon $from, Carbon $to, ?float $ownerPercentage = null): array
    {
        // 1. جلب الرحلات ضمن الفترة
        $trips = Trip::where('boat_id', $boat->id)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('id');

        // 2. إيرادات المالك من مبيعات رحلات القارب (التدفق القديم للدلال أُلغي)
        $totalRevenues = Sale::whereIn('trip_id', $trips)
            ->where('seller_id', $boat->owner_id)
            ->sum('net_owner_amount');

        // 4. المصروفات للفترة
        $expenses = Expense::where('boat_id', $boat->id)
            ->whereBetween('date', [$from, $to])
            ->sum('final_price');

        // 5. حساب صافي ربح الصيّاد
        $ownerProfitPercent = $ownerPercentage ?? $boat->owner_profit_percent ?? 0;
        $ownerNetProfit = $totalRevenues * ($ownerProfitPercent / 100);

        // 6. الرصيد المتبقي قبل توزيع الرواتب
        $remainingBalance = $totalRevenues - $expenses - $ownerNetProfit;

        // 7. جلب جميع الطاقم والكابتن
        $allCrew = $boat->crews->concat($boat->captain ? collect([$boat->captain]) : collect([]));

        // 8. حساب مجموع الرواتب الثابتة
        $totalFixedSalaries = $allCrew
            ->where('salary_type', 'salary')
            ->sum('salary_amount');

        $remainingBalanceForCrew = $remainingBalance - $totalFixedSalaries;

        // 9. توزيع الرواتب (ثابتة ونسبية)
        $crewWithCaptain = $allCrew->map(function ($member) use ($remainingBalanceForCrew) {
            $calculated = $member->salary_type === 'salary'
                ? $member->salary_amount
                : $remainingBalanceForCrew * (($member->salary_amount ?? 0) / 100);

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'phone' => $member->phone,
                'role' => $member->role,
                'salary_type' => $member->salary_type,
                'salary_amount' => $member->salary_amount,
                'fixed_amount' => $member->salary_type === 'salary' ? number_format($member->salary_amount, 2) : 0,
                'percentage' => $member->salary_type === 'percentage' ? number_format($member->salary_amount, 2) : 0,
                'calculated_salary' => round($calculated, 2),
                'is_captain' => $member->role === 'captain',
                'is_crew' => $member->role === 'crew',
            ];
        });

        $totalCrewSalary = $crewWithCaptain->sum('calculated_salary');
        $balanceAfterDistribution = $remainingBalance - $totalCrewSalary;

        // 10. إضافة الرصيد المرحل من آخر راتب (إذا موجود)
        $previousPayroll = Payroll::where('boat_id', $boat->id)
            ->where('status', 'closed') // فقط الرصيد المرحل من راتب مغلق
            ->latest('period_to')
            ->first();

        $carryOver = ($previousPayroll->carry_over ?? 0) + $balanceAfterDistribution;

        return [
            'total_revenues' => round($totalRevenues, 2),
            'total_expenses' => round($expenses, 2),
            'owner_profit_percent' => $ownerProfitPercent,
            'owner_net_profit' => round($ownerNetProfit, 2),
            'remaining_balance' => round($remainingBalance, 2),
            'crew' => $crewWithCaptain,
            'total_crew_salary' => $totalCrewSalary,
            'balance_after_distribution' => round($balanceAfterDistribution, 2),
            'carry_over' => round($carryOver, 2),
        ];
    }

    public function calculateMonthlyPayroll(int $year, int $month)
    {
        $payroll = PayrollModel::create([
            'year' => $year,
            'month' => $month,
            'status' => 'draft',
            'type' => 'salary',
        ]);
        $users = User::whereIn('role', ['employee', 'crew', 'captain'])->get();

        foreach ($users as $user) {
            if ($user->salary_type === 'salary') {
                $base_salary = 0;
                $percentage = 0;
                $sales_amount = 0;
                $final_salary = $base_salary = $user->salary_amount;
                $payrollDetail = PayrollDetailsModel::create([
                    'payroll_id' => $payroll->id,
                    'user_id' => $user->id,
                    'base_salary' => $base_salary,
                    'percentage' => $percentage,
                    'sales_amount' => $sales_amount,
                    'final_salary' => $final_salary,
                ]);
            }
        }

        return PayrollModel::with('details', 'details.user')->find($payroll->id);
    }

    public function calculateMonthlyPayrollPercentage(int $year, int $month)
    {
        $payroll = PayrollModel::create([
            'year' => $year,
            'month' => $month,
            'status' => 'draft',
            'type' => 'percentage',
        ]);
        $users = User::whereIn('role', ['employee', 'crew', 'captain'])->get();

        foreach ($users as $user) {
            if ($user->salary_type === 'percentage') {
                $base_salary = 0;
                $percentage = 0;
                $sales_amount = 0;
                $percentage = $user->salary_amount;
                $total_captins_salary = $sales_amount = $this->calculatePercentageSalary($user, $year, $month);
                $total_captins = User::whereIn('role', ['crew', 'captain'])->where('salary_type', 'percentage')->where('boat_id', $user->boat_id)->count();
                $final_salary = ($total_captins_salary / $total_captins);
                $payrollDetail = PayrollDetailsModel::create([
                    'payroll_id' => $payroll->id,
                    'user_id' => $user->id,
                    'base_salary' => $base_salary,
                    'percentage' => $percentage,
                    'sales_amount' => $sales_amount,
                    'final_salary' => $final_salary,
                    'captins_amount' => $total_captins_salary,
                    'captins_count' => $total_captins,
                ]);
            }
        }

        return PayrollModel::with('details', 'details.user')->find($payroll->id);
    }

    public function calculatePercentageSalary(User $user, int $year, int $month)
    {
        if ($user->salary_type === 'percentage' && filled($user->salary_amount)) {

            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

            $ownerId = $user->owner_id ?? auth()->id();

            $trips = Trip::where('boat_id', $user->boat_id)->pluck('id');
            $sales = (float) Sale::whereIn('trip_id', $trips)
                ->whereBetween(DB::raw('DATE(sale_datetime)'), [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('total_price');

            $salaries = PayrollModel::where('year', $year)
                ->where('month', $month)
                ->where('type', 'salary')
                ->first();

            $captins = User::whereIn('role', ['crew', 'captain'])->where('salary_type', 'salary')->where('boat_id', $user->boat_id)->pluck('id');
            $employees = User::whereIn('role', ['employee'])->where('salary_type', 'salary')->where('owner_id', $ownerId)->pluck('id');
            $boats = max(Boat::active()->where('owner_id', $ownerId)->count(), 1);

            $captinsTotalSalaries = $salaries
                ? (float) $salaries->details()->whereIn('user_id', $captins)->sum('final_salary')
                : 0.0;
            $employeesTotalSalaries = $salaries
                ? (float) $salaries->details()->whereIn('user_id', $employees)->sum('final_salary')
                : 0.0;

            $expenses = (float) Expense::where('boat_id', $user->boat_id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('final_price');

            $totalIncome = $sales - ($expenses + $captinsTotalSalaries + ($employeesTotalSalaries / $boats));

            return $totalIncome / 2;
        }

        return 0;
    }
}
