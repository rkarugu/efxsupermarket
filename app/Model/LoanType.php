<?php

namespace App\Model;

use App\Models\BaseModel;

class LoanType extends BaseModel{
    protected $table = 'wa_loan_type';
    public $timestamps = false;

    protected $guarded = [];
}


