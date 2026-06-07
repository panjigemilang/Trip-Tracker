<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JourneyActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'journey_id',
        'activity_id',
        'status',
        'status_changed_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'status' => \App\Enums\ActivityStatus::class,
    ];

    public function journey()
    {
        return $this->belongsTo(Journey::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
