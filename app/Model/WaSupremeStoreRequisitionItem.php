<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaSupremeStoreRequisitionItem extends Model
{
   

    protected $table = "wa_supreme_store_requisition_items";
    
	 public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

     public function getInternalPurchaseId() {
        return $this->belongsTo('App\Model\WaSupremeStoreRequisition', 'wa_supreme_store_requisitions_id');
    }

    public function location() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'store_location_id');
    }
}


