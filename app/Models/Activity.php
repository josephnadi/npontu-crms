<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Workflowable;

class Activity extends Model
{
    use HasFactory, SoftDeletes, Workflowable;

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

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', now());
    }

    public function markAsCompleted()
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
