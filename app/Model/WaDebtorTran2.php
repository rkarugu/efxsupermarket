<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaDebtorTran2 extends Model
{
    protected $table = 'wa_debtor_trans_25072023';
    
	 public function myInvoice() {
        return $this->belongsTo('App\Model\WaSalesInvoice', 'wa_sales_invoice_id');
    }

     public function customerDetail() {
        return $this->belongsTo('App\Model\WaCustomer', 'wa_customer_id');
    }
    
    public function userDetail() {
        return $this->belongsTo('App\Model\User', 'salesman_user_id');
    }
    public function paid_user() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}


