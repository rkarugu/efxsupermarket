<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaCreditNote extends Model
{
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'credit_note_number',
            'onUpdate'=>true
        ]];
    }

     public function getRelatedCustomer() {
        return $this->belongsTo('App\Model\WaCustomer', 'wa_customer_id');
    }

     public function getRelatedCustomerAllocatedAmnt() {
        return $this->hasMany('App\Model\WaDebtorTran', 'wa_credit_note_id');
    }

     public function getRelatedItem() {
         return $this->hasMany('App\Model\WaCreditNoteItem', 'wa_credit_note_id');
    }
}
