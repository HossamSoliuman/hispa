<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TripResource extends JsonResource
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
            'name' => $this->name,
            'number' => $this->number,
            'license_number' => $this->license_number,
            'status' => $this->status->value,
            'status_name' => $this->status->label(),
            'cancel_reason' => $this->cancel_reason,
            'license_attachment' => $this->license_attachment,
            'permit_type' => $this->permit_type,

            'owner' => [
                'id' => $this->owner_id,
                'name' => optional($this->owner)->name,
            ],
            'captain' => [
                'id' => $this->captain_id,
                'name' => optional($this->captain)->name,
            ],
            'counter' => [
                'id' => $this->counter_id,
                'name' => optional($this->counter)->name,
            ],
            'dalal' => [
                'id' => $this->dalal_id,
                'name' => optional($this->dalal)->name,
            ],

            'boat' => [
                'name' => $this->boat_name,
                'number' => $this->boat_number,
                'color' => $this->boat_color,
                'length' => $this->boat_length,
                'width' => $this->boat_width,
            ],
            'fish_stock' => FishStockResource::collection($this->fishStocks),
            'sales' => SaleResource::collection($this->sales),
            'departure_time' => Carbon::parse($this->departure_time)->format('h:i A'),
            'return_time' => Carbon::parse($this->return_time)->format('h:i A'),

            'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'), // أو 'd-m-Y'
            'end_date' => Carbon::parse($this->end_date)->format('Y-m-d'),
            'duration' => $this->duration_text,

            'actual_start_datetime' => $this->actual_start_datetime
                ? Carbon::parse($this->actual_start_datetime)->format('Y-m-d h:i A')
                : null,

            'actual_end_datetime' => $this->actual_end_datetime
                ? Carbon::parse($this->actual_end_datetime)->format('Y-m-d h:i A')
                : null,
            'crew_count' => optional($this->captain)->crew_count,
            'departure_port' => $this->departure_port,
            'return_port' => $this->return_port,

            'location' => [
                'region' => optional($this->region)->name,
                'governorate' => optional($this->governorate)->name,
                //                'city' => optional($this->city)->name,
                'port' => optional($this->port)->name,
            ],

            'notes' => $this->notes,
            //            'created_by' => $this->created_by,
            //            'updated_by' => $this->updated_by,

            'created_at' => $this->created_at,
        ];

        return $data;
    }
}
