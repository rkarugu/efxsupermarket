<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStockCheckFreezeC extends Model
{
    public $table = "wa_stock_check_freezes_c";
    public function getAssociateItems() {
        return $this->hasMany('App\Model\WaStockCheckFreezeCItem', 'wa_stock_check_freeze_id');
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


