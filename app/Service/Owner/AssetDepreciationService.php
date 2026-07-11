<?php

namespace App\Service\Owner;

use App\Models\Asset;
use Carbon\Carbon;

/**
 * Straight-line asset depreciation charged inside the monthly close only.
 *
 * For each active asset the depreciable base (purchase cost − salvage value) is
 * spread evenly over its useful life. The monthly charge is the annual amount
 * divided by twelve and is only billed for months that fall within the asset's
 * useful life (from its purchase month, exclusive of the month it is fully
 * written off). The total is deducted from the month's profit before the crew
 * distribution; see {@see MonthlyFinancialsService::compute()}.
 */
class AssetDepreciationService
{
    /**
     * Monthly depreciation for an owner's active assets in a calendar month,
     * scoped to a single boat when provided.
     *
     * @return array{total: float, assets: array<int, array{name: string, purchase_cost: float, annual: float, monthly: float}>}
     */
    public function forMonth(int $ownerId, int $year, int $month, ?int $boatId = null): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();

        $query = Asset::where('owner_id', $ownerId)
            ->where('status', 'active')
            ->where('depreciation_method', 'straight_line')
            ->where('useful_life_years', '>', 0);

        if ($boatId !== null) {
            $query->where('boat_id', $boatId);
        }

        $assets = [];
        $total = 0.0;

        foreach ($query->get() as $asset) {
            $monthly = $this->monthlyAmount($asset);

            if ($monthly <= 0 || ! $this->isWithinUsefulLife($asset, $monthStart)) {
                continue;
            }

            $monthly = round($monthly, 2);
            $total += $monthly;

            $assets[] = [
                'name' => (string) $asset->name,
                'purchase_cost' => round((float) $asset->purchase_cost, 2),
                'annual' => round($monthly * 12, 2),
                'monthly' => $monthly,
            ];
        }

        return [
            'total' => round($total, 2),
            'assets' => $assets,
        ];
    }

    /**
     * Month-by-month straight-line depreciation for an owner's active assets
     * across a full calendar year, plus a per-asset breakdown. Reuses the same
     * straight-line / useful-life rules as {@see forMonth()} so the yearly view
     * reconciles with each month's own charge.
     *
     * Each asset also carries its lifetime depreciation progress as of today:
     * total useful-life months, months already depreciated, months remaining,
     * the amount depreciated so far and the amount still to depreciate.
     *
     * @return array{
     *     months: array<int, float>,
     *     year_total: float,
     *     assets: array<int, array{id: int, name: string, type: string, purchase_cost: float, useful_life_years: int, monthly: float, months_charged: int, year_total: float, total_months: int, months_paid: int, remaining_months: int, paid_so_far: float, remaining: float}>
     * }
     */
    public function forYear(int $ownerId, int $year, ?int $boatId = null): array
    {
        $query = Asset::where('owner_id', $ownerId)
            ->where('status', 'active')
            ->where('depreciation_method', 'straight_line')
            ->where('useful_life_years', '>', 0);

        if ($boatId !== null) {
            $query->where('boat_id', $boatId);
        }

        $months = array_fill(1, 12, 0.0);
        $assets = [];
        $yearTotal = 0.0;

        foreach ($query->get() as $asset) {
            $monthly = round($this->monthlyAmount($asset), 2);

            if ($monthly <= 0) {
                continue;
            }

            $monthsCharged = 0;

            for ($month = 1; $month <= 12; $month++) {
                $monthStart = Carbon::create($year, $month, 1)->startOfMonth();

                if (! $this->isWithinUsefulLife($asset, $monthStart)) {
                    continue;
                }

                $months[$month] += $monthly;
                $monthsCharged++;
            }

            if ($monthsCharged === 0) {
                continue;
            }

            $assetYearTotal = round($monthly * $monthsCharged, 2);
            $yearTotal += $assetYearTotal;

            [$totalMonths, $monthsPaid, $remainingMonths] = $this->lifetimeProgress($asset);
            $paidSoFar = round($monthly * $monthsPaid, 2);
            $remaining = round($monthly * $remainingMonths, 2);

            $assets[] = [
                'id' => (int) $asset->id,
                'name' => (string) $asset->name,
                'type' => (string) $asset->asset_type,
                'purchase_cost' => round((float) $asset->purchase_cost, 2),
                'useful_life_years' => (int) $asset->useful_life_years,
                'monthly' => $monthly,
                'months_charged' => $monthsCharged,
                'year_total' => $assetYearTotal,
                'total_months' => $totalMonths,
                'months_paid' => $monthsPaid,
                'remaining_months' => $remainingMonths,
                'paid_so_far' => $paidSoFar,
                'remaining' => $remaining,
            ];
        }

        return [
            'months' => array_map(fn (float $value): float => round($value, 2), $months),
            'year_total' => round($yearTotal, 2),
            'assets' => $assets,
        ];
    }

    /**
     * Lifetime depreciation progress for an asset as of the current month:
     * total useful-life months, months already depreciated (from the purchase
     * month, inclusive, capped at the useful life) and months remaining.
     *
     * @return array{0: int, 1: int, 2: int} [total_months, months_paid, remaining_months]
     */
    private function lifetimeProgress(Asset $asset): array
    {
        $totalMonths = (int) $asset->useful_life_years * 12;

        if ($totalMonths <= 0 || empty($asset->purchase_date)) {
            return [$totalMonths, 0, $totalMonths];
        }

        $start = Carbon::parse($asset->purchase_date)->startOfMonth();
        $now = Carbon::now()->startOfMonth();

        $monthsPaid = $now->greaterThanOrEqualTo($start)
            ? (int) abs($start->diffInMonths($now)) + 1
            : 0;

        $monthsPaid = max(0, min($monthsPaid, $totalMonths));

        return [$totalMonths, $monthsPaid, $totalMonths - $monthsPaid];
    }

    /**
     * Straight-line monthly depreciation for a single asset.
     */
    private function monthlyAmount(Asset $asset): float
    {
        $life = (int) $asset->useful_life_years;

        if ($life <= 0) {
            return 0.0;
        }

        $depreciable = (float) $asset->purchase_cost - (float) $asset->salvage_value;

        return $depreciable > 0 ? ($depreciable / $life) / 12 : 0.0;
    }

    /**
     * Whether the given month falls within the asset's useful life window.
     */
    private function isWithinUsefulLife(Asset $asset, Carbon $monthStart): bool
    {
        if (empty($asset->purchase_date)) {
            return false;
        }

        $start = Carbon::parse($asset->purchase_date)->startOfMonth();
        $end = $start->copy()->addYears((int) $asset->useful_life_years);

        return $monthStart->greaterThanOrEqualTo($start) && $monthStart->lessThan($end);
    }
}
