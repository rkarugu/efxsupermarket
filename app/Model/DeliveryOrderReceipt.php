<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class DeliveryOrderReceipt extends Model
{
       public function getAssociateUserForReceipt() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

      public function getAssociateOrdersWithReceipt() {
        return $this->hasMany('App\Model\DeliveryOrderReceiptRelation', 'delivery_order_receipt_id');
    }

    public function getAssociatePaymentsWithReceipt() {
        return $this->hasMany('App\Model\DeliveryReceiptSummaryPayment', 'delivery_order_receipt_id');
    }

    public function getAssociateCashierDetail() {
        return $this->belongsTo('App\Model\User', 'cashier_id');
    }
}


