<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class IndisciplineCategory extends BaseModel{
    protected $table = 'wa_indiscipline_category';
    public $timestamps = false;

    protected $guarded = [];
}


