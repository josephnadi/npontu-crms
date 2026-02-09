<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'subject',
        'description',
        'activity_date',
        'due_date',
        'completed_at',
        'status',
        'duration_minutes',
        'activityable_type',
        'activityable_id',
        'tags',
        'custom_fields',
        'owner_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'tags' => 'array',
        'custom_fields' => 'array',
    ];

    /**
     * Get the parent activityable model (Contact, Lead, Deal, or Client).
     */
    public function activityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
