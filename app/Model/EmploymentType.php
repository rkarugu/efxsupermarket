<?php

namespace App\Model;

use App\Models\BaseModel;

class EmploymentType extends BaseModel{
    protected $table = 'wa_employment_types';
    public $timestamps = false;

    protected $guarded = [];
}


