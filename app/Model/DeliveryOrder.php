<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class DeliveryOrder extends Model
{
      public function getAssociateUserForOrder() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

      public function getAssociateItemWithOrder() {
        return $this->hasMany('App\Model\DeliveryOrderItem', 'delivery_order_id');
    }

    public function getAssociateBillRelation() {
        return $this->hasOne('App\Model\BillDeliveryOrderRelation', 'delivery_order_id');
    }

     public function getAssociatedSalesRepresentatitv() {
        return $this->hasOne('App\Model\DeliveryOrderSaleRepRelation', 'delivery_order_id');
    }
  
}


