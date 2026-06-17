<?php

namespace App\Service\Owner;

use App\Models\CatchDetail;
use App\Models\Trip;
use Illuminate\Support\Collection;

/**
 * Single source of truth for a trip's financial figures so the trip view and the
 * printed trip report always show identical numbers.
 *
 * Model (per the owner's definition):
 * - الإهلاك (depreciation) is every paid cost neither the owner nor the crew keep
 *   — i.e. commission + labor (العمولة + النولة). Its percentage is that amount
 *   over total sales.
 * - net profit = total income (gross sales) − total expenses (depreciation).
 * - net profit is split in half: owner 50% / crew 50%.
 * - the crew share is distributed across the boat's percentage crew (captain
 *   included), honouring any custom percentage, via {@see MonthlyFinancialsService::crewDistribution()}.
 */
class TripFinancialsService
{
    public const OWNER_PERCENT = 50.0;

    public function __construct(private MonthlyFinancialsService $monthly) {}

    /**
     * @return array{
     *     catch_weight: float, gross_revenue: float, depreciation: float,
     *     depreciation_percent: float, total_costs: float, total_income: float,
     *     total_expenses: float, net_profit: float, owner_share: float,
     *     crew_share: float, crew_count: int, per_crew: float,
     *     outstanding: float,
     *     catch_weight_by_unit: \Illuminate\Support\Collection<string, float>,
     *     crew_members: \Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    public function compute(Trip $trip): array
    {
        $grossRevenue = (float) $trip->sales->sum('total_price');
        $commission = (float) $trip->sales->sum('commission_amount');
        $labor = (float) $trip->sales->sum('labor_amount');

        $depreciation = $commission + $labor;
        $depreciationPercent = $grossRevenue > 0
            ? round($depreciation / $grossRevenue * 100, 2)
            : 0.0;

        $netProfit = $grossRevenue - $depreciation;
        $ownerShare = round($netProfit * (self::OWNER_PERCENT / 100), 2);
        $crewShare = round($netProfit - $ownerShare, 2);

        $crewMembers = $this->crewSalaries($trip, $crewShare);

        $catchWeight = (float) ($trip->catches?->total_weight ?? 0);
        if ($catchWeight <= 0 && $trip->catches) {
            $catchWeight = (float) $trip->catches->details->sum('weight');
        }

        $catchWeightByUnit = $trip->catches
            ? $trip->catches->details
                ->groupBy(fn (CatchDetail $detail): string => $detail->unit->name ?: __('owner.units.kg'))
                ->map(fn (Collection $group): float => (float) $group->sum('weight'))
            : collect();

        return [
            'catch_weight' => $catchWeight,
            'catch_weight_by_unit' => $catchWeightByUnit,
            'gross_revenue' => round($grossRevenue, 2),
            'total_income' => round($grossRevenue, 2),
            'depreciation' => round($depreciation, 2),
            'depreciation_percent' => $depreciationPercent,
            'total_costs' => round($depreciation, 2),
            'total_expenses' => round($depreciation, 2),
            'net_profit' => round($netProfit, 2),
            'owner_share' => $ownerShare,
            'crew_share' => $crewShare,
            'crew_count' => $crewMembers->count(),
            'per_crew' => $crewMembers->isNotEmpty() ? round($crewShare / $crewMembers->count(), 2) : 0.0,
            'outstanding' => (float) $trip->sales->sum('remaining_total'),
            'crew_members' => $crewMembers,
        ];
    }

    /**
     * Per-member crew payout sheet (الاسم / النسبة / المبلغ / التوقيع), with each
     * member's percentage of the crew pool resolved.
     *
     * @return \Illuminate\Support\Collection<int, array{user_id:int, name:string, role:string, custom_percent: float|null, percent: float, due: float}>
     */
    private function crewSalaries(Trip $trip, float $crewShare): Collection
    {
        if (! $trip->boat_id || ! $trip->owner_id) {
            return collect();
        }

        $distribution = $this->monthly->crewDistribution($trip->owner_id, $crewShare, $trip->boat_id);

        return $distribution['members']->map(function (array $member) use ($crewShare): array {
            $member['percent'] = $member['custom_percent'] !== null
                ? $member['custom_percent']
                : ($crewShare > 0 ? round($member['due'] / $crewShare * 100, 2) : 0.0);

            return $member;
        })->values();
    }
}
