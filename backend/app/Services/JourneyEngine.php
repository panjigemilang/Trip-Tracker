<?php

namespace App\Services;

use App\Models\Journey;
use App\Models\JourneyActivity;
use App\Models\Activity;
use App\Enums\JourneyStatus;
use App\Enums\ActivityStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JourneyEngine
{
    public function evaluateAllActiveJourneys()
    {
        $journeys = Journey::with(['journeyActivities', 'trip.activities'])
            ->where('status', JourneyStatus::ACTIVE)
            ->get();

        foreach ($journeys as $journey) {
            $this->evaluateJourney($journey);
        }
    }

    public function evaluateJourney(Journey $journey)
    {
        $now = Carbon::now();
        $activities = $journey->trip->activities;
        $journeyActivities = $journey->journeyActivities->keyBy('activity_id');

        foreach ($activities as $index => $activity) {
            // Using time and date directly. Assumes no timezone complications for now.
            $activityDatetime = Carbon::parse($activity->date->format('Y-m-d') . ' ' . $activity->time);
            $journeyActivity = $journeyActivities->get($activity->id);

            // Create if it doesn't exist
            if (!$journeyActivity) {
                $journeyActivity = JourneyActivity::create([
                    'journey_id' => $journey->id,
                    'activity_id' => $activity->id,
                    'status' => ActivityStatus::UPCOMING,
                ]);
                $journeyActivities->put($activity->id, $journeyActivity);
            }

            // Status transitions based on time
            if ($journeyActivity->status === ActivityStatus::UPCOMING) {
                if ($now->greaterThanOrEqualTo($activityDatetime)) {
                    // Transition to CURRENT
                    $this->markAsCurrent($journey, $journeyActivity);
                } elseif ($now->copy()->addMinutes(15)->greaterThanOrEqualTo($activityDatetime)) {
                    // 15 mins away
                    // Trigger reminder notification once
                    if (!$journeyActivity->reminder_sent_at) {
                        $journey->user->notify(new \App\Notifications\JourneyActivityReminder($activity));
                        $journeyActivity->update(['reminder_sent_at' => \Carbon\Carbon::now()]);
                    }
                }
            } elseif ($journeyActivity->status === ActivityStatus::CURRENT) {
                // If there is a next activity and its time has arrived, the current one is missed (if not manually completed)
                $nextActivity = $activities->get($index + 1);
                if ($nextActivity) {
                    $nextDatetime = Carbon::parse($nextActivity->date->format('Y-m-d') . ' ' . $nextActivity->time);
                    if ($now->greaterThanOrEqualTo($nextDatetime)) {
                        $this->markAsMissed($journeyActivity);
                        // The engine will pick up the next activity in the loop naturally
                    }
                }
            }
        }

        // Check if journey is completed (all activities are terminal)
        $terminalStatuses = [
            ActivityStatus::COMPLETED,
            ActivityStatus::SKIPPED,
            ActivityStatus::CANCELLED,
            ActivityStatus::MISSED,
        ];

        $allTerminal = true;
        foreach ($activities as $activity) {
            $ja = $journeyActivities->get($activity->id);
            if (!$ja || !in_array($ja->status, $terminalStatuses)) {
                $allTerminal = false;
                break;
            }
        }

        if ($allTerminal && $activities->count() > 0) {
            $journey->update([
                'status' => JourneyStatus::COMPLETED,
                'ended_at' => Carbon::now(),
            ]);
            $journey->user->notify(new \App\Notifications\JourneyCompleted($journey));
        }
    }

    protected function markAsCurrent(Journey $journey, JourneyActivity $journeyActivity)
    {
        // If there was a previous CURRENT activity, mark it MISSED
        $previousCurrent = JourneyActivity::where('journey_id', $journey->id)
            ->where('status', ActivityStatus::CURRENT)
            ->where('id', '!=', $journeyActivity->id)
            ->first();

        if ($previousCurrent) {
            $this->markAsMissed($previousCurrent);
        }

        $journeyActivity->update([
            'status' => ActivityStatus::CURRENT,
            'status_changed_at' => Carbon::now(),
        ]);

        $journey->update([
            'current_activity_id' => $journeyActivity->activity_id,
        ]);

        $journey->user->notify(new \App\Notifications\JourneyActivityStarting($journeyActivity->activity));
    }

    protected function markAsMissed(JourneyActivity $journeyActivity)
    {
        $journeyActivity->update([
            'status' => ActivityStatus::MISSED,
            'status_changed_at' => Carbon::now(),
        ]);
        
        // TODO: dispatch Activity Missed Notification
    }

    public function startJourney($tripId, $userId)
    {
        return DB::transaction(function () use ($tripId, $userId) {
            $journey = Journey::create([
                'trip_id' => $tripId,
                'user_id' => $userId,
                'status' => JourneyStatus::ACTIVE,
                'started_at' => Carbon::now(),
            ]);

            $this->evaluateJourney($journey);

            return $journey;
        });
    }

    public function updateActivityStatus(Journey $journey, $activityId, ActivityStatus $newStatus)
    {
        $journeyActivity = JourneyActivity::firstOrCreate(
            ['journey_id' => $journey->id, 'activity_id' => $activityId],
            ['status' => ActivityStatus::UPCOMING]
        );

        $journeyActivity->update([
            'status' => $newStatus,
            'status_changed_at' => Carbon::now(),
        ]);

        if ($newStatus !== ActivityStatus::CURRENT) {
             if ($journey->current_activity_id == $activityId) {
                 $journey->update(['current_activity_id' => null]);
             }
        }

        $this->evaluateJourney($journey);
    }
}
