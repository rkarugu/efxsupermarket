<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleTelematicsData extends Model
{
    use HasFactory;
    protected $table = 'vehicle_telematics';
    protected $connection = 'telematics';
}
