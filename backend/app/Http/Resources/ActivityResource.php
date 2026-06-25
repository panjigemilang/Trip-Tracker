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
            'date' => $this->date ? ($this->date instanceof \DateTimeInterface ? $this->date->format('Y-m-d') : \Carbon\Carbon::parse($this->date)->format('Y-m-d')) : null,
            // The `time` column is a SQL TIME type and comes back as "H:i:s"
            // (e.g. "14:30:00"). The client (and this API's own validation,
            // `date_format:H:i`) works in "H:i", so format it consistently here.
            'time' => $this->time ? \Carbon\Carbon::parse($this->time)->format('H:i') : null,
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
