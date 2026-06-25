<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JourneyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'status' => $this->status?->value,
            'current_activity_id' => $this->current_activity_id,
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
            // The client checks both `activities` and `journey_activities`
            // for the activity list — provide it under the relation's own
            // name, each carrying its full nested `activity` via
            // JourneyActivityResource.
            'journey_activities' => JourneyActivityResource::collection($this->whenLoaded('journeyActivities')),
        ];
    }
}
