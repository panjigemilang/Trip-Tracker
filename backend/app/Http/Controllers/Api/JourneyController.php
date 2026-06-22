<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Journey;
use App\Models\Activity;
use App\Enums\ActivityStatus;
use App\Enums\JourneyStatus;
use App\Http\Requests\UpdateActivityStatusRequest;
use App\Services\JourneyEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class JourneyController extends Controller
{
    protected $engine;

    public function __construct(JourneyEngine $engine)
    {
        $this->engine = $engine;
    }

    public function start(Request $request, Trip $trip)
    {
        // Add authorization using gates/policies later
        // Gate::authorize('startJourney', $trip);
        
        if ($trip->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($trip->journey()->exists()) {
            return response()->json([
                'message' => 'Journey already exists for this trip.'
            ], 400);
        }

        if ($trip->start_date && Carbon::today()->lt($trip->start_date->startOfDay())) {
            return response()->json([
                'message' => 'A journey can only be started on its scheduled date.'
            ], 400);
        }

        $journey = $this->engine->startJourney($trip->id, $request->user()->id);

        $trip->update(['status' => 'active']);

        return response()->json([
            'data' => $journey->load('journeyActivities', 'currentActivity'),
            'message' => 'Journey started successfully.'
        ], 201);
    }

    public function show(Request $request, Journey $journey)
    {
        if ($journey->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($journey->status === JourneyStatus::ACTIVE) {
            $this->engine->evaluateJourney($journey);
        }

        return response()->json([
            'data' => $journey->load('journeyActivities', 'currentActivity', 'trip.activities')
        ]);
    }

    public function updateActivityStatus(UpdateActivityStatusRequest $request, Journey $journey, Activity $activity)
    {
        if ($journey->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($activity->trip_id !== $journey->trip_id) {
            return response()->json([
                'message' => 'Activity does not belong to this journey.'
            ], 400);
        }

        // Use the validated status enum
        $status = ActivityStatus::from($request->validated()['status']);

        $this->engine->updateActivityStatus($journey, $activity->id, $status);

        return response()->json([
            'data' => $journey->fresh('journeyActivities', 'currentActivity'),
            'message' => 'Activity status updated successfully.'
        ]);
    }
}
