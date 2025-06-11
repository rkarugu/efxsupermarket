<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use DB;
class WaCashSales extends Model
{
	protected $table = 'wa_cash_sales';	
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'cash_sales_number',
            'onUpdate'=>true
        ]];
    }

     public function getRelatedCustomer() {
        return $this->belongsTo('App\Model\WaCustomer', 'wa_customer_id');
    }
     public function getRelatedSalesman() {
        return $this->belongsTo('App\Model\User', 'creater_id');
    }

     public function getRelatedCustomerAllocatedAmnt() {
        return $this->hasMany('App\Model\WaDebtorTran', 'wa_cash_sales_id');
    }

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaCashSalesItem', 'wa_cash_sales_id');
    }
    
 
}


