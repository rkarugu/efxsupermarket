<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class OrderOffer extends Model
{
    
   public function getAssociateItemwithOffers() {
       return $this->hasMany('App\Model\OrderedItem', 'order_offer_id');
    }

    public function getAssociateOffersDetail() {
       return $this->belongsTo('App\Model\Category', 'offer_id');
    }
     
}


