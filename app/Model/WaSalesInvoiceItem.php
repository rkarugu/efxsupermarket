<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class WaSalesInvoiceItem extends Model
{
   
	 

     public function getUnitOfMeasure() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'unit_of_measure_id');
    }
    

    

     
}


