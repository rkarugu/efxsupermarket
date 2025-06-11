<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class EmploymentStatus extends BaseModel{
    protected $table = 'wa_employment_status';
    public $timestamps = false;

    protected $guarded = [];
}


