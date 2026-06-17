<?php

namespace App\Service\Owner;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
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

    /**
     * Compute the full monthly financial waterfall for an owner.
     *
     * @return array{
     *     from: string, to: string, owner_id: int, boat_id: int|null,
     *     gross_sales: float, net_sales: float,
     *     commission_labor: float, net_owner_revenue: float,
     *     trip_expenses: float, general_expenses: float,
     *     depreciation: float, total_expenses: float, net_profit: float,
     *     owner_percent: float, owner_share: float, crew_share: float,
     *     crew_count: int, per_fisherman: float
     * }
     */
    public function compute(int $ownerId, string $from, string $to, ?int $boatId = null): array
    {
        $saleIds = $this->ownerSalesQuery($ownerId, $from, $to, $boatId)->pluck('id');

        $grossSales = (float) Sale::whereIn('id', $saleIds)->sum('total_price');
        $netOwnerSales = (float) Sale::whereIn('id', $saleIds)->sum('net_owner_amount');
        $commissionLabor = (float) Sale::whereIn('id', $saleIds)
            ->sum(DB::raw('COALESCE(commission_amount,0) + COALESCE(labor_amount,0)'));

        $netSales = $grossSales;
        $netOwnerRevenue = $netOwnerSales;

        $tripExpenses = (float) $this->expenseQuery($ownerId, $from, $to, $boatId)
            ->whereIn('category_id', $this->categoryIdsForTypes(['operating', 'maintenance']))
            ->sum('final_price');

        $generalExpenses = (float) $this->expenseQuery($ownerId, $from, $to, $boatId)
            ->whereIn('category_id', $this->categoryIdsForTypes(['general', 'government']))
            ->sum('final_price');

        $depreciation = (float) $this->setting(self::SETTING_DEPRECIATION, 0);

        $totalExpenses = $tripExpenses + $generalExpenses + $depreciation;
        $netProfit = $netOwnerRevenue - $totalExpenses;

        $ownerPercent = (float) $this->setting(self::SETTING_OWNER_PERCENT, self::DEFAULT_OWNER_PERCENT);
        $ownerShare = $netProfit * ($ownerPercent / 100);
        $crewShare = $netProfit - $ownerShare;

        $crewCount = $this->participatingCrewQuery($ownerId, $boatId)->count();
        $perFisherman = $crewCount > 0 ? $crewShare / $crewCount : 0.0;

        return [
            'from' => $from,
            'to' => $to,
            'owner_id' => $ownerId,
            'boat_id' => $boatId,
            'gross_sales' => round($grossSales, 2),
            'net_sales' => round($netSales, 2),
            'commission_labor' => round($commissionLabor, 2),
            'net_owner_revenue' => round($netOwnerRevenue, 2),
            'trip_expenses' => round($tripExpenses, 2),
            'general_expenses' => round($generalExpenses, 2),
            'depreciation' => round($depreciation, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
            'owner_percent' => $ownerPercent,
            'owner_share' => round($ownerShare, 2),
            'crew_share' => round($crewShare, 2),
            'crew_count' => $crewCount,
            'per_fisherman' => round($perFisherman, 2),
        ];
    }

    /**
     * Detailed sale & expense records backing the monthly figures, scoped and
     * filtered exactly like {@see compute()} so the totals reconcile line by line.
     *
     * @return array{
     *     sales: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sale>,
     *     expenses: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense>
     * }
     */
    public function details(int $ownerId, string $from, string $to, ?int $boatId = null): array
    {
        $sales = $this->ownerSalesQuery($ownerId, $from, $to, $boatId)
            ->with(['customer', 'trip'])
            ->orderBy('sale_datetime')
            ->get();

        $expenses = $this->expenseQuery($ownerId, $from, $to, $boatId)
            ->whereIn('category_id', $this->categoryIdsForTypes(['operating', 'maintenance', 'general', 'government']))
            ->with(['category', 'vendor'])
            ->orderBy('date')
            ->get();

        return [
            'sales' => $sales,
            'expenses' => $expenses,
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

    private function setting(string $key, float $default): float
    {
        $value = Setting::where('key', $key)->value('value');

        return is_null($value) ? $default : (float) $value;
    }
}
