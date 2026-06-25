<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'expires_at' => $this->expires_at ? $this->expires_at->toIso8601String() : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'first_image_url' => $this->first_image_url,
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            // The client looks for a nested `journey: {id, status}` object
            // (not a bare `journey_id`) to know whether a trip has an active
            // journey and what its status is.
            'journey' => $this->when($this->relationLoaded('journey') && $this->journey, fn () => [
                'id' => $this->journey->id,
                'status' => $this->journey->status,
            ]),
        ];
    }
}
