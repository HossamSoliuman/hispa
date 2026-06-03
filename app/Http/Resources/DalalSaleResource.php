<?php

namespace App\Http\Resources;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DalalSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $details = DalalSaleDetailsResource::collection($this->details);

        $data = [

            'id' => $this->id,
            'number' => $this->number,
            'customer' => $this->customer->name ?? $this->customer_name,
            'status' => $this->status,
            'status_text' => Sale::statusText($this->status),
            'owner_id' => $this->owner_id ?? null,
            'total_price' => $this->total_price,
            'commission_rate' => $this->commission_rate,
            'commission_amount' => $this->commission_amount,
            'labor_rate' => $this->labor_rate,
            'labor_amount' => $this->labor_amount,
            'net_owner_amount' => $this->net_owner_amount,
            'remaining_total' => $this->remaining_total,
            'created_at' => $this->created_at->toDateTimeString(),
            'sale_datetime' => $this->sale_datetime,
            'details_count' => $details->count(),
            'total_weight' => $details->sum(fn ($d) => $d->weight),
            //            'total_quantity' => $details->sum(fn($d) => $d->quantity),
            'details' => $details,

        ];

        return $data;
    }
}
