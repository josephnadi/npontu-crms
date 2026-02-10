<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Engagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'subject',
        'description',
        'score',
        'engagement_date',
        'engageable_id',
        'engageable_type',
        'metadata',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'engagement_date' => 'datetime',
        'metadata' => 'array',
        'score' => 'integer',
    ];

    /**
     * Get the parent engageable model (Lead, Client, Contact, Deal).
     */
    public function engageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who recorded the engagement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the engagement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
