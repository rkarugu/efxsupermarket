<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'order_id',
        'message',
        'is_seen'
    ];

    public static function sendNotification($user, $title, $order, $message)
    {
        Notification::create([
            'user_id' => $user,
            'title' => $title,
            'order_id' => $order,
            'message' => $message
        ]);
    }
}
