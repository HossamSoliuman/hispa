<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FishStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = [
            'id' => $this->id,
            //            'trip_id' => $this->trip_id,
            //            'trip_name' => optional($this->trip)->name,
            'fish_id' => $this->fish_id,
            // 'fish_name' => $this->fish_name,
            'fish_name' => $this->fish->name ?? __('messages.unknown'),
            //            'quantity_total' => $this->quantity,
            'weight_total' => $this->weight,
            //            'quantity_captain' => $this->quantity_captain,
            'weight_captain' => $this->weight_captain,
            //            'quantity_counter' => $this->quantity_counter,
            'weight_counter' => $this->weight_counter,
            'added_by' => optional($this->addedBy)?->name,
            'notes' => $this->notes,
            'notes_by_counter' => $this->notes_by_counter,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];

        return $data;
    }
}
