<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class WaPurchaseOrderItem extends Model
{
    public function getInventoryItemDetail()
    {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class, 'wa_inventory_item_id');
    }

    public function getPurchaseOrder()
    {
        return $this->belongsTo('App\Model\WaPurchaseOrder', 'wa_purchase_order_id');
    }

    public function getSupplierUomDetail()
    {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'supplier_uom_id');
    }

    public function get_unit_of_measure()
    {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'unit_of_measure');
    }

    public function stockMove()
    {
        return $this->hasOne('App\Model\WaStockMove', 'stock_id_code', 'item_no')->where('wa_purchase_order_id', $this->wa_purchase_order_id);
    }

    public function pack_size()
    {
        return $this->belongsTo('App\Model\PackSize', 'pack_size_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'store_location_id');
    }

    public function vat() {
        return $this->belongsTo('App\Model\TaxManager', 'tax_manager_id');
    }
}
