<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierVehicleType extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'vehicle_type',
        'tonnage',
        'offloading_time',
    ];
}
