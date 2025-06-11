<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class OrderedItem extends Model
{
    
  public function getAssociateFooditem() 
  {
        return $this->belongsTo('App\Model\FoodItem', 'food_item_id');
    }

    public function getrelatedOrderForItem() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }

	 public function getInventoryItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

     public function getPurchaseOrder() {
        return $this->belongsTo('App\Model\WaPurchaseOrder', 'wa_purchase_order_id');
    }

     public function getSupplierUomDetail() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'supplier_uom_id');
    }

    public function getPrintClassName() {
        return $this->belongsTo('App\Model\PrintClass', 'print_class_id');
    }

     
}


