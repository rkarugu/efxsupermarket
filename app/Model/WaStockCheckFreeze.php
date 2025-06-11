<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStockCheckFreeze extends Model
{
    public function getAssociateItems() {
        return $this->hasMany('App\Model\WaStockCheckFreezeItem', 'wa_stock_check_freeze_id');
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


