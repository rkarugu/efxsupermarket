<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledNotification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'roles' => 'json',
        'users' => 'json',
        'emails' => 'array',
        'phone_numbers' => 'array',
    ];
}
