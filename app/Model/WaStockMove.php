<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;

//use Cviebrock\EloquentSluggable\Sluggable;
class WaStockMove extends BaseModel
{

    protected $casts = ['quantity' => 'int'];

    protected $guarded = [];

    //  protected $appends = ['total_quantity'];

    public function getRelatedUser()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function getLocationOfStore()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }

    public function getInventoryItemDetail()
    {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }

    public function getTotalQuantityAttribute($value)
    {
        if (isset($this->stock_id_code) && !empty($this->stock_id_code)) {
            $get_quantity = WaStockMove::where('stock_id_code', $this->stock_id_code)->groupBy('wa_location_and_store_id')->sum('qauntity');
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
