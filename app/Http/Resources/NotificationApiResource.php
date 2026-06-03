<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $locale = app()->getLocale();

        $data = json_decode($this->data, true);

        if (isset($data['title']) && is_array($data['title'])) {
            $data['title'] = $data['title'][$locale] ?? $data['title']['en'] ?? reset($data['title']);
        }
        if (isset($data['body']) && is_array($data['body'])) {
            $data['body'] = $data['body'][$locale] ?? $data['body']['en'] ?? reset($data['body']);
        }

        return [
            'id' => $this->id,
            'data' => $data,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
