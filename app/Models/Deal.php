<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Workflowable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Deal extends Model
{
    use HasFactory, SoftDeletes, Workflowable;

    protected $fillable = [
        'title', 'description', 'value', 'currency', 'deal_stage_id',
        'contact_id', 'client_id', 'partner_id', 'contact_name', 'client_name', 'expected_close_date',
        'actual_close_date', 'probability', 'status', 'lost_reason',
        'tags', 'custom_fields', 'owner_id', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
    ];

    public function stage()
    {
        return $this->belongsTo(DealStage::class, 'deal_stage_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all of the deal's activities.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    /**
     * Get all of the deal's engagements.
     */
    public function engagements()
    {
        return $this->morphMany(Engagement::class, 'engageable');
    }

    /**
     * Convert the deal to a project.
     */
    public function convertToProject()
    {
        return DB::transaction(function () {
            $project = Project::create([
                'name' => $this->title,
                'description' => $this->description,
                'status' => 'pending',
                'priority' => 'medium',
                'progress' => 0,
                'start_date' => now(),
                'end_date' => $this->expected_close_date ?: now()->addMonths(3),
                'budget' => $this->value,
                'owner_id' => $this->owner_id,
                'client_id' => $this->client_id,
                'deal_id' => $this->id,
            ]);

            // Move activities and engagements
            $this->activities()->update([
                'activityable_type' => Project::class,
                'activityable_id' => $project->id
            ]);

            $this->engagements()->update([
                'engageable_type' => Project::class,
                'engageable_id' => $project->id
            ]);

            // Mark deal as won
            $this->update([
                'status' => 'won',
                'actual_close_date' => now(),
            ]);

            return $project;
        });
    }

    /**
     * Convert deal back to a lead (usually if lost or needs more nurturing)
     */
    public function convertToLead()
    {
        return DB::transaction(function () {
            $lead = Lead::create([
                'first_name' => $this->contact ? $this->contact->first_name : $this->title,
                'last_name' => $this->contact ? $this->contact->last_name : ' (Deal)',
                'email' => $this->contact ? $this->contact->email : ($this->client ? $this->client->email : null),
                'phone' => $this->contact ? $this->contact->phone : ($this->client ? $this->client->phone : null),
                'company_name' => $this->client ? $this->client->name : $this->client_name,
                'status' => 'new',
                'owner_id' => $this->owner_id,
                'notes' => "Converted from Deal: {$this->title}. Reason: " . ($this->lost_reason ?: 'N/A'),
            ]);

            // Move activities and engagements
            $this->activities()->update([
                'activityable_type' => Lead::class,
                'activityable_id' => $lead->id
            ]);

            $this->engagements()->update([
                'engageable_type' => Lead::class,
                'engageable_id' => $lead->id
            ]);

            // Mark deal as lost
            $this->update([
                'status' => 'lost',
            ]);

            return $lead;
        });
    }

    public function convertToTicket()
    {
        return DB::transaction(function () {
            return Ticket::create([
                'subject' => "Support regarding deal: {$this->title}",
                'description' => "Ticket created from deal. Original description: " . ($this->description ?: 'None'),
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'general',
                'client_id' => $this->client_id,
                'contact_id' => $this->contact_id,
                'user_id' => auth()->id(),
                'ticket_number' => 'TIC-' . strtoupper(bin2hex(random_bytes(3))),
            ]);
        });
    }

    public function convertToEngagement()
    {
        return DB::transaction(function () {
            $engagement = Engagement::create([
                'subject' => "Engagement for Deal: {$this->title}",
                'description' => "Engagement created from deal. Original description: " . ($this->description ?: 'None'),
                'engagement_date' => now(),
                'type' => 'meeting', // Default type, can be changed later
                'status' => 'scheduled', // Default status
                'user_id' => auth()->id(),
                'engageable_type' => Deal::class,
                'engageable_id' => $this->id,
            ]);

            // Optionally, update the deal status or add a note
            // $this->update(['status' => 'engaged']);

            return $engagement;
        });
    }
}
