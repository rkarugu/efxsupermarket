<?php

namespace App\Models;

use App\Model\Branch;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaInventoryItemPrice extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function item()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }

    public function location()
    {
        return $this->belongsTo(WaLocationAndStore::class,'store_location_id');
    }

    public function inventoryitemprices(): HasMany
    {
        return $this->hasMany(UpdateItemPriceUtilityLog::class, 'id', 'wa_inventory_item_price_id');
    }
}
