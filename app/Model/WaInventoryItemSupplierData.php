<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaInventoryItemSupplierData extends Model
{
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class,'wa_supplier_id');
    }

    public function inventory_item()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }
}
