<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start',
        'end',
        'location',
        'activity_type_id',
        'is_focus',
        'color',
        'realization_status',
        'realization_at',
        'realization_note',
    ];

    protected $casts = [
        'start'           => 'datetime',
        'end'             => 'datetime',
        'is_focus'        => 'boolean',
        'realization_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }
}
