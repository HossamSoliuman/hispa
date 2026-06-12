<?php

namespace App\Service\Owner;

use App\Enums\TripStatus;
use App\Models\CrewAdvance;
use App\Models\MonthClosing;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Month-close (إقفال شهر الصيد).
 *
 * Computes the fleet-level monthly distribution from the canonical
 * MonthlyFinancialsService, splits the crew pool by profit shares (أسهم),
 * and freezes the result as an immutable snapshot so later edits to sales or
 * expenses never change a closed month.
 */
class MonthClosingService
{
    public function __construct(
        private MonthlyFinancialsService $financials,
        private PayrollService $payroll,
    ) {}

    /**
     * Build (but do not persist) the distribution for a month.
     *
     * @return array{
     *     year: int, month: int, from: string, to: string,
     *     financials: array<string, mixed>,
     *     dues: array<int, array<string, mixed>>,
     *     total_shares: float, share_value: float,
     *     existing: \App\Models\MonthClosing|null,
     *     warnings: array<int, string>
     * }
     */
    public function preview(int $ownerId, int $year, int $month): array
    {
        [$from, $to] = $this->monthRange($year, $month);

        $financials = $this->financials->compute($ownerId, $from, $to);

        $members = $this->participatingCrew($ownerId);
        $sharesMap = $members->mapWithKeys(fn (User $m) => [$m->id => (float) $m->profit_shares])->all();

        $distribution = $this->financials->distributeCrewPool($financials['crew_share'], $sharesMap);

        $dues = $members->map(function (User $member) use ($distribution, $ownerId, $from, $to) {
            $due = (float) ($distribution['dues'][$member->id] ?? 0);
            $advances = (float) CrewAdvance::where('user_id', $member->id)
                ->where('owner_id', $ownerId)
                ->whereBetween('date', [$from, $to])
                ->sum('amount');

            return [
                'user_id' => $member->id,
                'member_name' => $member->name,
                'role' => $member->role,
                'shares' => (float) $member->profit_shares,
                'share_value' => $distribution['share_value'],
                'due_amount' => round($due, 2),
                'advances' => round($advances, 2),
                'paid_amount' => 0.0,
                'remaining' => round($due - $advances, 2),
            ];
        })->all();

        return [
            'year' => $year,
            'month' => $month,
            'from' => $from,
            'to' => $to,
            'financials' => $financials,
            'dues' => $dues,
            'total_shares' => $distribution['total_shares'],
            'share_value' => $distribution['share_value'],
            'existing' => $this->find($ownerId, $year, $month),
            'warnings' => $this->warnings($ownerId, $from, $to),
        ];
    }

    /**
     * Freeze the month's distribution into an immutable snapshot.
     *
     * @throws \DomainException when the month is already closed.
     */
    public function close(int $ownerId, int $year, int $month, ?int $closedBy = null): MonthClosing
    {
        if ($this->find($ownerId, $year, $month)) {
            throw new \DomainException(__('owner.month_closing.errors.already_closed'));
        }

        $preview = $this->preview($ownerId, $year, $month);
        $f = $preview['financials'];

        return DB::transaction(function () use ($ownerId, $year, $month, $closedBy, $preview, $f) {
            $closing = MonthClosing::create([
                'owner_id' => $ownerId,
                'year' => $year,
                'month' => $month,
                'status' => 'closed',
                'gross_sales' => $f['gross_sales'],
                'returns' => $f['returns'],
                'net_sales' => $f['net_sales'],
                'net_owner_revenue' => $f['net_owner_revenue'],
                'trip_expenses' => $f['trip_expenses'],
                'general_expenses' => $f['general_expenses'],
                'fixed_salaries' => $f['fixed_salaries'],
                'depreciation' => $f['depreciation'],
                'total_expenses' => $f['total_expenses'],
                'net_profit' => $f['net_profit'],
                'owner_percent' => $f['owner_percent'],
                'owner_share' => $f['owner_share'],
                'crew_share' => $f['crew_share'],
                'share_value' => $preview['share_value'],
                'total_shares' => $preview['total_shares'],
                'closed_by' => $closedBy,
                'closed_at' => now(),
            ]);

            foreach ($preview['dues'] as $due) {
                $closing->dues()->create([
                    'user_id' => $due['user_id'],
                    'member_name' => $due['member_name'],
                    'role' => $due['role'],
                    'shares' => $due['shares'],
                    'share_value' => $due['share_value'],
                    'due_amount' => $due['due_amount'],
                    'advances' => $due['advances'],
                    'paid_amount' => 0,
                    'remaining' => $due['remaining'],
                    'is_paid' => false,
                ]);
            }

            return $closing->load('dues');
        });
    }

    /**
     * Reopen a closed month (deletes the snapshot so it can be recomputed).
     *
     * @throws \DomainException when any payment has already been made.
     */
    public function reopen(MonthClosing $closing): void
    {
        $linkedPayments = array_sum(
            $this->payroll->monthlyPercentagePaidByUser($closing->owner_id, $closing->year, $closing->month)
        );

        if ($linkedPayments > 0 || $closing->dues()->where('paid_amount', '>', 0)->exists()) {
            throw new \DomainException(__('owner.month_closing.errors.payments_exist'));
        }

        $closing->delete();
    }

    public function find(int $ownerId, int $year, int $month): ?MonthClosing
    {
        return MonthClosing::where('owner_id', $ownerId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('status', 'closed')
            ->first();
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    private function participatingCrew(int $ownerId)
    {
        return User::where('owner_id', $ownerId)
            ->whereIn('role', ['crew', 'captain'])
            ->where('salary_type', 'percentage')
            ->get();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function monthRange(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();

        return [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];
    }

    /**
     * @return array<int, string>
     */
    private function warnings(int $ownerId, string $from, string $to): array
    {
        $warnings = [];

        $openTrips = Trip::where('owner_id', $ownerId)
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->whereNotIn('status', [TripStatus::Sold->value, TripStatus::Cancelled->value])
            ->count();

        if ($openTrips > 0) {
            $warnings[] = __('owner.month_closing.warnings.open_trips', ['count' => $openTrips]);
        }

        $unpaid = Sale::where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to])
            ->where('remaining_total', '>', 0)
            ->sum('remaining_total');

        if ($unpaid > 0) {
            $warnings[] = __('owner.month_closing.warnings.unpaid_sales', ['amount' => number_format((float) $unpaid, 2)]);
        }

        return $warnings;
    }
}
