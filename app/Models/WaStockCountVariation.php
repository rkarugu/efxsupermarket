<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaStockCountVariation extends BaseModel
{
    use HasFactory;
    protected $table = 'wa_stock_count_variation';

    public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

    public function getUomDetail() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'uom_id');
    }

    public function stockDebtorItem(): HasOne
    {
        return $this->hasOne(StockDebtorTranItem::class,'stock_count_variation_id');
    }
}
