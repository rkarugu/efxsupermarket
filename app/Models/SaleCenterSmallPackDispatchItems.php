<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleCenterSmallPackDispatchItems extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(SaleCenterSmallPackDispatch::class,'dispatch_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class,'bin_id');
    }
}
