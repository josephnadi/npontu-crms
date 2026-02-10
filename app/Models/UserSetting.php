<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'locale',
        'timezone',
        'currency',
        'theme',
        'notif_in_app',
        'notif_email',
        'notif_due_reminders',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
