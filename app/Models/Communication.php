<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = [
        'type',
        'direction',
        'subject',
        'content',
        'status',
        'from_identifier',
        'to_identifier',
        'communicable_type',
        'communicable_id',
        'metadata',
        'owner_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function communicable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
