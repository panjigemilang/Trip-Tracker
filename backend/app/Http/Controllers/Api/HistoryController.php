<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = $request->user()->trips()
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['activities.images', 'journey.activities'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'data' => $histories
        ]);
    }

    public function show(Request $request, $id)
    {
        $trip = $request->user()->trips()
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['activities.images', 'journey.activities.activity'])
            ->findOrFail($id);

        return response()->json([
            'data' => $trip
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
