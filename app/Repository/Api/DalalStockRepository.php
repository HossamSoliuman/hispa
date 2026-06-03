<?php

namespace App\Repository\Api;

use App\Http\Resources\DalalStockResource;
use App\Interfaces\CRUD;
use App\Models\DalalStock;
use App\Traits\RespondsWithHttpStatus;

class DalalStockRepository implements CRUD
{
    use RespondsWithHttpStatus;

    public function getList($request)
    {
        $user = $request->user();

        $stocks = DalalStock::with([
            'owner:id,name',
            'trip:id,number',
            'details.fish:id,scientific_name',
        ])
            ->where('dalal_id', $user->id)
            ->latest()
            ->paginate(10);

        // Extract all details
        $allDetails = $stocks->getCollection()->flatMap(function ($stock) {
            return $stock->details;
        });

        // Group by fish_id
        $groupedByFish = $allDetails->groupBy('fish_id')->map(function ($group, $fishId) {
            return [
                'fish_id' => $fishId,
                'fish_name' => optional($group->first()->fish)->name,
                'total_weight' => $group->sum('weight'),
                'total_quantity' => $group->sum('quantity'),
                'entries_count' => $group->count(),
            ];
        })->values();

        $summary = [
            'total_weight' => $allDetails->sum('weight'),
            'distinct_fish_count' => $allDetails->pluck('fish_id')->unique()->count(),

        ];

        return $this->success(trans('api.stocks_fetched'), [
            'summary' => $summary,
            'grouped_by_fish' => $groupedByFish,
            'stocks' => paginationResult(DalalStockResource::collection($stocks)),
        ], 200);
    }

    public function getDetail($id)
    {
        $user = request()->user();

        $stock = DalalStock::with([
            'owner:id,name',
            'trip:id,number',
            'details.fish:id,scientific_name',
        ])
            ->where('dalal_id', $user->id)
            ->find($id);

        if (! $stock) {
            return $this->failure(trans('api.stock_not_found'), [], 404);
        }

        return $this->success(trans('api.stock_details_fetched'), new DalalStockResource($stock), 200);
    }

    public function saveData($request)
    {
        // TODO: Implement saveData() method.
    }

    public function updateData($request, $id)
    {
        // TODO: Implement updateData() method.
    }

    public function deleteData($id)
    {
        // TODO: Implement deleteData() method.
    }
}
