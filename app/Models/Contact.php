<?php

namespace App\Models;

use App\Traits\Workflowable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Contact extends Model
{
    use HasFactory, SoftDeletes, Workflowable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'job_title',
        'client_id',
        'status',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'notes',
        'tags',
        'owner_id',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
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

    public function convertToLead()
    {
        return DB::transaction(function () {
            $lead = Lead::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'job_title' => $this->job_title,
                'company_name' => $this->client ? $this->client->name : null,
                'status' => 'new',
                'owner_id' => $this->owner_id,
                'address_line1' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'notes' => $this->notes,
            ]);

            // Move activities
            $this->activities()->update([
                'activityable_type' => Lead::class,
                'activityable_id' => $lead->id
            ]);

            // Move engagements
            $this->engagements()->update([
                'engageable_type' => Lead::class,
                'engageable_id' => $lead->id
            ]);

            // If it's a client contact, we might want to keep it but mark it?
            // For now, let's just delete the contact as it's now a lead
            $this->delete();

            return $lead;
        });
    }

    public function convertToTicket()
    {
        return DB::transaction(function () {
            $ticket = Ticket::create([
                'subject' => "Support request from {$this->full_name}",
                'description' => "Ticket created from contact profile. Original notes: " . ($this->notes ?: 'None'),
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'general',
                'client_id' => $this->client_id,
                'contact_id' => $this->id,
                'user_id' => auth()->id(),
                'ticket_number' => 'TIC-' . strtoupper(bin2hex(random_bytes(3))),
            ]);

            return $ticket;
        });
    }
}
