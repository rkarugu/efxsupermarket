<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaExternalRequisitionItem extends Model
{
   protected $guarded = [];
    public function unit_of_measures()
    {
        return $this->belongsTo(WaUnitOfMeasure::class, 'unit_of_measure_id');
    }
	 public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

     public function getExternalPurchaseId() {
        return $this->belongsTo('App\Model\WaExternalRequisition', 'wa_external_requisition_id');
    }

   
}


