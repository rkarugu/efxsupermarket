<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStockCheckFreezeItem extends Model
{
    protected $fillable = [
        'wa_inventory_item_id','wa_location_and_store_id'
    ];
    
    public function getAssociateItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }
    
    public function getUnitOfMeausureDetail() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measure_id');
    }
}


