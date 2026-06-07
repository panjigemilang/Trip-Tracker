<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TripPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Users can view their own trips (filtered in controller)
    }

    public function view(User $user, Trip $trip): bool
    {
        return (int) $user->id === (int) $trip->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Trip $trip): bool
    {
        return (int) $user->id === (int) $trip->user_id && $trip->status !== 'active';
    }

    public function delete(User $user, Trip $trip): bool
    {
        return (int) $user->id === (int) $trip->user_id;
    }

    public function restore(User $user, Trip $trip): bool
    {
        return (int) $user->id === (int) $trip->user_id;
    }

    public function forceDelete(User $user, Trip $trip): bool
    {
        return (int) $user->id === (int) $trip->user_id;
    }

    public function startJourney(User $user, Trip $trip): bool
    {
        return (int) $user->id === (int) $trip->user_id && $trip->status === 'planned';
    }
}
