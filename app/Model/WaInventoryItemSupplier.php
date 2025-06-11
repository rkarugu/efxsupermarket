<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class WaInventoryItemSupplier extends Model{

    
    protected $table = "wa_inventory_item_suppliers";

    protected $guarded = [];
    
    use Sluggable;
    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'supplier_code',
            'onUpdate' => true
        ]];
    }
    
    
    public function suppliers()
    {
        return $this->belongsToMany(WaSupplier::class, 'wa_supplier_id');
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(WaInventoryItem::class, 'wa_inventory_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class,'wa_supplier_id');
    }

    public function inventory_item()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }
}
