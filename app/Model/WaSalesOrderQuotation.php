<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaSalesOrderQuotation extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'sales_order_number',
            'onUpdate'=>true
        ]];
    }

     public function getRelatedCustomer() {
        return $this->belongsTo('App\Model\WaCustomer', 'wa_customer_id');
    }

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaSalesOrderQuotationItem', 'wa_sales_order_quotation_id');
    }
    

     
}


