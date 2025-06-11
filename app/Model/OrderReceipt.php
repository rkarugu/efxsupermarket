<?php

namespace App\Model;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;  

use Illuminate\Database\Eloquent\Model;


class OrderReceipt extends Model
{
       public function getAssociateUserForReceipt() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

      public function getAssociateOrdersWithReceipt() {
        return $this->hasMany('App\Model\OrderReceiptRelation', 'order_receipt_id');
    }

    public function getAssociatePaymentsWithReceipt() {
        $data = $this->hasMany('App\Model\ReceiptSummaryPayment', 'order_receipt_id');
        if (Input::get('restaurant')) {
            $data->where('restaurant_id', Input::get('restaurant'));
        }
        if (Input::get('payment_method')) {
                $data->where('payment_mode', Input::get('payment_method'));
        }
        return $data;
    }

    public function getAssociateCashierDetail() {
        return $this->belongsTo('App\Model\User', 'cashier_id');
    }
}


