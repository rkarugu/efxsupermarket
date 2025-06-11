<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewFuelEntry extends Model
{
    protected $guarded = [];
    protected $table = 'fuel_entries';

    public function getRelatedVehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
    public function getRelatedShift(){
        return $this->belongsTo(DeliverySchedule::class, 'shift_id');
    }

    public function fueledBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'fueled_by');
    }
}
