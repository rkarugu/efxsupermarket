<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeProfessionalHistory extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
