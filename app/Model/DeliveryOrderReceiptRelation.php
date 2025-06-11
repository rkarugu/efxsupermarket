<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderReceiptRelation extends Model
{
       protected $fillable = array('delivery_order_id', 'delivery_order_receipt_id');

    public function getAssociateOrderForReceipt() {
        return $this->belongsTo('App\Model\DeliveryOrder', 'delivery_order_id');
    }
}


