<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'expires_at',
        'slug',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expires_at' => 'datetime',
    ];

    protected $appends = ['first_image_url'];

    public function getFirstImageUrlAttribute()
    {
        if ($this->relationLoaded('activities') && $this->activities->isNotEmpty()) {
            $firstActivity = $this->activities->first();
            if ($firstActivity->relationLoaded('images') && $firstActivity->images->isNotEmpty()) {
                return $firstActivity->images->first()->path;
            }
        }
        return null;
    }

    public static function generateUniqueSlug($title, $userId, $currentTripId = null)
    {
        $slug = Str::slug($title) ?: 'trip';
        $originalSlug = $slug;
        $count = 1;
        
        while (true) {
            $query = static::where('user_id', $userId)->where('slug', $slug);
            if ($currentTripId) {
                $query->where('id', '!=', $currentTripId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = $originalSlug . '-' . (++$count);
        }
        
        return $slug;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = static::generateUniqueSlug($model->title, $model->user_id);
            }
        });
        static::updating(function ($model) {
            if ($model->isDirty('title')) {
                $model->slug = static::generateUniqueSlug($model->title, $model->user_id, $model->id);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('date')->orderBy('time')->orderBy('sort_order');
    }

    public function journey()
    {
        return $this->hasOne(Journey::class);
    }
}
