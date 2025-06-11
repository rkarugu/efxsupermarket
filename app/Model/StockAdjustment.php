<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class StockAdjustment extends Model
{
    public function item()
    {
        return $this->belongsTo(WaInventoryItem::class,'item_id');
    }
    
    public function location()
    {
        return $this->belongsTo(WaLocationAndStore::class,'wa_location_and_store_id');
    }
}


