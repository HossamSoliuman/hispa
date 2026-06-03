<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailsResource extends JsonResource
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
            'sale_id' => $this->sale_id,
            'fish_id' => $this->fish_id,
            // 'fish_name' => $this->fish_name,
            'fish_name' => $this->fish->name ?? __('messages.unknown'),
            //            'quantity' => $this->quantity,
            'weight' => $this->weight,
            'price_per_kilo' => $this->price_per_kilo,
            'total_price' => $this->total_price,

        ];

        return $data;
    }
}
