<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockExpungedVariation extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function getInventoryItemDetail() {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }

    public function getUomDetail() {
        return $this->belongsTo(WaUnitOfMeasure::class, 'uom_id');
    }

    public function expungedBy() {
        return $this->belongsTo(User::class, 'expunged_by');
    }
}
