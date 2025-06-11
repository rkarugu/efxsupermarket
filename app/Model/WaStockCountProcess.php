<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStockCountProcess extends Model
{

	protected $table = 'wa_stock_count_process';    

    public function getStoreLocationName() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
        
}


