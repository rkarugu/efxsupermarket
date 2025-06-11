<?php
namespace App\Model;

use App\WaInventoryLocationTransferItemReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class WaInventoryLocationTransferItem extends Model
{
    protected $guarded = [];

	 public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }

    public function inventoryLocationTransferItemReturns()
    {
        return $this->hasMany(WaInventoryLocationTransferItemReturn::class);
    }

     public function getTransferLocation() {
        return $this->belongsTo('App\Model\WaInventoryLocationTransfer', 'wa_inventory_location_transfer_id');
    }
    public function getRequisitionItem() {
        return $this->belongsTo('App\Model\WaInternalRequisitionItem', 'wa_internal_requisition_item_id');
    }
    
    public function returned_by()
    {
        return $this->belongsTo(User::class,'return_by');
    }

    public function getDiscount(): float
    {
        return WaInternalRequisitionItem::find($this->wa_internal_requisition_item_id)->discount;
    }
}


