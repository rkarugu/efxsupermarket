<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Vehicle;

class VehicleExemptionSchedule extends BaseModel
{
    use HasFactory;
    protected $table = 'vehicle_exemption_schedules';
    public function getVehiclesAttribute()
    {
        $vehicleIds = explode(',', $this->vehicle_ids);

        return Vehicle::whereIn('id', $vehicleIds)->get();
    }
}
