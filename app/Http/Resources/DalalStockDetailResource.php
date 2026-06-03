<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DalalStockDetailResource extends JsonResource
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
            'fish_id' => $this->fish_id,
            // 'fish_name' => $this->fish_name,
            'fish_name' => $this->fish->name ?? __('messages.unknown'),
            'weight' => $this->weight,
            //            'quantity' => $this->quantity,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];

        return $data;
    }
}
