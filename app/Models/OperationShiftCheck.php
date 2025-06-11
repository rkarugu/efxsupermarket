<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationShiftCheck extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function operationShift()
    {
        return $this->belongsTo(OperationShift::class);
    }

    public function checkDetails()
    {
        return $this->hasMany(OperationShiftCheckDetail::class);
    }
}
