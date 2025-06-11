<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class BillOrderRelation extends Model
{
    protected $fillable = array('bill_id', 'order_id');

    public function getAssociateOrderForBill() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }

    public function getAssociateBill() {
        return $this->belongsTo('App\Model\Bill', 'bill_id');
    }
}


