<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalancesWaStockCount extends Model
{
    use HasFactory;
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
    public function getUomDetail(){
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'uom');
    }
}
