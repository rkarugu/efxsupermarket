<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeEmergencyContact extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }
}
