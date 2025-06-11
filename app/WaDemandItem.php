<?php

namespace App;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaDemandItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }
    public function getDemand() {
        return $this->belongsTo('App\WaDemand', 'wa_demand_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
}
