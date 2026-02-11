<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'industry',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'notes',
        'owner_id',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function engagements()
    {
        return $this->morphMany(Engagement::class, 'engageable');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Convert client back to a lead
     */
    public function convertToLead()
    {
        return \DB::transaction(function () {
            // 1. Create Lead
            $contact = $this->contacts()->first();
            
            $lead = Lead::create([
                'first_name' => $contact ? $contact->first_name : $this->name,
                'last_name' => $contact ? $contact->last_name : '',
                'email' => $contact ? $contact->email : null,
                'phone' => $this->phone,
                'company_name' => $this->name,
                'status' => 'new',
                'address_line1' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'owner_id' => $this->owner_id,
                'notes' => $this->notes,
            ]);

            // 2. Move/Link activities and engagements
            $this->activities()->update([
                'activityable_type' => Lead::class,
                'activityable_id' => $lead->id
            ]);

            $this->engagements()->update([
                'engageable_type' => Lead::class,
                'engageable_id' => $lead->id
            ]);

            // 3. Delete Client and Contacts (optional, but requested as "conversion")
            // For safety, let's just mark the client as inactive or similar, 
            // but the user said "convert", so maybe soft delete?
            $this->delete();
            $this->contacts()->delete();

            return $lead;
        });
    }

    public function convertToPartner()
    {
        return \DB::transaction(function () {
            $partner = Partner::create([
                'name' => $this->name,
                'type' => 'reseller',
                'status' => 'active',
                'phone' => $this->phone,
                'website' => $this->website,
                'description' => "Converted from Client: " . ($this->notes ?: 'No notes'),
                'user_id' => $this->owner_id,
            ]);

            $this->delete();
            $this->contacts()->delete();

            return $partner;
        });
    }

    /**
     * Convert client to a support ticket
     */
    public function convertToTicket()
    {
        return \DB::transaction(function () {
            $contact = $this->contacts()->first();
            
            $ticket = Ticket::create([
                'ticket_number' => 'TKT-' . strtoupper(uniqid()),
                'subject' => "Support Request from Client: " . $this->name,
                'description' => "Client Name: " . $this->name . "\nPrimary Contact: " . ($contact ? $contact->full_name : 'N/A') . "\nNotes: " . ($this->notes ?: 'N/A'),
                'status' => 'open',
                'priority' => 'medium',
                'client_id' => $this->id,
                'contact_id' => $contact ? $contact->id : null,
                'user_id' => auth()->id(),
                'assigned_to' => $this->owner_id,
            ]);

            return $ticket;
        });
    }
}
