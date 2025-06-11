<?php

namespace App;

use App\Model\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id', 'id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(\App\Model\User::class, 'driver_id', 'id');
    }

    public function serviceIntervals(): HasMany
    {
        return $this->hasMany(VehicleServiceInterval::class, 'vehicle_id', 'id');
    }

    public function insuranceRecords(): HasMany
    {
        return $this->hasMany(VehicleInsuranceRecord::class, 'vehicle_id', 'id');
    }
    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id', 'id');
    }
    public function turnboy(): BelongsTo
    {
        return $this->belongsTo(\App\Model\User::class, 'turn_boy_id', 'id');
    }
}
