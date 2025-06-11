<?php

namespace App\Model;

use App\Models\BaseModel;

class Gender extends BaseModel{
    protected $table = 'wa_gender';
    public $timestamps = false;

    protected $guarded = [];
}


