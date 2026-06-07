<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TripValidationService
{
    /**
     * Validates that there are no gaps greater than 7 days between activities in a trip.
     *
     * @param Trip $trip
     * @throws ValidationException
     */
    public function validateGaps(Trip $trip): void
    {
        $activities = $trip->activities()->orderBy('date')->get();

        if ($activities->count() <= 1) {
            return;
        }

        $previousDate = null;

        foreach ($activities as $activity) {
            $currentDate = Carbon::parse($activity->date);

            if ($previousDate !== null) {
                $diffInDays = $previousDate->diffInDays($currentDate);

                if ($diffInDays > 7) {
                    throw ValidationException::withMessages([
                        'activities' => [
                            "There is a gap of {$diffInDays} days between activities. The maximum allowed gap is 7 days.",
                        ],
                    ]);
                }
            }

            $previousDate = $currentDate;
        }
    }
}
