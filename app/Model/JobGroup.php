<?php

namespace App\Model;

use App\Models\BaseModel;

class JobGroup extends BaseModel{
    protected $table = 'wa_job_group';
    public $timestamps = false;

    protected $guarded = [];
}


