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
}
