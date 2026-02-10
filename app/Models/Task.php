<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'type',
        'description',
        'status',
        'priority',
        'priority_score',
        'due_date',
        'sla_minutes',
        'escalated_at',
        'suggested_actions',
        'taskable_type',
        'taskable_id',
        'project_id',
        'owner_id',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'date',
        'escalated_at' => 'datetime',
        'suggested_actions' => 'array',
        'priority_score' => 'integer',
        'sla_minutes' => 'integer',
    ];

    /**
     * Get the parent taskable model (Contact, Lead, Deal, etc.).
     */
    public function taskable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
