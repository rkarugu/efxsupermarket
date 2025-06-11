<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaSalesInvoice extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'sales_invoice_number',
            'onUpdate'=>true
        ]];
    }

     public function getRelatedCustomer() {
        return $this->belongsTo('App\Model\WaCustomer', 'wa_customer_id');
    }

     public function getRelatedCustomerAllocatedAmnt() {
        return $this->hasMany('App\Model\WaDebtorTran', 'wa_sales_invoice_id');
    }

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaSalesInvoiceItem', 'wa_sales_invoice_id');
    }
    

     
}


