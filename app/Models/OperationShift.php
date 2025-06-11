<?php

namespace App\Models;

use App\Model\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationShift extends Model
{
    use HasFactory;

    protected $guarded =[];
    public function shiftChecks()
    {
        return $this->hasMany(OperationShiftCheck::class,'operation_shift_id');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }
}
