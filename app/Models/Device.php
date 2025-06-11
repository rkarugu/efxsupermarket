<?php

namespace App\Models;

use App\Model\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class,'device_type_id');
    }

    public function deviceLogs(): HasMany
    {
        return $this->hasMany(DeviceLog::class,'device_id');
    }

    public function latestDeviceLog()
    {
        return $this->hasOne(DeviceLog::class, 'device_id')->latest();
    }

    public function secondLatestDeviceLog()
    {
        return $this->hasOne(DeviceLog::class, 'device_id')
                    ->where('is_received', 1)
                    ->latest();
    }

    public function simCard(): BelongsTo
    {
        return $this->belongsTo(DeviceSimCard::class,'simcard_id');
    }

    public function repair(): HasMany
    {
        return $this->hasMany(DeviceRepair::class,'device_id');
    }

    public function latestRepair()
    {
        return $this->hasOne(DeviceRepair::class, 'device_id')->latest();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class,'branch_id');
    }

}
