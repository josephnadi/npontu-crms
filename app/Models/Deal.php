<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'value', 'currency', 'deal_stage_id',
        'contact_name', 'client_name', 'expected_close_date',
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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
