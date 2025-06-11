<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class WaTyrePurchaseOrderItem extends Model
{
   
    //wa_tyre_purchase_order_item_id
    public function getRelatedGrn() {
        return $this->hasOne('App\Model\WaGrn', 'wa_purchase_order_item_id','id');
    }
    
	 public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\TyreInventory', 'wa_inventory_item_id');
    }

     public function getTyrePurchaseOrder() {
        return $this->belongsTo('App\Model\WaTyrePurchaseOrder', 'wa_tyre_purchase_order_id');
    }

     public function getSupplierUomDetail() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'supplier_uom_id');
    }

    public function stockMove() {
        return $this->hasOne('App\Model\WaStockMove', 'stock_id_code','item_no')->where('wa_tyre_purchase_order_id',$this->wa_tyre_purchase_order_id);
    }
    public function getNonStockItemDetail() {
        return $this->belongsTo('App\Model\WaNonStockInventoryItems', 'wa_inventory_item_id');
    }
    public function location() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'store_location_id');
    }
    public function pack_size() {
        return $this->belongsTo('App\Model\PackSize', 'pack_size_id');
    }


    //wa_tyre_purchase_order_item_id
    public function controlled_items() {
        return $this->hasMany('App\Model\WaTyrePurchaseOrderItemControlled', 'wa_tyre_purchase_order_item_id');
    }
}


