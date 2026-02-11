<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'client_id',
        'contact_id',
        'user_id',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertToLead()
    {
        return DB::transaction(function () {
            $lead = Lead::create([
                'first_name' => $this->contact ? $this->contact->first_name : $this->subject,
                'last_name' => $this->contact ? $this->contact->last_name : ' (Ticket)',
                'email' => $this->contact ? $this->contact->email : ($this->client ? $this->client->email : null),
                'phone' => $this->contact ? $this->contact->phone : ($this->client ? $this->client->phone : null),
                'company_name' => $this->client ? $this->client->name : null,
                'status' => 'new',
                'owner_id' => $this->assigned_to ?: auth()->id(),
                'notes' => "Converted from Ticket #{$this->ticket_number}: {$this->description}",
            ]);

            $this->update(['status' => 'closed']);

            return $lead;
        });
    }

    public function convertToDeal()
    {
        return DB::transaction(function () {
            $deal = Deal::create([
                'title' => "Opportunity from Ticket #{$this->ticket_number}",
                'value' => 0,
                'status' => 'open',
                'client_id' => $this->client_id,
                'contact_id' => $this->contact_id,
                'owner_id' => $this->assigned_to ?: auth()->id(),
                'description' => $this->description,
                'expected_close_date' => now()->addMonth(),
            ]);

            $this->update(['status' => 'closed']);

            return $deal;
        });
    }

    public function convertToContact()
    {
        return \DB::transaction(function () {
            $contact = Contact::create([
                'first_name' => $this->contact ? $this->contact->first_name : explode(' ', $this->subject)[0],
                'last_name' => $this->contact ? $this->contact->last_name : (isset(explode(' ', $this->subject)[1]) ? explode(' ', $this->subject)[1] : '(Ticket)'),
                'email' => $this->contact ? $this->contact->email : ($this->client ? $this->client->email : null),
                'phone' => $this->contact ? $this->contact->phone : ($this->client ? $this->client->phone : null),
                'job_title' => 'Customer from Ticket',
                'client_id' => $this->client_id,
                'status' => 'active',
                'owner_id' => $this->assigned_to ?: auth()->id(),
                'notes' => "Converted from Ticket #{$this->ticket_number}: {$this->description}",
            ]);

            $this->update(['status' => 'closed']);

            return $contact;
        });
    }
}
