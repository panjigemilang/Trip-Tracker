<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Activity $activity): bool
    {
        return (int) $user->id === (int) $activity->trip->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Activity $activity): bool
    {
        return (int) $user->id === (int) $activity->trip->user_id;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return (int) $user->id === (int) $activity->trip->user_id;
    }

    public function restore(User $user, Activity $activity): bool
    {
        return (int) $user->id === (int) $activity->trip->user_id;
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return (int) $user->id === (int) $activity->trip->user_id;
    }
}
