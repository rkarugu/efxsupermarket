<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaInventoryItemRawMaterial extends Model
{
    protected $guarded = [];

    public function inventory_item(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id', 'id');
    }

    public function raw_material(): WaInventoryItem
    {
        return WaInventoryItem::find($this->raw_material_id);
    }
}
