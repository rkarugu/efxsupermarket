<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalancesWaStockCountProcess extends Model
{
    use HasFactory;
    protected $table = 'wa_stock_count_process';    

    public function getStoreLocationName() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
        
}
