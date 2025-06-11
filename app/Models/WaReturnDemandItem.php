<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaReturnDemandItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function returnDemand()
    {
        return $this->belongsTo(WaReturnDemand::class, 'wa_return_demand_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
}
