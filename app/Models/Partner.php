<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'email',
        'phone',
        'website',
        'description',
        'commission_rate',
        'user_id',
    ];

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deals()
    {
        // Assuming deals can be associated with partners via a pivot or foreign key
        // For now, let's keep it simple.
        return $this->hasMany(Deal::class);
    }

    /**
     * Convert partner back to a lead
     */
    public function convertToLead()
    {
        return DB::transaction(function () {
            $lead = Lead::create([
                'company_name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => 'new',
                'owner_id' => $this->user_id,
                'notes' => "Converted from Partner: " . ($this->description ?: 'No description'),
            ]);

            $this->delete();

            return $lead;
        });
    }

    /**
     * Convert partner to a ticket
     */
    public function convertToTicket()
    {
        return DB::transaction(function () {
            $ticket = Ticket::create([
                'subject' => "Support request from partner: {$this->name}",
                'description' => "Ticket created from partner profile. Original description: " . ($this->description ?: 'None'),
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'general',
                'user_id' => auth()->id(),
                'ticket_number' => 'TIC-' . strtoupper(bin2hex(random_bytes(3))),
            ]);

            return $ticket;
        });
    }
}
