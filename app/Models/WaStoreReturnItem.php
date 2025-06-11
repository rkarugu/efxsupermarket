<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaStoreReturnItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function storeReturn()
    {
        return $this->belongsTo(WaStoreReturn::class, 'wa_store_return_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
}
