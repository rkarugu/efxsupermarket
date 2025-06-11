<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class BillDeliveryOrderRelation extends Model
{
    protected $fillable = array('delivery_order_bill_id', 'delivery_order_id');

     public function getAssociateOrderForBill() {
        return $this->belongsTo('App\Model\DeliveryOrder', 'delivery_order_id');
    }
}


