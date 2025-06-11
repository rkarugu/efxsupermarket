<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    

     public function getAssociateTableWithOrder() {
        return $this->hasMany('App\Model\OrderBookedTable', 'order_id');
    }

    public function getAssociateItemWithOrder() {
        return $this->hasMany('App\Model\OrderedItem', 'order_id');
    }

    public function getAssociateRestro() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

     public function getAssociateOffersWithOrder() {
        return $this->hasMany('App\Model\OrderOffer', 'order_id');
    }

     public function getAssociateUserForOrder() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function getAssociateFeedback() {
        return $this->hasOne('App\Model\Feedback', 'order_id');
    }

    public function getAssociateBillRelation() {
        return $this->hasOne('App\Model\BillOrderRelation', 'order_id');
    }

    public function getAssociateDiscounterUserDetail() {
        return $this->belongsTo('App\Model\User', 'discounting_user_id');
    }

    public function getAssociateComplimentaryUserDetail() {
        return $this->belongsTo('App\Model\User', 'complimentry_code','complementary_number');
    }

      public function getCancledOrderUserDetail() {
        return $this->belongsTo('App\Model\User', 'order_canceled_by_user');
    }

    public function getCancledOrderPrintClassUserDetail() {
        return $this->belongsTo('App\Model\PrintClassUser', 'order_canceled_by_print_class_user');
    }
  

  
     
}


