<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Journey extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'trip_id',
        'user_id',
        'status',
        'current_activity_id',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'status' => \App\Enums\JourneyStatus::class,
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentActivity()
    {
        return $this->belongsTo(Activity::class, 'current_activity_id');
    }

    public function journeyActivities()
    {
        return $this->hasMany(JourneyActivity::class);
    }
}
