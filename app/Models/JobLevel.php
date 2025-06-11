<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobLevel extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function jobGroup()
    {
        return $this->belongsTo(JobGroup::class);
    }

    public function jobGrades()
    {
        return $this->hasMany(JobGrade::class);
    }

    public function jobTitles()
    {
        return $this->hasMany(JobTitle::class);
    }
}
