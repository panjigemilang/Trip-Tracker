<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'time' => $this->time,
            'title' => $this->title,
            'location' => $this->location,
            'notes' => $this->notes,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'images' => $this->whenLoaded('images'),
        ];
    }
}
