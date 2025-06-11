<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobGroup extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function jobLevels()
    {
        return $this->hasMany(JobLevel::class);
    }
}
