<?php

namespace App\Models;

use App\Traits\Workflowable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes, Workflowable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'company_name',
        'job_title',
        'lead_source_id',
        'status',
        'score',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'converted_to_client_id',
        'converted_to_deal_id',
        'converted_at',
        'notes',
        'tags',
        'custom_fields',
        'owner_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'score' => 'integer',
        'tags' => 'array',
        'custom_fields' => 'array',
        'converted_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

    /**
     * Relationship: Lead source
     */
    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

    /**
     * Relationship: Owner (User who owns this lead)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relationship: Creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Activities (Polymorphic)
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    /**
     * Relationship: Engagements (Polymorphic)
     */
    public function engagements()
    {
        return $this->morphMany(Engagement::class, 'engageable');
    }

    /**
     * Relationship: Converted to Client
     */
    public function convertedToClient()
    {
        return $this->belongsTo(Client::class, 'converted_to_client_id');
    }

    /**
     * Relationship: Converted to Deal
     */
    public function convertedToDeal()
    {
        return $this->belongsTo(Deal::class, 'converted_to_deal_id');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get active (non-converted, non-lost) leads
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['converted', 'lost']);
    }

    /**
     * Accessor: Get full name
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
