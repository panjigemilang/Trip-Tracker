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

        $activityData = \Illuminate\Support\Arr::except($request->validated(), ['images']);
        $activity = $trip->activities()->create($activityData);

        if ($request->has('images')) {
            $this->storeImages($activity, $request->input('images'));
        }

        // Update trip dates when a new activity is added
        $trip->update([
            'start_date' => $trip->activities()->min('date'),
            'end_date' => $trip->activities()->max('date'),
        ]);

        return new ActivityResource($activity->fresh('images'));
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
