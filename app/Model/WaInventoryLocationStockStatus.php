<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaInventoryLocationStockStatus extends Model
{
    protected $table = "wa_inventory_location_stock_status";
    protected $guarded = [];

    public function item(){
        return $this->belongsTo(\App\Model\WaInventoryItem::class,'wa_inventory_item_id');
    }

    public function location(){
        return $this->belongsTo(\App\Model\WaLocationAndStore::class,'wa_location_and_stores_id');
    }
}