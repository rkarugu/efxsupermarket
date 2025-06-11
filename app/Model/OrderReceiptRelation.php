<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class OrderReceiptRelation extends Model
{
       protected $fillable = array('order_id', 'order_receipt_id');

    public function getAssociateOrderForReceipt() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }
}


