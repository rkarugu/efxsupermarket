<?php

namespace App\Model;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaInventoryLocationUom extends BaseModel
{
    protected $table = "wa_inventory_location_uom";
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(\App\Model\WaInventoryItem::class, 'inventory_id');
    }

    public function location()
    {
        return $this->belongsTo(\App\Model\WaLocationAndStore::class, 'location_id');
    }

    public function uom()
    {
        return $this->belongsTo(\App\Model\WaUnitOfMeasure::class, 'uom_id');
    }

}
