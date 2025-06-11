<?php

namespace App\Model;

use App\Models\BaseModel;

class MaritalStatus extends BaseModel{
    protected $table = 'wa_marital_status';
    public $timestamps = false;

    protected $guarded = [];
}

