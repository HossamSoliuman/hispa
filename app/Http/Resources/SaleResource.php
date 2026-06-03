<?php

namespace App\Http\Resources;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'number' => $this->number,
            'status' => $this->status,
            'status_text' => Sale::statusText($this->status),
            'customer' => $this->customer->name ?? $this->customer_name,
            'notes' => $this->notes,
            'owner_id' => $this->seller_type === 'owner' ? $this->seller_id : null,
            'dalal_id' => $this->seller_type === 'dalal' ? $this->seller_id : null,
            'trip_id' => $this->trip_id,
            'total_price' => $this->total_price,
            'commission_rate' => $this->commission_rate,
            'commission_amount' => $this->commission_amount,
            'labor_rate' => $this->labor_rate,
            'labor_amount' => $this->labor_amount,
            'net_owner_amount' => $this->net_owner_amount,
            'remaining_total' => $this->remaining_total,
            'sale_datetime' => $this->sale_datetime,
            'details' => SaleDetailsResource::collection($this->whenLoaded('details')),

        ];

        return $data;
    }
}
