<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class PaymentModes extends Model{
    protected $table = 'wa_payment_modes';
    public $timestamps = false;

    protected $guarded = [];
}

