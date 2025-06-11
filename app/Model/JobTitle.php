<?php

namespace App\Model;

use App\Models\BaseModel;

class JobTitle extends BaseModel{
    protected $table = 'wa_job_title';
    public $timestamps = false;

    protected $guarded = [];
}

