<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Trip::class, 'trip');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $trips = $request->user()->trips()
            ->when($request->query('status'), function ($query, $status) {
                $query->whereIn('status', explode(',', $status));
            })
            ->orderBy($request->query('sort', 'created_at'), $request->query('order', 'desc'))
            ->paginate((int) $request->query('per_page', 15));

        return TripResource::collection($trips);
    }

    public function store(StoreTripRequest $request): TripResource
    {
        $trip = DB::transaction(function () use ($request) {
            $trip = $request->user()->trips()->create($request->validated());

            if ($request->has('activities')) {
                $trip->activities()->createMany($request->input('activities'));
            }

            return $trip->load('activities');
        });

        return new TripResource($trip);
    }

    public function show(Trip $trip): TripResource
    {
        return new TripResource($trip->load('activities.images'));
    }

    public function update(UpdateTripRequest $request, Trip $trip): TripResource
    {
        $trip->update($request->validated());

        return new TripResource($trip->fresh('activities'));
    }

    public function destroy(Trip $trip): \Illuminate\Http\JsonResponse
    {
        $trip->delete();

        return response()->json([
            'message' => 'Trip deleted successfully',
        ]);
    }
}
