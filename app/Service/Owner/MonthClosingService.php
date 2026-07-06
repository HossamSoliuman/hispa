<?php

namespace App\Service\Owner;

use App\Enums\TripStatus;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\CrewAdvance;
use App\Models\Expense;
use App\Models\MonthClosing;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
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
        private AssetDepreciationService $assetDepreciation,
    ) {}

    /**
     * Build (but do not persist) the distribution for a month.
     *
     * @return array{
     *     year: int, month: int, boat_id: int|null, from: string, to: string,
     *     financials: array<string, mixed>,
     *     asset_depreciation: array{total: float, assets: array<int, array<string, mixed>>},
     *     depreciation_brought_forward: float, depreciation_deferred: float,
     *     dues: array<int, array<string, mixed>>,
     *     total_shares: float, share_value: float,
     *     existing: \App\Models\MonthClosing|null,
     *     warnings: array<int, string>
     * }
     */
    public function preview(int $ownerId, int $year, int $month, ?int $boatId = null): array
    {
        [$from, $to] = $this->monthRange($year, $month);

        $depreciation = $this->assetDepreciation->forMonth($ownerId, $year, $month, $boatId);
        $broughtForward = $this->financials->broughtForwardDepreciation($ownerId, $year, $month, $boatId);

        $financials = $this->financials->compute($ownerId, $from, $to, $boatId, $depreciation['total'], $broughtForward, true);

        $distribution = $this->financials->crewDistribution($ownerId, $financials['crew_share'], $boatId);

        $dues = $distribution['members']->map(function (array $member) use ($distribution, $ownerId, $from, $to) {
            $due = (float) $member['due'];
            $advances = (float) CrewAdvance::where('user_id', $member['user_id'])
                ->where('owner_id', $ownerId)
                ->whereBetween('date', [$from, $to])
                ->sum('amount');

            return [
                'user_id' => $member['user_id'],
                'member_name' => $member['name'],
                'role' => $member['role'],
                'shares' => (float) $member['shares'],
                'custom_share_percent' => $member['custom_percent'],
                'share_value' => $member['custom_percent'] !== null ? 0.0 : $distribution['share_value'],
                'due_amount' => round($due, 2),
                'advances' => round($advances, 2),
                'paid_amount' => 0.0,
                'remaining' => round($due - $advances, 2),
            ];
        })->all();

        return [
            'year' => $year,
            'month' => $month,
            'boat_id' => $boatId,
            'from' => $from,
            'to' => $to,
            'financials' => $financials,
            'asset_depreciation' => $depreciation,
            'depreciation_brought_forward' => $financials['depreciation_brought_forward'],
            'depreciation_deferred' => $financials['depreciation_deferred'],
            'dues' => $dues,
            'total_shares' => $distribution['total_shares'],
            'share_value' => $distribution['share_value'],
            'existing' => $this->find($ownerId, $year, $month, $boatId),
            'warnings' => $this->warnings($ownerId, $from, $to, $boatId),
        ];
    }

    /**
     * Freeze the month's distribution into an immutable snapshot.
     *
     * @throws \DomainException when the month is already closed.
     */
    public function close(int $ownerId, int $year, int $month, ?int $closedBy = null, ?int $boatId = null): MonthClosing
    {
        if ($this->find($ownerId, $year, $month, $boatId)) {
            throw new \DomainException(__('owner.month_closing.errors.already_closed'));
        }

        $preview = $this->preview($ownerId, $year, $month, $boatId);
        $f = $preview['financials'];

        return DB::transaction(function () use ($ownerId, $year, $month, $boatId, $closedBy, $preview, $f) {
            $closing = MonthClosing::create([
                'owner_id' => $ownerId,
                'year' => $year,
                'month' => $month,
                'boat_id' => $boatId,
                'status' => 'closed',
                'gross_sales' => $f['gross_sales'],
                'net_sales' => $f['net_sales'],
                'net_owner_revenue' => $f['net_owner_revenue'],
                'trip_expenses' => $f['trip_expenses'],
                'general_expenses' => $f['general_expenses'],
                'depreciation' => $f['depreciation'],
                'asset_depreciation_breakdown' => $preview['asset_depreciation']['assets'],
                'depreciation_brought_forward' => $f['depreciation_brought_forward'],
                'depreciation_deferred' => $f['depreciation_deferred'],
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
                    'custom_share_percent' => $due['custom_share_percent'],
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

    /**
     * Detailed sale & expense records for a closed month, used to render the
     * full line-by-line breakdown beneath the report summary.
     *
     * @return array{
     *     sales: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sale>,
     *     expenses: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense>
     * }
     */
    public function details(MonthClosing $closing): array
    {
        [$from, $to] = $this->monthRange($closing->year, $closing->month);

        return $this->financials->details($closing->owner_id, $from, $to, $closing->boat_id);
    }

    /**
     * Annual roll-up of an owner's monthly closings for a calendar year.
     *
     * Lists all twelve months; only months that have been closed carry a frozen
     * {@see MonthClosing} snapshot, the rest are null. The year totals sum the
     * closed months only. Boats follow {@see find()}: a null boat means the
     * fleet-wide closings, otherwise a single boat's closings.
     *
     * @return array{
     *     year: int, boat_id: int|null,
     *     months: array<int, \App\Models\MonthClosing|null>,
     *     totals: array{gross_sales: float, net_owner_revenue: float, trip_expenses: float, general_expenses: float, depreciation: float, depreciation_brought_forward: float, depreciation_deferred: float, total_expenses: float, net_profit: float, owner_share: float, crew_share: float},
     *     closed_count: int
     * }
     */
    public function annualSummary(int $ownerId, int $year, ?int $boatId = null): array
    {
        $closings = MonthClosing::where('owner_id', $ownerId)
            ->where('year', $year)
            ->where('status', 'closed')
            ->when($boatId !== null, fn ($query) => $query->where('boat_id', $boatId))
            ->when($boatId === null, fn ($query) => $query->whereNull('boat_id'))
            ->get()
            ->keyBy('month');

        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = $closings->get($month);
        }

        $fields = [
            'gross_sales', 'net_owner_revenue', 'trip_expenses', 'general_expenses',
            'depreciation', 'depreciation_brought_forward', 'depreciation_deferred',
            'total_expenses', 'net_profit', 'owner_share', 'crew_share',
        ];

        $totals = [];
        foreach ($fields as $field) {
            $totals[$field] = round((float) $closings->sum(fn (MonthClosing $closing) => (float) $closing->{$field}), 2);
        }

        return [
            'year' => $year,
            'boat_id' => $boatId,
            'months' => $months,
            'totals' => $totals,
            'closed_count' => $closings->count(),
        ];
    }

    /**
     * Distinct calendar years that have at least one closed month for the owner
     * (optionally scoped to a boat), most recent first, each with its rolled-up
     * annual summary. Drives the "closed years" list on the annual report page.
     *
     * @return array<int, array{year: int, summary: array<string, mixed>}>
     */
    public function closedYears(int $ownerId, ?int $boatId = null): array
    {
        $years = MonthClosing::where('owner_id', $ownerId)
            ->where('status', 'closed')
            ->when($boatId !== null, fn ($query) => $query->where('boat_id', $boatId))
            ->when($boatId === null, fn ($query) => $query->whereNull('boat_id'))
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return $years->map(fn (int $year): array => [
            'year' => $year,
            'summary' => $this->annualSummary($ownerId, $year, $boatId),
        ])->all();
    }

    /**
     * Rich year-level analysis for the annual closing report: trip activity,
     * crew payroll totals, expenses grouped by category and by type, a sales
     * composition (top species, top customers, per-boat split), catch production,
     * per-member crew earnings and a set of derived insights (best/worst month,
     * margins, trend, verdict, recommendations) so the printed report reads like
     * an analyst's year-in-review. Money figures reconcile with the summary: the
     * closing figures are read from the frozen snapshots and every live breakdown
     * (sales, catches, expenses) is restricted to the year's closed months.
     *
     * @return array{
     *     trips: array{total: int, sold: int, cancelled: int, active: int},
     *     payroll: array{crew_pool: float, owner_share: float, advances: float, paid: float, remaining: float, crew_count: int},
     *     expenses_by_category: array<int, array{name: string, total: float}>,
     *     expenses_by_type: array<int, array{type: string, label: string, total: float}>,
     *     sales: array{totals: array{gross: float, net_owner: float, commission_labor: float, invoices: int, avg_invoice: float}, by_fish: array<int, array{name: string, unit_name: string, quantity: float, weight: float, total: float}>, top_customers: array<int, array{name: string, invoices: int, total: float}>, by_boat: array<int, array{name: string, invoices: int, total: float}>},
     *     catch: array{trips_with_catch: int, total_weight: float, total_amount: float, by_species: array<int, array{name: string, unit_name: string, weight: float, total: float}>},
     *     crew_members: array<int, array{name: string, role: string, months: int, earned: float, advances: float, paid: float, remaining: float}>,
     *     analysis: array<string, mixed>
     * }
     */
    public function annualAnalysis(int $ownerId, int $year, ?int $boatId = null): array
    {
        $closings = MonthClosing::where('owner_id', $ownerId)
            ->where('year', $year)
            ->where('status', 'closed')
            ->when($boatId !== null, fn ($query) => $query->where('boat_id', $boatId))
            ->when($boatId === null, fn ($query) => $query->whereNull('boat_id'))
            ->with('dues')
            ->get();

        $closedMonths = $closings->pluck('month')->map(fn ($month): int => (int) $month)->sort()->values()->all();

        $payroll = [
            'crew_pool' => round((float) $closings->sum('crew_share'), 2),
            'owner_share' => round((float) $closings->sum('owner_share'), 2),
            'advances' => round((float) $closings->sum(fn (MonthClosing $c) => (float) $c->dues->sum('advances')), 2),
            'paid' => round((float) $closings->sum(fn (MonthClosing $c) => (float) $c->dues->sum('paid_amount')), 2),
            'remaining' => round((float) $closings->sum(fn (MonthClosing $c) => (float) $c->dues->sum('remaining')), 2),
            'crew_count' => $closings->flatMap->dues->pluck('user_id')->unique()->count(),
        ];

        $tripsQuery = Trip::where('owner_id', $ownerId)
            ->whereYear('created_at', $year)
            ->when($boatId !== null, fn ($query) => $query->where('boat_id', $boatId));

        $tripsTotal = (clone $tripsQuery)->count();
        $tripsSold = (clone $tripsQuery)->where('status', TripStatus::Sold->value)->count();
        $tripsCancelled = (clone $tripsQuery)->where('status', TripStatus::Cancelled->value)->count();

        $trips = [
            'total' => $tripsTotal,
            'sold' => $tripsSold,
            'cancelled' => $tripsCancelled,
            'active' => max($tripsTotal - $tripsSold - $tripsCancelled, 0),
        ];

        [$expensesByCategory, $expensesByType] = $this->expenseBreakdowns($ownerId, $year, $closedMonths, $boatId);

        return [
            'trips' => $trips,
            'payroll' => $payroll,
            'expenses_by_category' => $expensesByCategory,
            'expenses_by_type' => $expensesByType,
            'sales' => $this->salesBreakdown($ownerId, $year, $closedMonths, $boatId),
            'catch' => $this->catchBreakdown($ownerId, $year, $closedMonths, $boatId),
            'crew_members' => $this->crewMembersAnnual($closings),
            'analysis' => $this->buildInsights($closings, $expensesByCategory),
        ];
    }

    /**
     * Expenses grouped both by category and by category type for the year's
     * closed months. Both share a single query so the two views always reconcile.
     *
     * @param  array<int, int>  $closedMonths
     * @return array{0: array<int, array{name: string, total: float}>, 1: array<int, array{type: string, label: string, total: float}>}
     */
    private function expenseBreakdowns(int $ownerId, int $year, array $closedMonths, ?int $boatId): array
    {
        if ($closedMonths === []) {
            return [[], []];
        }

        $query = Expense::query()
            ->where('owner_id', $ownerId)
            ->when($boatId !== null, fn ($query) => $query->where('boat_id', $boatId));
        $this->constrainToMonths($query, 'date', $year, $closedMonths);

        $rows = $query
            ->selectRaw('category_id, SUM(final_price) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $byCategory = $rows->map(fn ($row): array => [
            'name' => $row->category?->name ?? '—',
            'total' => round((float) $row->total, 2),
        ])->sortByDesc('total')->values()->all();

        $typeLabels = __('owner.annual_summary.expense_types');

        $byType = $rows->groupBy(fn ($row): string => (string) ($row->category?->type ?: 'other'))
            ->map(fn ($group, $type): array => [
                'type' => (string) $type,
                'label' => $typeLabels[$type] ?? ($typeLabels['other'] ?? $type),
                'total' => round((float) $group->sum('total'), 2),
            ])
            ->sortByDesc('total')
            ->values()
            ->all();

        return [$byCategory, $byType];
    }

    /**
     * Sales composition for the year's closed months: headline totals, the top
     * species by revenue, the top customers, and (fleet-wide only) the per-boat
     * split. Scoped exactly like the owner's monthly sales so it reconciles.
     *
     * @param  array<int, int>  $closedMonths
     * @return array{totals: array{gross: float, net_owner: float, commission_labor: float, invoices: int, avg_invoice: float}, by_fish: array<int, array{name: string, unit_name: string, quantity: float, weight: float, total: float}>, top_customers: array<int, array{name: string, invoices: int, total: float}>, by_boat: array<int, array{name: string, invoices: int, total: float}>}
     */
    private function salesBreakdown(int $ownerId, int $year, array $closedMonths, ?int $boatId): array
    {
        $empty = [
            'totals' => ['gross' => 0.0, 'net_owner' => 0.0, 'commission_labor' => 0.0, 'invoices' => 0, 'avg_invoice' => 0.0],
            'by_fish' => [],
            'top_customers' => [],
            'by_boat' => [],
        ];

        if ($closedMonths === []) {
            return $empty;
        }

        $query = Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->when($boatId !== null, fn ($query) => $query->whereIn('trip_id', Trip::where('boat_id', $boatId)->pluck('id')));
        $this->constrainToMonths($query, 'sale_datetime', $year, $closedMonths);

        $sales = $query
            ->with(['customer', 'trip.boat'])
            ->get(['id', 'customer_id', 'customer_name', 'trip_id', 'total_price', 'net_owner_amount', 'commission_amount', 'labor_amount']);

        $invoices = $sales->count();
        $gross = round((float) $sales->sum('total_price'), 2);

        $totals = [
            'gross' => $gross,
            'net_owner' => round((float) $sales->sum('net_owner_amount'), 2),
            'commission_labor' => round((float) $sales->sum(fn (Sale $sale): float => (float) $sale->commission_amount + (float) $sale->labor_amount), 2),
            'invoices' => $invoices,
            'avg_invoice' => $invoices > 0 ? round($gross / $invoices, 2) : 0.0,
        ];

        $byFish = SaleDetail::query()
            ->leftJoin('units', 'sale_details.unit_id', '=', 'units.id')
            ->whereIn('sale_id', $sales->pluck('id'))
            ->selectRaw('sale_details.fish_id, MAX(sale_details.fish_name) as fish_name, sale_details.unit_id, MAX(units.name_ar) as unit_name_ar, MAX(units.name_en) as unit_name_en, SUM(sale_details.quantity) as quantity, SUM(sale_details.weight) as weight, SUM(sale_details.total_price) as total')
            ->groupBy('sale_details.fish_id', 'sale_details.unit_id')
            ->get()
            ->map(fn ($row): array => [
                'name' => $row->fish_name ?: '—',
                'unit_name' => $this->localizedUnitName($row->unit_name_ar, $row->unit_name_en),
                'quantity' => round((float) $row->quantity, 2),
                'weight' => round((float) $row->weight, 2),
                'total' => round((float) $row->total, 2),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->all();

        $topCustomers = $sales->groupBy(fn (Sale $sale): string => $sale->customer_id ? 'id:'.$sale->customer_id : 'name:'.trim((string) $sale->customer_name))
            ->map(fn ($group): array => [
                'name' => ($group->first()->customer?->name ?: $group->first()->customer_name) ?: '—',
                'invoices' => $group->count(),
                'total' => round((float) $group->sum('total_price'), 2),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->all();

        $byBoat = [];
        if ($boatId === null) {
            $byBoat = $sales->groupBy(fn (Sale $sale): int => (int) ($sale->trip?->boat?->id ?: 0))
                ->map(fn ($group): array => [
                    'name' => $group->first()->trip?->boat?->name ?: __('owner.annual_summary.unassigned'),
                    'invoices' => $group->count(),
                    'total' => round((float) $group->sum('total_price'), 2),
                ])
                ->sortByDesc('total')
                ->values()
                ->all();
        }

        return [
            'totals' => $totals,
            'by_fish' => $byFish,
            'top_customers' => $topCustomers,
            'by_boat' => $byBoat,
        ];
    }

    /**
     * Catch production for the year's closed months: how many trips landed a
     * catch, the total landed weight and value, and the top species by value.
     *
     * @param  array<int, int>  $closedMonths
     * @return array{trips_with_catch: int, total_weight: float, total_amount: float, by_species: array<int, array{name: string, unit_name: string, weight: float, total: float}>}
     */
    private function catchBreakdown(int $ownerId, int $year, array $closedMonths, ?int $boatId): array
    {
        if ($closedMonths === []) {
            return ['trips_with_catch' => 0, 'total_weight' => 0.0, 'total_amount' => 0.0, 'by_species' => []];
        }

        $catchQuery = CatchModel::query()
            ->where('owner_id', $ownerId)
            ->when($boatId !== null, fn ($query) => $query->whereIn('trip_id', Trip::where('boat_id', $boatId)->pluck('id')));
        $this->constrainToMonths($catchQuery, 'catch_date', $year, $closedMonths);

        $catchIds = (clone $catchQuery)->pluck('id');

        $bySpecies = CatchDetail::query()
            ->leftJoin('units', 'catch_details.unit_id', '=', 'units.id')
            ->whereIn('catch_id', $catchIds)
            ->selectRaw('catch_details.fish_id, MAX(catch_details.fish_name) as fish_name, catch_details.unit_id, MAX(units.name_ar) as unit_name_ar, MAX(units.name_en) as unit_name_en, SUM(catch_details.weight) as weight, SUM(catch_details.total_price) as total')
            ->groupBy('catch_details.fish_id', 'catch_details.unit_id')
            ->get()
            ->map(fn ($row): array => [
                'name' => $row->fish_name ?: '—',
                'unit_name' => $this->localizedUnitName($row->unit_name_ar, $row->unit_name_en),
                'weight' => round((float) $row->weight, 2),
                'total' => round((float) $row->total, 2),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->all();

        return [
            'trips_with_catch' => $catchIds->count(),
            'total_weight' => round((float) (clone $catchQuery)->sum('total_weight'), 2),
            'total_amount' => round((float) (clone $catchQuery)->sum('total_amount'), 2),
            'by_species' => $bySpecies,
        ];
    }

    /**
     * Locale-aware unit label from the raw Arabic/English name columns, so a
     * fish/species line can print its measured amount together with its unit
     * (كجم/شكه/قلم/…) rather than assuming kilograms.
     */
    private function localizedUnitName(?string $nameAr, ?string $nameEn): string
    {
        return app()->getLocale() === 'en'
            ? ($nameEn ?: ($nameAr ?? ''))
            : ($nameAr ?: ($nameEn ?? ''));
    }

    /**
     * Per-member crew earnings rolled up across the year's closed months: what
     * each member earned, took as advances, was paid, and is still owed.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\MonthClosing>  $closings
     * @return array<int, array{name: string, role: string, months: int, earned: float, advances: float, paid: float, remaining: float}>
     */
    private function crewMembersAnnual($closings): array
    {
        return $closings->flatMap->dues
            ->groupBy('user_id')
            ->map(function ($dues): array {
                $first = $dues->first();

                return [
                    'name' => $first->member_name,
                    'role' => $first->role,
                    'months' => $dues->count(),
                    'earned' => round((float) $dues->sum('due_amount'), 2),
                    'advances' => round((float) $dues->sum('advances'), 2),
                    'paid' => round((float) $dues->sum('paid_amount'), 2),
                    'remaining' => round((float) $dues->sum('remaining'), 2),
                ];
            })
            ->sortByDesc('earned')
            ->values()
            ->all();
    }

    /**
     * Derive the analyst narrative (best/worst month, margins, trend, verdict,
     * insights, recommendations) from the year's frozen closings.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\MonthClosing>  $closings
     * @param  array<int, array{name: string, total: float}>  $expensesByCategory
     * @return array<string, mixed>
     */
    private function buildInsights($closings, array $expensesByCategory): array
    {
        $monthNames = __('owner.annual_summary.months');
        $closedCount = $closings->count();

        $grossSales = round((float) $closings->sum('gross_sales'), 2);
        $totalExpenses = round((float) $closings->sum('total_expenses'), 2);
        $netProfit = round((float) $closings->sum('net_profit'), 2);

        $best = $closings->sortByDesc('net_profit')->first();
        $worst = $closings->sortBy('net_profit')->first();
        $avgNet = $closedCount > 0 ? round($netProfit / $closedCount, 2) : 0.0;
        $margin = $grossSales > 0 ? round($netProfit / $grossSales * 100, 1) : 0.0;
        $expenseRatio = $grossSales > 0 ? round($totalExpenses / $grossSales * 100, 1) : 0.0;
        $profitableMonths = $closings->where('net_profit', '>', 0)->count();
        $lossMonths = $closings->where('net_profit', '<', 0)->count();
        $isProfitable = $netProfit > 0;

        $ordered = $closings->sortBy('month')->values();
        $trend = 'flat';
        if ($closedCount >= 2) {
            $half = max(intdiv($closedCount, 2), 1);
            $firstAvg = (float) $ordered->take($half)->avg('net_profit');
            $secondAvg = (float) $ordered->slice($closedCount - $half)->avg('net_profit');
            $diff = $secondAvg - $firstAvg;
            $threshold = abs($avgNet) * 0.1;
            $trend = $diff > $threshold ? 'up' : ($diff < -$threshold ? 'down' : 'flat');
        }

        $t = fn (string $key, array $params = []): string => __('owner.annual_summary.analysis.'.$key, $params);

        $insights = [];
        $insights[] = $isProfitable
            ? $t('insight_profitable', ['net' => number_format($netProfit, 2), 'margin' => $margin])
            : $t('insight_loss', ['net' => number_format($netProfit, 2)]);

        if ($best) {
            $insights[] = $t('insight_best', ['month' => $monthNames[$best->month], 'net' => number_format((float) $best->net_profit, 2)]);
        }
        if ($worst && $best && $worst->id !== $best->id) {
            $insights[] = $t('insight_worst', ['month' => $monthNames[$worst->month], 'net' => number_format((float) $worst->net_profit, 2)]);
        }
        $insights[] = $t('insight_avg', ['avg' => number_format($avgNet, 2), 'count' => $closedCount]);
        $insights[] = $t('insight_expense_ratio', ['ratio' => $expenseRatio]);
        $insights[] = $t('insight_trend_'.$trend);
        $insights[] = $t('insight_profitable_months', ['profit' => $profitableMonths, 'loss' => $lossMonths, 'total' => $closedCount]);

        $recommendations = [];
        if (! $isProfitable) {
            $recommendations[] = $t('rec_loss');
        }
        if ($expenseRatio > 70) {
            $recommendations[] = $t('rec_high_expenses', ['ratio' => $expenseRatio]);
        }
        if ($margin > 0 && $margin < 15) {
            $recommendations[] = $t('rec_thin_margin', ['margin' => $margin]);
        }
        if ($trend === 'down') {
            $recommendations[] = $t('rec_declining');
        }
        if ($lossMonths > 0) {
            $recommendations[] = $t('rec_loss_months', ['count' => $lossMonths]);
        }
        if (! empty($expensesByCategory)) {
            $recommendations[] = $t('rec_top_expense', ['name' => $expensesByCategory[0]['name'], 'amount' => number_format($expensesByCategory[0]['total'], 2)]);
        }
        if ($recommendations === []) {
            $recommendations[] = $t('rec_healthy');
        }

        return [
            'gross_sales' => $grossSales,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'closed_count' => $closedCount,
            'best' => $best ? ['month' => $best->month, 'net' => round((float) $best->net_profit, 2)] : null,
            'worst' => $worst ? ['month' => $worst->month, 'net' => round((float) $worst->net_profit, 2)] : null,
            'avg_net' => $avgNet,
            'margin' => $margin,
            'expense_ratio' => $expenseRatio,
            'profitable_months' => $profitableMonths,
            'loss_months' => $lossMonths,
            'is_profitable' => $isProfitable,
            'trend' => $trend,
            'insights' => $insights,
            'recommendations' => $recommendations,
        ];
    }

    public function find(int $ownerId, int $year, int $month, ?int $boatId = null): ?MonthClosing
    {
        return MonthClosing::where('owner_id', $ownerId)
            ->where('year', $year)
            ->where('month', $month)
            ->when($boatId !== null, fn ($query) => $query->where('boat_id', $boatId))
            ->when($boatId === null, fn ($query) => $query->whereNull('boat_id'))
            ->where('status', 'closed')
            ->first();
    }

    /**
     * Constrain a query's date column to a given set of months of a year.
     *
     * Portable across MySQL (production) and SQLite (test DB, which has no
     * MONTH() function): one inclusive per-month date range, OR-ed together, so
     * non-contiguous closed months are matched exactly.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $query
     * @param  array<int, int>  $months
     */
    private function constrainToMonths($query, string $column, int $year, array $months): void
    {
        $ranges = [];
        foreach ($months as $month) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $ranges[] = [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];
        }

        $query->where(function ($inner) use ($column, $ranges): void {
            foreach ($ranges as [$from, $to]) {
                $inner->orWhereBetween(DB::raw('DATE('.$column.')'), [$from, $to]);
            }
        });
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
    private function warnings(int $ownerId, string $from, string $to, ?int $boatId = null): array
    {
        $warnings = [];

        $openTrips = Trip::where('owner_id', $ownerId)
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->whereNotIn('status', [TripStatus::Sold->value, TripStatus::Cancelled->value])
            ->when($boatId, fn ($query) => $query->where('boat_id', $boatId))
            ->count();

        if ($openTrips > 0) {
            $warnings[] = __('owner.month_closing.warnings.open_trips', ['count' => $openTrips]);
        }

        $unpaid = Sale::where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to])
            ->when($boatId, fn ($query) => $query->whereIn('trip_id', Trip::where('boat_id', $boatId)->pluck('id')))
            ->where('remaining_total', '>', 0)
            ->sum('remaining_total');

        if ($unpaid > 0) {
            $warnings[] = __('owner.month_closing.warnings.unpaid_sales', ['amount' => number_format((float) $unpaid, 2)]);
        }

        return $warnings;
    }
}
