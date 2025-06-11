<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class NWaInternalRequisitionItem extends Model
{
   

    
	 public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

     public function getInternalPurchaseId() {
        return $this->belongsTo('App\Model\NWaInternalRequisition', 'wa_internal_requisition_id');
    }

   
}


