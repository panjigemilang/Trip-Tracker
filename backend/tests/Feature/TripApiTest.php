<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Trip;

class TripApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_trip()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/trips', [
            'title' => 'Test Trip',
            'description' => 'A wonderful test trip',
            'status' => 'planned'
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'Test Trip');
        
        $this->assertDatabaseHas('trips', [
            'title' => 'Test Trip',
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_fetch_their_trips()
    {
        $user = User::factory()->create();
        Trip::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/trips');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}
