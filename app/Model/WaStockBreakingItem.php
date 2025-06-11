<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class WaStockBreakingItem extends Model
{
    protected $table = 'wa_stock_breaking_items';

    public function source_item()
    {
        return $this->belongsTo(WaInventoryItem::Class,'source_item_id');
    }

    public function destination_item()
    {
        return $this->belongsTo(WaInventoryItem::Class,'destination_item_id');
    }
}