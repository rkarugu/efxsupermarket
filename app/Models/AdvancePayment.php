<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use App\PaymentVoucherItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'Pending');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id');
    }

    public function lpo()
    {
        return $this->belongsTo(WaPurchaseOrder::class, 'wa_purchase_order_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(WaPurchaseOrder::class, 'wa_purchase_order_id');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function payment()
    {
        return $this->morphOne(PaymentVoucherItem::class, 'payable');
    }
}
