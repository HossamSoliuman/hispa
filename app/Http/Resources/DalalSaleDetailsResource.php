<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DalalSaleDetailsResource extends JsonResource
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
            // 'fish_name' => $this->fish->scientific_name ?? 'غير معروف',
            'fish_name' => $this->fish->name ?? __('messages.unknown'),
            //            'quantity' => $this->quantity,
            'weight' => $this->weight,
            'price_per_kilo' => $this->price_per_kilo,
            'total_price' => $this->total_price,
            'dalal_stock_detail_id' => $this->dalal_stock_detail_id,

        ];

        return $data;
    }
}
