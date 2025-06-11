<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class WaSalesOrderQuotationItem extends Model
{
   
	  public function getRelatedItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

     public function getUnitOfMeasure() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'unit_of_measure_id');
    }
    

    

     
}


