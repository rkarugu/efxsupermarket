<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaBanktran extends BaseModel
{
    
    public function getPaymentMethod() 
    {
        return $this->belongsTo('App\Model\PaymentMethod', 'wa_payment_method_id');
    }

    public function getPCurrencyDetail() 
    {
        return $this->belongsTo('App\Model\WaCurrencyManager', 'wa_curreny_id');
    }

      public function getCashierDetail() 
    {
        return $this->belongsTo('App\Model\User', 'cashier_id');
    }
    public function debt_or_trans() 
    {
        return $this->belongsTo('App\Model\WaDebtorTran', 'document_no','document_no')->with(['customerDetail']);
    }

     
}


