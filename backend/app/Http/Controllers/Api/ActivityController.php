<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class, 'activity');
    }

    public function index(Trip $trip): AnonymousResourceCollection
    {
        $this->authorize('view', $trip);

        $activities = $trip->activities()->with('images')->get();

        return ActivityResource::collection($activities);
    }

    public function store(StoreActivityRequest $request, Trip $trip): ActivityResource
    {
        $this->authorize('update', $trip);

        $activity = $trip->activities()->create($request->validated());

        // Update trip dates when a new activity is added
        $trip->update([
            'start_date' => $trip->activities()->min('date'),
            'end_date' => $trip->activities()->max('date'),
        ]);

        return new ActivityResource($activity);
    }

    public function show(Trip $trip, Activity $activity): ActivityResource
    {
        $this->authorize('view', $activity);

        return new ActivityResource($activity->load('images'));
    }

    public function update(UpdateActivityRequest $request, Trip $trip, Activity $activity): ActivityResource
    {
        $activity->update($request->validated());

        // Update trip dates
        $trip->update([
            'start_date' => $trip->activities()->min('date'),
            'end_date' => $trip->activities()->max('date'),
        ]);

        return new ActivityResource($activity->fresh('images'));
    }

    public function destroy(Trip $trip, Activity $activity): \Illuminate\Http\JsonResponse
    {
        $activity->delete();

        // Update trip dates
        $trip->update([
            'start_date' => $trip->activities()->min('date'),
            'end_date' => $trip->activities()->max('date'),
        ]);

        return response()->json([
            'message' => 'Activity deleted successfully',
        ]);
    }
}
