<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        "account_number",
        "sender_id",
        "api_key",
        "account_balance",
        "sms_units",
        "active",
    ];

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }
}
