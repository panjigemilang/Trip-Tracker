<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Trip;
use App\Models\Journey;
use App\Enums\JourneyStatus;
use Carbon\Carbon;

class JourneyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_start_journey_in_future()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'start_date' => Carbon::tomorrow()->toDateString(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/trips/{$trip->id}/journey");

        $response->assertStatus(400)
                 ->assertJsonPath('message', 'A journey can only be started on its scheduled date.');
    }

    public function test_user_can_start_journey_today()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'start_date' => Carbon::today()->toDateString(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/trips/{$trip->id}/journey");

        $response->assertStatus(201);
        $this->assertDatabaseHas('journeys', [
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'status' => JourneyStatus::ACTIVE->value,
        ]);
    }

    public function test_show_completed_journey_does_not_evaluate()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        
        $journey = Journey::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'status' => JourneyStatus::COMPLETED,
            'started_at' => Carbon::now()->subDays(2),
            'ended_at' => Carbon::now()->subDay(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/journeys/{$journey->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.status', JourneyStatus::COMPLETED->value);
    }
}
