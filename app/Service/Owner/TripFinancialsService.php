<?php

namespace App\Service\Owner;

use App\Models\CatchDetail;
use App\Models\Trip;
use App\Models\TripBoat;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Single source of truth for a trip's financial figures so the trip view and the
 * printed trip report always show identical numbers.
 *
 * A trip may span several boats. Each boat is costed independently — its own
 * sales, its own catch, its own depreciation (العمولة + النولة), and its own
 * 50/50 owner/crew split distributed across that boat's crew plus the captains
 * assigned to it for this trip. The trip headline figures are the sum of the
 * per-boat results, exposed under the same keys the views already consumed, with
 * an additional `boats` collection holding the per-boat breakdown.
 *
 * Legacy / factory trips that predate the multi-boat model (no `trip_boats`
 * rows) are treated as a single boat built from the trip's own boat/captain
 * snapshot, so their numbers are unchanged.
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
     *     crew_members: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     boats: \Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    public function compute(Trip $trip): array
    {
        $trip->loadMissing([
            'tripBoats.boat',
            'tripBoats.captains',
            'catchModels.details.fish',
            'catchModels.details.unit',
            'sales',
            'boat',
            'captain',
        ]);

        $boats = $this->boatEntries($trip);

        $grossRevenue = (float) $boats->sum('gross_revenue');
        $depreciation = (float) $boats->sum('depreciation');
        $netProfit = (float) $boats->sum('net_profit');
        $ownerShare = (float) $boats->sum('owner_share');
        $crewShare = (float) $boats->sum('crew_share');
        $outstanding = (float) $boats->sum('outstanding');
        $catchWeight = (float) $boats->sum('catch_weight');

        $crewMembers = $boats->flatMap(fn (array $boat): Collection => $boat['crew_members'])->values();

        $catchWeightByUnit = collect();
        foreach ($boats as $boat) {
            foreach ($boat['catch_weight_by_unit'] as $unit => $weight) {
                $catchWeightByUnit[$unit] = ($catchWeightByUnit[$unit] ?? 0.0) + (float) $weight;
            }
        }

        $crewCount = $crewMembers->count();
        $depreciationPercent = $grossRevenue > 0 ? round($depreciation / $grossRevenue * 100, 2) : 0.0;

        return [
            'catch_weight' => round($catchWeight, 2),
            'catch_weight_by_unit' => $catchWeightByUnit,
            'gross_revenue' => round($grossRevenue, 2),
            'total_income' => round($grossRevenue, 2),
            'depreciation' => round($depreciation, 2),
            'depreciation_percent' => $depreciationPercent,
            'total_costs' => round($depreciation, 2),
            'total_expenses' => round($depreciation, 2),
            'net_profit' => round($netProfit, 2),
            'owner_share' => round($ownerShare, 2),
            'crew_share' => round($crewShare, 2),
            'crew_count' => $crewCount,
            'per_crew' => $crewCount > 0 ? round($crewShare / $crewCount, 2) : 0.0,
            'outstanding' => round($outstanding, 2),
            'crew_members' => $crewMembers,
            'boats' => $boats,
        ];
    }

    /**
     * Per-boat financial breakdown for the trip. Falls back to a single
     * synthesized boat for trips with no trip_boats rows.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function boatEntries(Trip $trip): Collection
    {
        $tripBoats = $trip->tripBoats;

        if ($tripBoats->isEmpty()) {
            return collect([$this->computeBoat($trip, null, true)]);
        }

        $single = $tripBoats->count() === 1;

        return $tripBoats->map(fn (TripBoat $tripBoat): array => $this->computeBoat($trip, $tripBoat, $single))->values();
    }

    /**
     * Compute one boat's figures. When $tripBoat is null the boat is synthesized
     * from the trip's own boat/captain snapshot (legacy single-boat trips).
     *
     * @param  bool  $single  whether this is the trip's only boat — legacy
     *                        catches/sales with no boat_id then fall to it.
     * @return array<string, mixed>
     */
    private function computeBoat(Trip $trip, ?TripBoat $tripBoat, bool $single): array
    {
        $boatId = $tripBoat?->boat_id ?? $trip->boat_id;
        $boat = $tripBoat?->boat ?? $trip->boat;

        $captainUsers = $tripBoat
            ? $tripBoat->captains
            : collect($trip->captain && $trip->captain->exists ? [$trip->captain] : []);

        $sales = $single
            ? $trip->sales
            : $trip->sales->where('boat_id', $boatId);

        $catch = $single
            ? $trip->catchModels->first()
            : $trip->catchModels->firstWhere('boat_id', $boatId);

        $grossRevenue = (float) $sales->sum('total_price');
        $commission = (float) $sales->sum('commission_amount');
        $labor = (float) $sales->sum('labor_amount');
        $depreciation = $commission + $labor;

        $netProfit = $grossRevenue - $depreciation;
        $ownerShare = round($netProfit * (self::OWNER_PERCENT / 100), 2);
        $crewShare = round($netProfit - $ownerShare, 2);

        $catchDetails = $catch?->details ?? collect();
        $catchWeight = (float) ($catch?->total_weight ?? 0);
        if ($catchWeight <= 0) {
            $catchWeight = (float) $catchDetails->sum('weight');
        }

        $catchWeightByUnit = $catchDetails
            ->groupBy(fn (CatchDetail $detail): string => $detail->unit->name ?: __('owner.units.kg'))
            ->map(fn (Collection $group): float => (float) $group->sum('weight'));

        $crewMembers = $this->crewSalaries($trip, $boatId, $captainUsers, $crewShare);

        return [
            'trip_boat_id' => $tripBoat?->id,
            'boat_id' => $boatId,
            'boat_name' => $tripBoat?->boat_name ?? $trip->boat_name ?? ($boat?->name ?? '—'),
            'boat_number' => $tripBoat?->boat_number ?? $trip->boat_number,
            'boat_color' => $tripBoat?->boat_color ?? $trip->boat_color,
            'boat_length' => $tripBoat?->boat_length ?? $trip->boat_length,
            'boat_width' => $tripBoat?->boat_width ?? $trip->boat_width,
            'crew_count' => (int) ($tripBoat?->crew_count ?? $trip->crew_count ?? 0),
            'captains' => $captainUsers->map(fn (User $captain): array => [
                'id' => $captain->id,
                'name' => $captain->name,
                'phone' => $captain->phone,
            ])->values(),
            'catch' => $catch,
            'catch_details' => $catchDetails,
            'catch_weight' => $catchWeight,
            'catch_weight_by_unit' => $catchWeightByUnit,
            'sales' => $sales->values(),
            'gross_revenue' => round($grossRevenue, 2),
            'total_income' => round($grossRevenue, 2),
            'depreciation' => round($depreciation, 2),
            'depreciation_percent' => $grossRevenue > 0 ? round($depreciation / $grossRevenue * 100, 2) : 0.0,
            'total_costs' => round($depreciation, 2),
            'total_expenses' => round($depreciation, 2),
            'net_profit' => round($netProfit, 2),
            'owner_share' => $ownerShare,
            'crew_share' => $crewShare,
            'outstanding' => (float) $sales->sum('remaining_total'),
            'crew_members' => $crewMembers,
        ];
    }

    /**
     * Per-member crew payout sheet (الاسم / النسبة / المبلغ / التوقيع) for one
     * boat: its percentage crew plus the captains assigned to it for this trip.
     *
     * @param  \Illuminate\Support\Collection<int, User>  $captainUsers
     * @return \Illuminate\Support\Collection<int, array{user_id:int, name:string, role:string, custom_percent: float|null, percent: float, due: float}>
     */
    private function crewSalaries(Trip $trip, ?int $boatId, Collection $captainUsers, float $crewShare): Collection
    {
        if (! $trip->owner_id) {
            return collect();
        }

        $crew = collect();
        if ($boatId) {
            $crew = User::query()
                ->where('owner_id', $trip->owner_id)
                ->where('boat_id', $boatId)
                ->where('role', 'crew')
                ->where('salary_type', 'percentage')
                ->get();
        }

        $captains = $captainUsers->filter(fn (User $captain): bool => $captain->salary_type === 'percentage');

        $members = $crew->concat($captains)->unique('id')->values();

        $distribution = $this->monthly->distributeAmong($members, $crewShare);

        return $distribution['members']->map(function (array $member) use ($crewShare): array {
            $member['percent'] = $member['custom_percent'] !== null
                ? $member['custom_percent']
                : ($crewShare > 0 ? round($member['due'] / $crewShare * 100, 2) : 0.0);

            return $member;
        })->values();
    }
}
