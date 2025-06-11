<?php

namespace App\Model;

use App\Models\BaseModel;

class NHIF extends BaseModel{
    protected $table = 'wa_nhif';
    public $timestamps = false;

    protected $guarded = [];
}

