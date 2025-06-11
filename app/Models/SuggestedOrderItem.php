<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestedOrderItem extends Model
{
    use HasFactory;

    public function inventory_item(){
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }
}
