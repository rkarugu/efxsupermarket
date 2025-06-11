<?php

namespace App\Model;

use App\Models\BaseModel;

class Salutation extends BaseModel{
    protected $table = 'wa_salutation';
    public $timestamps = false;

    protected $guarded = [];
}

