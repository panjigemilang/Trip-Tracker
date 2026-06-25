<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JourneyActivityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'journey_id' => $this->journey_id,
            // `status` is a backed enum (App\Enums\ActivityStatus); cast to
            // its scalar value explicitly so it always serializes as the
            // plain string the client's enum parser expects.
            'status' => $this->status?->value,
            'status_changed_at' => $this->status_changed_at?->toIso8601String(),
            'activity' => new ActivityResource($this->whenLoaded('activity')),
        ];
    }
}
