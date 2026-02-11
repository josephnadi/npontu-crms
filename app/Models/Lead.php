<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Workflowable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
    use HasFactory, SoftDeletes, Workflowable;

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
     * Convert lead to a client and optionally a deal.
     */
    public function convertToClient(array $dealData = [])
    {
        return \DB::transaction(function () use ($dealData) {
            // 1. Create Client
            $client = Client::create([
                'name' => $this->company_name ?: $this->full_name,
                'industry' => null, // Lead doesn't have industry, but we could add it
                'website' => null,
                'phone' => $this->phone,
                'address' => $this->address_line1,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'notes' => $this->notes,
                'owner_id' => $this->owner_id,
            ]);

            // 2. Create Contact
            $contact = Contact::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'mobile' => $this->mobile,
                'job_title' => $this->job_title,
                'company_name' => $this->company_name,
                'client_id' => $client->id,
                'address' => $this->address_line1,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'owner_id' => $this->owner_id,
                'created_by' => $this->owner_id,
            ]);

            // 3. Create Deal if requested
            $deal = null;
            if (!empty($dealData) && isset($dealData['create_deal']) && $dealData['create_deal']) {
                $deal = Deal::create([
                    'title' => $dealData['deal_title'],
                    'value' => $dealData['deal_value'],
                    'deal_stage_id' => $dealData['deal_stage_id'],
                    'client_id' => $client->id,
                    'contact_id' => $contact->id,
                    'owner_id' => $this->owner_id,
                    'status' => 'open',
                ]);
            }

            // 4. Move activities and engagements
            $this->activities()->update([
                'activityable_type' => Client::class,
                'activityable_id' => $client->id
            ]);

            $this->engagements()->update([
                'engageable_type' => Client::class,
                'engageable_id' => $client->id
            ]);

            // 5. Mark lead as converted
            $this->update([
                'status' => 'converted',
                'converted_to_client_id' => $client->id,
                'converted_to_deal_id' => $deal?->id,
                'converted_at' => now(),
            ]);

            return $client;
        });
    }

    public function convertToPartner()
    {
        return DB::transaction(function () {
            $partner = Partner::create([
                'name' => $this->company_name ?: $this->full_name,
                'type' => 'reseller',
                'status' => 'active',
                'email' => $this->email,
                'phone' => $this->phone,
                'user_id' => $this->owner_id,
                'description' => "Converted from Lead: " . ($this->notes ?: 'No notes'),
            ]);

            $this->update([
                'status' => 'converted',
                'converted_at' => now(),
            ]);

            return $partner;
        });
    }

    /**
     * Convert lead to a support ticket
     */
    public function convertToTicket()
    {
        return DB::transaction(function () {
            // Check if we should convert to client first?
            // Usually tickets are for clients/contacts.
            // But sometimes leads have issues too.
            
            $ticket = Ticket::create([
                'ticket_number' => 'TKT-' . strtoupper(uniqid()),
                'subject' => "Inquiry from Lead: " . $this->full_name,
                'description' => "Lead Company: " . ($this->company_name ?: 'N/A') . "\nLead Email: " . $this->email . "\nNotes: " . ($this->notes ?: 'N/A'),
                'status' => 'open',
                'priority' => 'medium',
                'user_id' => auth()->id(),
                'assigned_to' => $this->owner_id,
            ]);

            return $ticket;
        });
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
