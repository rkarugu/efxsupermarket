<?php

namespace App\Models;

use App\Model\WaInventoryItem;
use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceChangeHistoryLogSupplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id', 'id');
    }
}
