<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Return only the desired keys
        if (in_array($this->key, ['title', 'title_en', 'logo', 'APP_ENV', 'domain', 'website_maintenance', 'email', 'phone'])) {
            return [
                'id' => $this->id,
                'key' => $this->key,
                'value' => $this->value,
            ];
        }

        return [];
    }
}
