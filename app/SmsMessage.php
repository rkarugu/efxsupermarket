<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        "sms_account_id",
        "msisdn",
        "sms_external_id",
        "message",
        "status"
    ];

    public function smsAccount(): BelongsTo
    {
        return $this->belongsTo(SmsAccount::class);
    }
}
