<?php

namespace App\Model;

use App\Models\BaseModel;

class PaymentFrequency extends BaseModel{
    protected $table = 'wa_payment_frequency';
    public $timestamps = false;

    protected $guarded = [];
}

