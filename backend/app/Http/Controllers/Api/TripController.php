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
            ->with(['journey', 'activities.images'])
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
            // The `trips.status` column defaults to 'draft', but the
            // documented contract's status enum is only
            // planned|active|completed|cancelled — set it explicitly so new
            // trips land in a status the client actually understands.
            $trip = $request->user()->trips()->create(
                array_merge(
                    \Illuminate\Support\Arr::except($request->validated(), ['activities', 'images']),
                    ['status' => 'planned'],
                ),
            );

            if ($request->has('activities')) {
                // `sort_order` is NOT NULL with no DB default, and it's only
                // `nullable` in validation — without this, creating a trip
                // with activities throws a not-null constraint violation.
                $sortOrder = 0;
                foreach ($request->input('activities') as $activityData) {
                    $images = $activityData['images'] ?? [];
                    unset($activityData['images']);
                    if (!isset($activityData['sort_order'])) {
                        $activityData['sort_order'] = ++$sortOrder;
                    } else {
                        $sortOrder = (int) $activityData['sort_order'];
                    }

                    $activity = $trip->activities()->create($activityData);

                    if (!empty($images)) {
                        $this->storeImages($activity, $images);
                    }
                }
            } elseif ($request->has('images') && !empty($request->input('images'))) {
                // Automatically create a starting activity if the user uploaded images
                $activity = $trip->activities()->create([
                    'title' => 'Expedition Start',
                    'date' => $trip->start_date,
                    'time' => '09:00',
                    'location' => null,
                    'notes' => 'Initialization visuals attached.',
                    'sort_order' => 1
                ]);
                $this->storeImages($activity, $request->input('images'));
            }

            return $trip->load('activities.images');
        });

        return new TripResource($trip);
    }

    public function show(Trip $trip): TripResource
    {
        return new TripResource($trip->load('activities.images', 'journey'));
    }

    public function update(UpdateTripRequest $request, Trip $trip): TripResource
    {
        $trip->update($request->validated());

        return new TripResource($trip->fresh(['activities.images', 'journey']));
    }

    public function destroy(Trip $trip): \Illuminate\Http\JsonResponse
    {
        $trip->delete();

        return response()->json([
            'message' => 'Trip deleted successfully',
        ]);
    }

    private function storeImages($activity, array $images): void
    {
        foreach ($images as $imageData) {
            if (!isset($imageData['base64']) || !isset($imageData['name'])) {
                continue;
            }
            $base64Data = $imageData['base64'];
            $originalName = $imageData['name'];
            
            if (preg_match('/^data:(image\/[a-zA-Z+-\.]+);base64,(.+)$/', $base64Data, $matches)) {
                $mimeType = $matches[1];
                $data = base64_decode($matches[2]);
                $size = strlen($data);
                
                $extension = explode('/', $mimeType)[1] ?? 'png';
                if ($extension === 'jpeg') $extension = 'jpg';
                $fileName = (string) \Illuminate\Support\Str::uuid() . '.' . $extension;
                $path = 'activity_images/' . $fileName;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $data);
                
                $activity->images()->create([
                    'disk' => 'public',
                    'path' => 'storage/' . $path,
                    'original_name' => $originalName,
                    'size_bytes' => $size,
                    'mime_type' => $mimeType,
                ]);
            }
        }
    }
}
