<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['channels_array'];

    public function channelsArray(): Attribute
    {
        if (!$this->notification_channels) {
            return Attribute::make(get: fn() => []);
        }

        return Attribute::make(get: fn() => explode(',', $this->notification_channels));
    }
}
