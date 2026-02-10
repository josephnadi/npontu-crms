<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingAutomation extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'description',
        'trigger_config',
        'content_config',
        'sent_count',
        'open_count',
        'click_count',
        'owner_id',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'content_config' => 'array',
        'sent_count' => 'integer',
        'open_count' => 'integer',
        'click_count' => 'integer',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
