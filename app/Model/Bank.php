<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Bank extends BaseModel{
    protected $table = 'wa_bank';
    public $timestamps = false;

    protected $guarded = [];
}




