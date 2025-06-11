<?php

namespace App\Models;

use App\Model\User;
use App\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCustomSchedule extends Model
{
    use HasFactory;
    protected $table = 'vehicle_custom_schedules';
    public function getVehiclesAttribute()
    {
        $vehicleIds = explode(',', $this->vehicle_ids);

        return Vehicle::whereIn('id', $vehicleIds)->get();
    }
    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');  
    }
}
