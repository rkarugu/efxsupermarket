<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBeneficiary extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_minor' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
