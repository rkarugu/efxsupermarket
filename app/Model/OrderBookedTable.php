<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class OrderBookedTable extends Model
{
    public $timestamps = false;
   public function getRelativeTableData() {
        return $this->belongsTo('App\Model\TableManager', 'table_id');
    }

      public function getRelativeOrderData() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }
     
}


