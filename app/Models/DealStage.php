<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealStage extends Model
{
    protected $fillable = ['name', 'order_column', 'probability', 'color', 'is_active'];

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
