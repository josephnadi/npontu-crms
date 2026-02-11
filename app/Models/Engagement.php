<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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

    public function convertToDeal()
    {
        return DB::transaction(function () {
            $parent = $this->engageable;
            if (!$parent) throw new \Exception('Engageable parent not found.');

            $clientId = null;
            $contactId = null;

            if ($parent instanceof Client) {
                $clientId = $parent->id;
            } elseif ($parent instanceof Lead) {
                $client = $parent->convertToClient();
                $clientId = $client->id;
            } elseif ($parent instanceof Contact) {
                $clientId = $parent->client_id;
                $contactId = $parent->id;
            }

            return Deal::create([
                'title' => 'Deal from Engagement: ' . $this->subject,
                'client_id' => $clientId,
                'contact_id' => $contactId,
                'value' => 0,
                'status' => 'open',
                'owner_id' => auth()->id(),
                'created_by' => auth()->id(),
                'description' => $this->description,
                'deal_stage_id' => DealStage::first()?->id ?? 1,
            ]);
        });
    }

    public function convertToTicket()
    {
        return DB::transaction(function () {
            $parent = $this->engageable;
            if (!$parent) throw new \Exception('Engageable parent not found.');

            $clientId = null;
            $contactId = null;

            if ($parent instanceof Client) {
                $clientId = $parent->id;
            } elseif ($parent instanceof Contact) {
                $clientId = $parent->client_id;
                $contactId = $parent->id;
            }

            return Ticket::create([
                'subject' => "Ticket from Engagement: " . $this->subject,
                'description' => $this->description,
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'general',
                'client_id' => $clientId,
                'contact_id' => $contactId,
                'user_id' => auth()->id(),
                'ticket_number' => 'TIC-' . strtoupper(bin2hex(random_bytes(3))),
            ]);
        });
    }
}
