<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

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
        'notes',
        'tags',
        'custom_fields',
        'owner_id',
        'created_by',
        'updated_by',
        // 'converted_to_contact_id', // Uncomment when contacts table exists
        // 'converted_to_deal_id',    // Uncomment when deals table exists
        // 'converted_at',            // Uncomment when conversion is enabled
    ];

    protected $casts = [
        'score' => 'integer',
        'tags' => 'array',
        'custom_fields' => 'array',
        'converted_at' => 'datetime',
    ];

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
     * Relationship: Creator (User who created this lead)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Updater (User who last updated this lead)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship: Converted to Contact
     * Uncomment when Contact model is created
     */
    // public function convertedToContact()
    // {
    //     return $this->belongsTo(Contact::class, 'converted_to_contact_id');
    // }

    /**
     * Relationship: Converted to Deal
     * Uncomment when Deal model is created
     */
    // public function convertedToDeal()
    // {
    //     return $this->belongsTo(Deal::class, 'converted_to_deal_id');
    // }

    /**
     * Relationship: Activities
     * Uncomment when Activity model is created
     */
    // public function activities()
    // {
    //     return $this->morphMany(Activity::class, 'related');
    // }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get qualified leads
     */
    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    /**
     * Scope: Get leads that haven't been converted
     */
    public function scopeNotConverted($query)
    {
        return $query->where('status', '!=', 'converted');
    }

    /**
     * Accessor: Get full name
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
