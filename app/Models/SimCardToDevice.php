<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimCardToDevice extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function simCard(): BelongsTo
    {
        return $this->belongsTo(DeviceSimCard::class,'simcard_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class,'device_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
