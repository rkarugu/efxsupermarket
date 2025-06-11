<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\WaDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeContract extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function department()
    {
        return $this->belongsTo(WaDepartment::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }
}
