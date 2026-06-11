<?php

namespace App\Service\Owner;

use App\Models\Category;
use App\Models\Expense;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Canonical monthly financial calculation for the owner panel.
 *
 * Every owner financial page/report MUST consume this service so the same month
 * never shows two different numbers. See docs/dashboard-reports-overhaul-plan.md
 * Part 2 for the formula. Each value is computed exactly once; there are no
 * silent adjustments (no hidden depreciation haircut on sales).
 */
class MonthlyFinancialsService
{
    public const SETTING_OWNER_PERCENT = 'owner_profit_percent';

    public const SETTING_DEPRECIATION = 'monthly_depreciation_amount';

    public const DEFAULT_OWNER_PERCENT = 50.0;

    public const VAT_RATE = 15.0;

    /**
     * Compute the full monthly financial waterfall for an owner.
     *
     * @return array{
     *     from: string, to: string, owner_id: int, boat_id: int|null,
     *     gross_sales: float, returns: float, net_sales: float,
     *     commission_labor: float, net_owner_revenue: float,
     *     trip_expenses: float, general_expenses: float, fixed_salaries: float,
     *     depreciation: float, total_expenses: float, net_profit: float,
     *     owner_percent: float, owner_share: float, crew_share: float,
     *     crew_count: int, per_fisherman: float,
     *     sales_vat: float, expenses_vat: float
     * }
     */
    public function compute(int $ownerId, string $from, string $to, ?int $boatId = null): array
    {
        $saleIds = $this->ownerSalesQuery($ownerId, $from, $to, $boatId)->pluck('id');

        $grossSales = (float) Sale::whereIn('id', $saleIds)->sum('total_price');
        $netOwnerSales = (float) Sale::whereIn('id', $saleIds)->sum('net_owner_amount');
        $commissionLabor = (float) Sale::whereIn('id', $saleIds)
            ->sum(DB::raw('COALESCE(commission_amount,0) + COALESCE(labor_amount,0)'));

        $returns = (float) DB::table('returns')
            ->whereIn('sale_id', $saleIds)
            ->where('status', 'approved')
            ->sum('total_amount');

        $netSales = $grossSales - $returns;
        $netOwnerRevenue = $netOwnerSales - $returns;

        $tripExpenses = (float) $this->expenseQuery($ownerId, $from, $to, $boatId)
            ->whereIn('category_id', $this->categoryIdsForTypes(['operating', 'maintenance']))
            ->sum('final_price');

        $generalExpenses = (float) $this->expenseQuery($ownerId, $from, $to, $boatId)
            ->whereIn('category_id', $this->categoryIdsForTypes(['general', 'government']))
            ->sum('final_price');

        $fixedSalaries = $this->fixedSalaries($ownerId, $from, $boatId);
        $depreciation = (float) $this->setting(self::SETTING_DEPRECIATION, 0);

        $totalExpenses = $tripExpenses + $generalExpenses + $fixedSalaries + $depreciation;
        $netProfit = $netOwnerRevenue - $totalExpenses;

        $ownerPercent = (float) $this->setting(self::SETTING_OWNER_PERCENT, self::DEFAULT_OWNER_PERCENT);
        $ownerShare = $netProfit * ($ownerPercent / 100);
        $crewShare = $netProfit - $ownerShare;

        $crewCount = $this->participatingCrewQuery($ownerId, $boatId)->count();
        $perFisherman = $crewCount > 0 ? $crewShare / $crewCount : 0.0;

        $expensesVat = $this->expensesVat($ownerId, $from, $to, $boatId);
        $salesVat = $this->ownerIsVatApplicable($ownerId)
            ? $grossSales * (self::VAT_RATE / (100 + self::VAT_RATE))
            : 0.0;

        return [
            'from' => $from,
            'to' => $to,
            'owner_id' => $ownerId,
            'boat_id' => $boatId,
            'gross_sales' => round($grossSales, 2),
            'returns' => round($returns, 2),
            'net_sales' => round($netSales, 2),
            'commission_labor' => round($commissionLabor, 2),
            'net_owner_revenue' => round($netOwnerRevenue, 2),
            'trip_expenses' => round($tripExpenses, 2),
            'general_expenses' => round($generalExpenses, 2),
            'fixed_salaries' => round($fixedSalaries, 2),
            'depreciation' => round($depreciation, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
            'owner_percent' => $ownerPercent,
            'owner_share' => round($ownerShare, 2),
            'crew_share' => round($crewShare, 2),
            'crew_count' => $crewCount,
            'per_fisherman' => round($perFisherman, 2),
            'sales_vat' => round($salesVat, 2),
            'expenses_vat' => round($expensesVat, 2),
        ];
    }

    /**
     * Distribute a crew pool across members by their profit shares (أسهم).
     * Equal split is the special case where every member has shares = 1.
     *
     * @param  array<int|string, float>  $memberShares  [memberId => shares]
     * @return array{share_value: float, total_shares: float, dues: array<int|string, float>}
     */
    public function distributeCrewPool(float $crewPool, array $memberShares): array
    {
        $totalShares = array_sum($memberShares);
        $shareValue = $totalShares > 0 ? round($crewPool / $totalShares, 2) : 0.0;

        $dues = [];
        foreach ($memberShares as $memberId => $shares) {
            $dues[$memberId] = round($shareValue * $shares, 2);
        }

        return [
            'share_value' => $shareValue,
            'total_shares' => (float) $totalShares,
            'dues' => $dues,
        ];
    }

    /**
     * Base query for the owner's own sales within the period (sale_datetime basis).
     */
    private function ownerSalesQuery(int $ownerId, string $from, string $to, ?int $boatId)
    {
        $query = Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]);

        if ($boatId) {
            $query->whereIn('trip_id', Trip::where('boat_id', $boatId)->pluck('id'));
        }

        return $query;
    }

    /**
     * Base expense query, owner-scoped and on the business `date` column.
     */
    private function expenseQuery(int $ownerId, string $from, string $to, ?int $boatId)
    {
        $query = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereBetween('date', [$from, $to]);

        if ($boatId) {
            $query->where('boat_id', $boatId);
        }

        return $query;
    }

    /**
     * @param  array<int, string>  $types
     * @return array<int, int>
     */
    private function categoryIdsForTypes(array $types): array
    {
        return Category::whereIn('type', $types)->pluck('id')->all();
    }

    /**
     * Σ payroll_details_models.final_salary for the month's salary payroll,
     * scoped to the owner's salaried staff (and boat when filtered).
     */
    private function fixedSalaries(int $ownerId, string $from, ?int $boatId): float
    {
        $date = Carbon::parse($from);

        $payrollIds = PayrollModel::query()
            ->where(function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId)->orWhereNull('owner_id');
            })
            ->where('type', 'salary')
            ->where('year', $date->year)
            ->where('month', $date->month)
            ->pluck('id');

        if ($payrollIds->isEmpty()) {
            return 0.0;
        }

        $staffQuery = User::query()
            ->where('owner_id', $ownerId)
            ->where('salary_type', 'salary');

        if ($boatId) {
            $staffQuery->whereIn('role', ['crew', 'captain'])->where('boat_id', $boatId);
        } else {
            $staffQuery->whereIn('role', ['crew', 'captain', 'employee']);
        }

        return (float) PayrollDetailsModel::query()
            ->whereIn('payroll_id', $payrollIds)
            ->whereIn('user_id', $staffQuery->pluck('id'))
            ->sum('final_salary');
    }

    /**
     * Owner's percentage crew + captains participating in the crew pool.
     */
    private function participatingCrewQuery(int $ownerId, ?int $boatId)
    {
        $query = User::query()
            ->where('owner_id', $ownerId)
            ->whereIn('role', ['crew', 'captain'])
            ->where('salary_type', 'percentage');

        if ($boatId) {
            $query->where('boat_id', $boatId);
        }

        return $query;
    }

    private function expensesVat(int $ownerId, string $from, string $to, ?int $boatId): float
    {
        return (float) $this->expenseQuery($ownerId, $from, $to, $boatId)
            ->where('vat_rate', '>', 0)
            ->get(['final_price', 'vat_rate'])
            ->sum(function ($expense): float {
                $rate = (float) $expense->vat_rate;
                $final = (float) $expense->final_price;

                return $final - ($final / (1 + $rate / 100));
            });
    }

    private function ownerIsVatApplicable(int $ownerId): bool
    {
        return (bool) User::whereKey($ownerId)->value('is_vat_applicable');
    }

    private function setting(string $key, float $default): float
    {
        $value = Setting::where('key', $key)->value('value');

        return is_null($value) ? $default : (float) $value;
    }
}
