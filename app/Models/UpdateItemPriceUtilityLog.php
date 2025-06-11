<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateItemPriceUtilityLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function initiatedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WaLocationAndStore::class, 'wa_location_and_store_id', 'id');
    }

    public function locationitemprice(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItemPrice::class, 'wa_inventory_item_price_id', 'id');
    }

}
