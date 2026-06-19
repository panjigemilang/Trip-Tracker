<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JourneyActivity extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

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
