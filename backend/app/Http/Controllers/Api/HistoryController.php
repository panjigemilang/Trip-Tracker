<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = $request->user()->trips()
            ->whereIn('status', ['completed', 'cancelled'])
            // Journey's relation method is `journeyActivities()`, not
            // `activities()` — the old `journey.activities` path here threw
            // RelationNotFoundException on every call.
            ->with(['activities.images', 'journey.journeyActivities.activity'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'data' => TripResource::collection($histories),
        ]);
    }

    public function show(Request $request, $id)
    {
        $trip = $request->user()->trips()
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['activities.images', 'journey.journeyActivities.activity'])
            ->findOrFail($id);

        return response()->json([
            'data' => new TripResource($trip),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $trip = $request->user()->trips()
            ->whereIn('status', ['completed', 'cancelled'])
            ->findOrFail($id);

        $trip->delete();

        return response()->json(null, 204);
    }
}
