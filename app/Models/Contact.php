<?php

namespace App\Models;

use App\Traits\Workflowable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
