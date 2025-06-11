<?php

namespace App\Model;

use App\Models\BaseModel;

class TerminationTypes extends BaseModel{
    protected $table = 'wa_termination_types';
    public $timestamps = false;

    protected $guarded = [];
}
