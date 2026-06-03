<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoatResource extends JsonResource
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
            'length' => $this->length,
            'width' => $this->width,
            'color' => $this->color,
            'type' => $this->type,
            'license_region' => [
                'id' => $this->license_region_id,
                'name' => optional($this->licenseRegion)->name,
            ],
            'license_date' => $this->license_date,
            'license_date_expire' => $this->license_date_expire,
            'body_number' => $this->body_number,
            'body_type' => $this->body_type,
            'callsign_number' => $this->callsign_number,
            'serial_number' => $this->serial_number,
            'engine_status' => $this->engine_status,
            'engine_type' => $this->engine_type,
            'engine_power' => $this->engine_power,
            'crew_number' => $this->crew_number,
            'payload' => $this->payload,
            'region' => [
                'id' => $this->region_id,
                'name' => optional($this->region)->name,
            ],
            'governorate' => [
                'id' => $this->governorate_id,
                'name' => optional($this->governorate)->name,
            ],
            'port' => [
                'id' => $this->port_id,
                'name' => optional($this->port)->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $data;
    }
}
