<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderSaleRepRelation extends Model
{
    
   
      public function representativeDetail() {
        return $this->belongsTo('App\Model\User', 'representative_id');
    }
}


