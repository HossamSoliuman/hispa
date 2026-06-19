<?php

namespace App\Service\Owner;

use App\Models\Boat;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;

/**
 * Aggregation reports for the owner panel (boat/trip profitability, production by
 * species). Money figures are gross/operational and attributable to a boat or
 * trip; the canonical owner-level waterfall (overheads, owner/crew split) lives
 * in {@see MonthlyFinancialsService} and the month-close. See
 * docs/dashboard-reports-overhaul-plan.md Part 4 (reports #2, #6, #7, #20).
 */
class MonthlyReportsService
{
    public function __construct(private MonthlyFinancialsService $financials) {}

    /**
     * Profitability per boat for the period: gross/net sales, trip expenses,
     * net and margin. Sales attributed to a boat via their trip.
     *
     * @return array<int, array{boat_id: int, boat_name: string, gross_sales: float, net_sales: float, expenses: float, net_profit: float, margin: float}>
     */
    public function boatProfitability(int $ownerId, string $from, string $to): array
    {
        $boats = Boat::where('owner_id', $ownerId)->get(['id', 'name_ar', 'name_en']);

        $salesByBoat = DB::table('sales')
            ->join('trips', 'sales.trip_id', '=', 'trips.id')
            ->where('sales.seller_type', 'owner')
            ->where('sales.seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sales.sale_datetime)'), [$from, $to])
            ->groupBy('trips.boat_id')
            ->select('trips.boat_id', DB::raw('SUM(sales.total_price) as gross'), DB::raw('SUM(sales.net_owner_amount) as net'))
            ->get()
            ->keyBy('boat_id');

        $expensesByBoat = DB::table('expenses')
            ->where('owner_id', $ownerId)
            ->whereNotNull('boat_id')
            ->whereBetween('date', [$from, $to])
            ->groupBy('boat_id')
            ->select('boat_id', DB::raw('SUM(final_price) as total'))
            ->pluck('total', 'boat_id');

        $rows = [];
        foreach ($boats as $boat) {
            $gross = (float) ($salesByBoat[$boat->id]->gross ?? 0);
            $net = (float) ($salesByBoat[$boat->id]->net ?? 0);
            $expenses = (float) ($expensesByBoat[$boat->id] ?? 0);
            $netProfit = $net - $expenses;

            $rows[] = [
                'boat_id' => $boat->id,
                'boat_name' => $boat->name ?: ('#'.$boat->id),
                'gross_sales' => round($gross, 2),
                'net_sales' => round($net, 2),
                'expenses' => round($expenses, 2),
                'net_profit' => round($netProfit, 2),
                'margin' => $net > 0 ? round(($netProfit / $net) * 100, 2) : 0.0,
            ];
        }

        usort($rows, fn ($a, $b) => $b['net_profit'] <=> $a['net_profit']);

        return $rows;
    }

    /**
     * Profitability per trip for the period. Trip expenses are allocated by
     * boat + the trip's date window (expenses carry no trip_id) — an
     * approximation noted in the report footer.
     *
     * @return array<int, array{trip_id: int, number: string, boat_name: string, captain_name: string, start_date: ?string, status_label: string, gross_sales: float, net_sales: float, expenses: float, net_profit: float, margin: float}>
     */
    public function tripProfitability(int $ownerId, string $from, string $to, ?int $boatId = null): array
    {
        $query = Trip::with(['captain', 'boat:id,name_ar,name_en'])
            ->where('owner_id', $ownerId)
            ->whereBetween(DB::raw('DATE(start_date)'), [$from, $to]);

        if ($boatId) {
            $query->where('boat_id', $boatId);
        }

        $trips = $query->orderByDesc('start_date')->get();

        $salesByTrip = DB::table('sales')
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereIn('trip_id', $trips->pluck('id'))
            ->groupBy('trip_id')
            ->select('trip_id', DB::raw('SUM(total_price) as gross'), DB::raw('SUM(net_owner_amount) as net'))
            ->get()
            ->keyBy('trip_id');

        $rows = [];
        foreach ($trips as $trip) {
            $gross = (float) ($salesByTrip[$trip->id]->gross ?? 0);
            $net = (float) ($salesByTrip[$trip->id]->net ?? 0);
            $expenses = $this->tripExpenses($ownerId, $trip);
            $netProfit = $net - $expenses;

            $rows[] = [
                'trip_id' => $trip->id,
                'number' => $trip->number,
                'boat_name' => $trip->boat?->name ?: $trip->boat_name,
                'captain_name' => $trip->captain->name ?? '',
                'start_date' => optional($trip->start_date)->format('Y-m-d'),
                'status_label' => $trip->status instanceof \BackedEnum ? $trip->status->label() : (string) $trip->status,
                'gross_sales' => round($gross, 2),
                'net_sales' => round($net, 2),
                'expenses' => round($expenses, 2),
                'net_profit' => round($netProfit, 2),
                'margin' => $net > 0 ? round(($netProfit / $net) * 100, 2) : 0.0,
            ];
        }

        return $rows;
    }

    /**
     * Expenses allocated to a single trip: owner expenses on the trip's boat
     * whose date falls within the trip's window.
     */
    private function tripExpenses(int $ownerId, Trip $trip): float
    {
        if (! $trip->boat_id || ! $trip->start_date) {
            return 0.0;
        }

        $end = $trip->end_date ?? $trip->actual_end_datetime ?? $trip->start_date;

        return (float) DB::table('expenses')
            ->where('owner_id', $ownerId)
            ->where('boat_id', $trip->boat_id)
            ->whereBetween('date', [
                $trip->start_date->format('Y-m-d'),
                \Carbon\Carbon::parse($end)->format('Y-m-d'),
            ])
            ->sum('final_price');
    }

    /**
     * Owner expenses for the period grouped by category (with its type),
     * classified via categories.type only. Report #5.
     *
     * @return array<int, array{category_id: int, category: string, type: ?string, count: int, amount: float}>
     */
    public function expensesByCategory(int $ownerId, string $from, string $to, ?int $boatId = null): array
    {
        $query = DB::table('expenses')
            ->leftJoin('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.owner_id', $ownerId)
            ->whereBetween('expenses.date', [$from, $to]);

        if ($boatId) {
            $query->where('expenses.boat_id', $boatId);
        }

        $isEnglish = app()->getLocale() === 'en';

        $rows = $query->groupBy('expenses.category_id', 'categories.name_ar', 'categories.name_en', 'categories.type')
            ->select(
                'expenses.category_id',
                'categories.name_ar',
                'categories.name_en',
                'categories.type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(expenses.final_price) as amount')
            )
            ->get()
            ->map(fn ($row): array => [
                'category_id' => (int) $row->category_id,
                'category' => ($isEnglish ? ($row->name_en ?: $row->name_ar) : ($row->name_ar ?: $row->name_en)) ?: '—',
                'type' => $row->type,
                'count' => (int) $row->count,
                'amount' => round((float) $row->amount, 2),
            ])
            ->sortByDesc('amount')
            ->values()
            ->all();

        return $rows;
    }

    /**
     * Caught vs sold weight and value per fish species for the period.
     *
     * @return array<int, array{fish_id: int, fish_name: string, caught_weight: float, caught_value: float, sold_weight: float, sold_value: float}>
     */
    public function productionBySpecies(int $ownerId, string $from, string $to): array
    {
        $tripIds = Trip::where('owner_id', $ownerId)->pluck('id');

        $caught = DB::table('catch_details')
            ->join('catch_models', 'catch_details.catch_id', '=', 'catch_models.id')
            ->whereIn('catch_models.trip_id', $tripIds)
            ->whereBetween(DB::raw('DATE(catch_models.catch_date)'), [$from, $to])
            ->groupBy('catch_details.fish_id', 'catch_details.fish_name')
            ->select(
                'catch_details.fish_id',
                'catch_details.fish_name',
                DB::raw('SUM(catch_details.weight) as weight'),
                DB::raw('SUM(catch_details.total_price) as value')
            )
            ->get();

        $saleIds = DB::table('sales')
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to])
            ->pluck('id');

        $sold = DB::table('sale_details')
            ->whereIn('sale_id', $saleIds)
            ->groupBy('fish_id', 'fish_name')
            ->select(
                'fish_id',
                'fish_name',
                DB::raw('SUM(weight) as weight'),
                DB::raw('SUM(total_price) as value')
            )
            ->get();

        $species = [];
        foreach ($caught as $row) {
            $species[$row->fish_id] = [
                'fish_id' => (int) $row->fish_id,
                'fish_name' => $row->fish_name,
                'caught_weight' => round((float) $row->weight, 2),
                'caught_value' => round((float) $row->value, 2),
                'sold_weight' => 0.0,
                'sold_value' => 0.0,
            ];
        }
        foreach ($sold as $row) {
            if (! isset($species[$row->fish_id])) {
                $species[$row->fish_id] = [
                    'fish_id' => (int) $row->fish_id,
                    'fish_name' => $row->fish_name,
                    'caught_weight' => 0.0,
                    'caught_value' => 0.0,
                    'sold_weight' => 0.0,
                    'sold_value' => 0.0,
                ];
            }
            $species[$row->fish_id]['sold_weight'] = round((float) $row->weight, 2);
            $species[$row->fish_id]['sold_value'] = round((float) $row->value, 2);
        }

        $rows = array_values($species);
        usort($rows, fn ($a, $b) => $b['sold_value'] <=> $a['sold_value']);

        return $rows;
    }
}
