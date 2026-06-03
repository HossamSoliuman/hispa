<?php

namespace App\Services\Admin;

use App\Models\FishQuantityStock;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OwnerStockService
{
    /**
     * Base query for FishQuantityStock scoped by owner (via trip or boat).
     */
    public function baseQuery(?int $ownerId = null): Builder
    {
        $query = FishQuantityStock::query()
            ->with(['fish', 'trip', 'boat']);

        if ($ownerId !== null) {
            $query->where(function (Builder $q) use ($ownerId) {
                $q->whereHas('trip', fn (Builder $t) => $t->where('owner_id', $ownerId))
                    ->orWhereHas('boat', fn (Builder $b) => $b->where('owner_id', $ownerId));
            });
        }

        return $query;
    }

    /**
     * Apply common filters from request (from, to, boat_id, trip_id, fish_id).
     */
    public function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('from')) {
            $query->whereRaw('DATE(created_at) >= ?', [$request->input('from')]);
        }
        if ($request->filled('to')) {
            $query->whereRaw('DATE(created_at) <= ?', [$request->input('to')]);
        }
        if ($request->filled('boat_id')) {
            $query->where('boat_id', $request->input('boat_id'));
        }
        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->input('trip_id'));
        }
        if ($request->filled('fish_id')) {
            $query->where('fish_id', $request->input('fish_id'));
        }

        return $query;
    }

    /**
     * Get owners that have at least one FishQuantityStock (for filter dropdown).
     */
    public function getOwnersWithStock(): \Illuminate\Database\Eloquent\Collection
    {
        $ownerIdsFromTrips = FishQuantityStock::query()
            ->join('trips', 'fish_quantity_stocks.trip_id', '=', 'trips.id')
            ->distinct()
            ->pluck('trips.owner_id');
        $ownerIdsFromBoats = FishQuantityStock::query()
            ->join('boats', 'fish_quantity_stocks.boat_id', '=', 'boats.id')
            ->distinct()
            ->pluck('boats.owner_id');

        $ownerIds = $ownerIdsFromTrips->merge($ownerIdsFromBoats)->filter()->unique()->values();

        return User::query()
            ->ownerRole()
            ->whereIn('id', $ownerIds)
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
