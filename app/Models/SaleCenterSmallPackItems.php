<?php

namespace App\Models;

use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\Model\WaRouteCustomer;
use App\Model\WaUnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleCenterSmallPackItems extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function smallPacks(): BelongsTo
    {
        return $this->belongsTo(SaleCenterSmallPacks::class,'sale_center_small_pack_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }

    public function internalRequisitionItem(): BelongsTo
    {
        return $this->belongsTo(WaInternalRequisitionItem::class,'wa_internal_requisition_item_id');
    }

    public function routeCustomer(): BelongsTo
    {
        return $this->belongsTo(WaRouteCustomer::class,'wa_route_customer_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(WaUnitOfMeasure::class,'bin_id');
    }
}
