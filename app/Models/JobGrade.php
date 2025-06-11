<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobGrade extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function jobLevel()
    {
        return $this->belongsTo(JobLevel::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
