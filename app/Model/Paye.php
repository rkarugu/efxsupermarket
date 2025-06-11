<?php

namespace App\Model;

use App\Models\BaseModel;

class Paye extends BaseModel{
    protected $table = 'wa_paye';
    public $timestamps = false;

    protected $guarded = [];
}

