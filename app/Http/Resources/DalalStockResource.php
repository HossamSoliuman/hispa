<?php

namespace App\Http\Resources;

use App\Models\DalalStock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DalalStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $details = DalalStockDetailResource::collection($this->whenLoaded('details'));

        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'trip_number' => optional($this->trip)->number,

            'owner_id' => $this->owner_id,
            'owner_name' => optional($this->owner)->name,
            'owner_logo' => optional($this->owner)->logo,

            'dalal_id' => $this->dalal_id,
            'dalal_name' => optional($this->dalal)->name,
            'dalal_logo' => optional($this->dalal)->logo,

            //            'total_weight' => $this->total_weight,

            'status' => $this->status,
            'status_text' => DalalStock::statusText($this->status),
            'distinct_fish_count' => $details->pluck('fish_id')->unique()->count(),
            'total_weight' => $details->sum('weight'),
            //            'total_quantity' => $details->sum('quantity'),
            'details' => $details,

            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
