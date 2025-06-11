<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
//use Cviebrock\EloquentSluggable\Sluggable;
class WaStockMoveC extends Model
{

   protected $table = 'wa_stock_moves_C';

    public function getRelatedUser() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function getLocationOfStore() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
    
    public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

    public function getRequisition() {
        return $this->belongsTo('App\Model\WaStoreCRequisition', 'wa_store_c_requisitions_id');
    }

    public function getTotalQuantityAttribute($value)
    {
        if (isset($this->stock_id_code) && !empty($this->stock_id_code)) {
            $get_quantity = WaStockMoveC::where('stock_id_code', $this->stock_id_code)->groupBy('wa_location_and_store_id')->sum('qauntity');
            //echo $this->stock_id_code;
            //dd($get_quantity);
            if (isset($get_quantity) && !empty($get_quantity)) {
                return $get_quantity;
            } else {
                return "0.00";
            }
        }
    }

    
    

     
}


