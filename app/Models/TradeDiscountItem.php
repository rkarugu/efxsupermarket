<?php

namespace App\Models;

use App\WaSupplierInvoiceItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeDiscountItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tradeDiscount()
    {
        return $this->belongsTo(TradeDiscount::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(WaSupplierInvoiceItem::class, 'invoice_item_id');
    }
}
