<?php

namespace App\Model;

use App\Models\BaseModel;

class Branch extends BaseModel{
    protected $table = 'wa_branch';
    public $timestamps = false;

    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}




