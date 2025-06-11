<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaSalesmanTran extends Model
{
    protected $table = 'wa_salesman_trans';
    
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
