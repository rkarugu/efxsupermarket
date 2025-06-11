<?php

namespace App\Models;

use App\Model\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkSms extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function messages(): HasMany
    {
        return $this->hasMany(BulkSmsMessage::class,'bulk_sms_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class,'branch_id');
    }
}
