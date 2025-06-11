<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderBill extends Model
{
    public function getAssociateUserForBill() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function getAssociateOrdersWithBill() {
        return $this->hasMany('App\Model\BillDeliveryOrderRelation', 'delivery_order_bill_id');
    }
    
}


