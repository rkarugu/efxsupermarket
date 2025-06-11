<?php

namespace App\Model;

use App\Models\BaseModel;

class Nationality extends BaseModel{
    protected $table = 'wa_nationality';
    public $timestamps = false;

    protected $guarded = [];
}

