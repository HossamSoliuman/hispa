<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'region' => $this->region?->name,
            'governorate' => $this->governorate?->name,
            //            'city' => $this->city?->name,
            'port' => $this->port?->name,
            'address' => $this->address,
            'logo' => $this->logo,
            'record_number' => $this->record_number,
            'record_type' => $this->record_type,
            'attachment' => $this->attachment,
            'role' => $this->role,
            'id_number' => $this->id_number,
            'tax_number' => $this->tax_number,
            'nationality' => $this->nationality,
            'boat_name' => $this->boat_name,
            'boat_number' => $this->boat_number,
            'crew_count' => $this->crew_count,
            'fcm_token' => $this->fcm_token,
            'token' => $this->token,

        ];

        return $data;
    }
}
