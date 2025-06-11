<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaStockCountC extends Model
{
    public $table = "wa_stock_counts_c";
    
    protected $fillable = [
        'wa_inventory_item_id','wa_location_and_store_id'
    ];
    
    public function getAssociateItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }
    
    public function getAssociateLocationDetail() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
    public function category() {
        return $this->belongsTo('App\Model\WaInventoryCategory', 'category_id');
    }
}


