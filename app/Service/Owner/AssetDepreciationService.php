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
