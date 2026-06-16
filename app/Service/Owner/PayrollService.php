<?php

namespace App\Service\Owner;

use App\Models\Boat;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
            ->where('seller_type', 'owner')
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

    public function calculateMonthlyPayrollPercentage(int $ownerId, int $year, int $month)
    {
        $payroll = PayrollModel::create([
            'owner_id' => $ownerId,
            'year' => $year,
            'month' => $month,
            'status' => 'draft',
            'type' => 'percentage',
        ]);
        $users = User::where('owner_id', $ownerId)
            ->whereIn('role', ['employee', 'crew', 'captain'])
            ->get();

        foreach ($users as $user) {
            if ($user->salary_type === 'percentage') {
                $base_salary = 0;
                $percentage = 0;
                $sales_amount = 0;
                $percentage = $user->salary_amount;
                $total_captins_salary = $sales_amount = $this->calculatePercentageSalary($user, $year, $month);
                $total_captins = User::where('owner_id', $ownerId)
                    ->whereIn('role', ['crew', 'captain'])
                    ->where('salary_type', 'percentage')
                    ->where('boat_id', $user->boat_id)
                    ->count();
                $final_salary = $total_captins > 0 ? ($total_captins_salary / $total_captins) : 0;
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

            $ownerId = $user->owner_id ?? Auth::guard('owner')->id();

            $trips = Trip::where('boat_id', $user->boat_id)->pluck('id');
            $sales = (float) Sale::whereIn('trip_id', $trips)
                ->where('seller_type', 'owner')
                ->where('seller_id', $ownerId)
                ->whereBetween(DB::raw('DATE(sale_datetime)'), [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('total_price');

            $expenses = (float) Expense::where('owner_id', $ownerId)
                ->where('boat_id', $user->boat_id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('final_price');

            $totalIncome = $sales - $expenses;

            $ownerPercent = (float) (Setting::where('key', MonthlyFinancialsService::SETTING_OWNER_PERCENT)->value('value')
                ?? MonthlyFinancialsService::DEFAULT_OWNER_PERCENT);

            return $totalIncome * ((100 - $ownerPercent) / 100);
        }

        return 0;
    }

    /**
     * Read-only crew percentage payroll payment summary for a month, used by the
     * month-close report. Does not alter any financial totals.
     *
     * @return array{percentage: array<string, mixed>}
     */
    public function monthlyPayrollSummary(int $ownerId, int $year, int $month): array
    {
        return [
            'percentage' => $this->payrollTypeSummary($ownerId, $year, $month, 'percentage'),
        ];
    }

    /**
     * Amount actually disbursed to each crew member through the month's
     * percentage payroll, keyed by user id. Links the month-close crew dues to
     * the real per-person payments recorded in {@see PayrollController::payDetail()}.
     *
     * @return array<int, float>
     */
    public function monthlyPercentagePaidByUser(int $ownerId, int $year, int $month): array
    {
        $payroll = PayrollModel::with('details')
            ->where('owner_id', $ownerId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('type', 'percentage')
            ->first();

        if (! $payroll) {
            return [];
        }

        return $payroll->details
            ->where('is_paid', true)
            ->groupBy('user_id')
            ->map(fn ($rows) => round((float) $rows->sum('paid_amount'), 2))
            ->all();
    }

    /**
     * @return array{exists: bool, payroll_id: int|null, net_total: float, paid_amount: float, count: int, paid_count: int, status: string, paid_at: \Carbon\Carbon|null}
     */
    private function payrollTypeSummary(int $ownerId, int $year, int $month, string $type): array
    {
        $payroll = PayrollModel::with('details')
            ->where('owner_id', $ownerId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('type', $type)
            ->first();

        if (! $payroll) {
            return [
                'exists' => false,
                'payroll_id' => null,
                'net_total' => 0.0,
                'paid_amount' => 0.0,
                'count' => 0,
                'paid_count' => 0,
                'status' => 'not_created',
                'paid_at' => null,
            ];
        }

        $details = $payroll->details;
        $count = $details->count();
        $paidCount = $details->where('is_paid', true)->count();

        $status = 'unpaid';
        if ($count > 0 && $paidCount === $count) {
            $status = 'fully_paid';
        } elseif ($paidCount > 0) {
            $status = 'partially_paid';
        }

        return [
            'exists' => true,
            'payroll_id' => $payroll->id,
            'net_total' => round((float) $details->sum('final_salary'), 2),
            'paid_amount' => round((float) $details->where('is_paid', true)->sum('paid_amount'), 2),
            'count' => $count,
            'paid_count' => $paidCount,
            'status' => $status,
            'paid_at' => $details->where('is_paid', true)->max('paid_at'),
        ];
    }
}
