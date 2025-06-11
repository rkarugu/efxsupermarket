<?php

namespace App\Model;

use App\ReturnedGrn;
use App\WaSupplierInvoice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WaGrn extends BaseModel
{
    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(WaPurchaseOrder::class, 'wa_purchase_order_id');
    }

    public function lpo()
    {
        return $this->belongsTo(WaPurchaseOrder::class, 'wa_purchase_order_id');
    }

    public function rlpo()
    {
        return $this->belongsTo(WaReceivePurchaseOrder::class, 'wa_purchase_order_id', 'wa_purchase_order_id');
    }

    public function getRelatedInventoryItem()
    {
        return $this->belongsTo('App\Model\WaPurchaseOrderItem', 'wa_purchase_order_item_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(WaPurchaseOrderItem::class, 'wa_purchase_order_item_id');
    }

    public function getRelatedSupplier()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'wa_supplier_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id');
    }

    public function invoice()
    {
        return $this->hasOne(WaSupplierInvoice::class, 'grn_number', 'grn_number');
    }

    public function returnedGrns()
    {
        return $this->hasMany(ReturnedGrn::class, 'grn_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(WaInventoryItem::class, 'item_code', 'stock_id_code');
    }

    public function getTotalQoh()
    {
        return $qty = DB::table('wa_stock_moves')
            ->where('stock_id_code', $this->item_code)
            ->whereDate('created_at', '<', $this->created_at)
            ->select(DB::RAW('SUM(qauntity) as qauntity'))
            ->value('qauntity');
        if ($qty) {
            return $qty;
        }
        return 0;
    }

    public function stockMoves()
    {
        return $this->hasMany(WaStockMove::class, 'document_no', 'grn_number');
    }

    public function returns()
    {
        return $this->hasMany(ReturnedGrn::class, 'grn_number', 'grn_number');
    }

    public function returnsToPrint()
    {
        return $this->returns()->where('approved', 1)->where('is_printed', 0);
    }

    public function getItemQuantityAttribute()
    {
        $invoiceInfo = json_decode($this->invoice_info);

        return (float) $invoiceInfo->qty;
    }

    public function getItemPriceAttribute()
    {
        $invoiceInfo = json_decode($this->invoice_info);

        return (float) $invoiceInfo->order_price;
    }

    public function getItemVatRateAttribute()
    {
        $invoiceInfo = json_decode($this->invoice_info);

        return (float) $invoiceInfo->vat_rate;
    }    

    public function getItemDiscountAttribute()
    {
        $invoiceInfo = json_decode($this->invoice_info);

        return isset($invoiceInfo->total_discount) ? (float) $invoiceInfo->total_discount : 0;
    }

    public function getItemTotalAttribute()
    {
        return $this->item_price * $this->item_quantity - $this->item_discount;
    }

    public function getItemVatAttribute()
    {
        return getVatAmount($this->item_total, $this->item_vat_rate);
    }

    public function getItemExclusiveAttribute()
    {
        return getExclusiveAmount($this->item_total, $this->item_vat_rate);
    }
}
