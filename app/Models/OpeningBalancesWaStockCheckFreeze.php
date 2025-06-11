<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalancesWaStockCheckFreeze extends Model
{
    use HasFactory;
    public function getAssociateItems() {
        return $this->hasMany('App\Models\OpeningBalancesWaStockCheckFreezeItem', 'opening_balances_wa_stock_check_freeze_id');
    }
    
    public function getAssociateLocationDetail() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
    
    public function getAssociateUserDetail() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    public function unit_of_measure() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measure_id');
    }
}
