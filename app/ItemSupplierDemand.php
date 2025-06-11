<?php

namespace App;

use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSupplierDemand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id');
    }

    public function inventory()
    {
        return $this->belongsTo(WaSupplier::class, 'wa_inventory_item_id');
    }
}
