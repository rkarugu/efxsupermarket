<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class WaCashSalesItem extends Model
{
   
	 

    public function getUnitOfMeasure() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'unit_of_measure_id');
    }
    

    
    public function item()
    {
        return $this->belongsTo(WaInventoryItem::Class,'item_no','stock_id_code');
    }
     
}


