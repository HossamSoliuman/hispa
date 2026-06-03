<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FishPriceDefaultResource extends JsonResource
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
            'trip_id' => $this->trip_id,
            'trip_name' => optional($this->trip)->name,
            'fish_id' => $this->fish_id,
            // 'fish_name' => optional($this->fish)->scientific_name,
            'fish_name' => optional($this->fish)->name ?? __('messages.unknown'),
            'price_per_kilo' => $this->price_per_kilo,

        ];

        return $data;
    }
}
