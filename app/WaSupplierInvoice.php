<?php

namespace App;

use App\Model\WaPurchaseOrder;
use App\Model\WaReceivePurchaseOrder;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\Models\AdvancePaymentAllocation;
use App\Models\TradeDiscount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaSupplierInvoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'grn_date' => 'date',
        'supplier_invoice_date' => 'date',
    ];

    public function lpo()
    {
        return $this->belongsTo(WaPurchaseOrder::class, 'wa_purchase_order_id');
    }

    public function rlpo()
    {
        return $this->belongsTo(WaReceivePurchaseOrder::class, 'wa_purchase_order_id', 'wa_purchase_order_id');
    }

    public function suppTrans()
    {
        return $this->belongsTo(WaSuppTran::class, 'wa_supp_tran_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function items()
    {
        return $this->hasMany(WaSupplierInvoiceItem::class, 'wa_supplier_invoice_id');
    }

    public function discounts()
    {
        return $this->hasMany(TradeDiscount::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasOne(PaymentVoucherItem::class, 'payable_id', 'wa_supp_tran_id')
            ->where('payable_type', 'invoice');
    }

    public function voucherPayments($voucher)
    {
        return $this->payments()->where('payment_voucher_id', $voucher);
    }

    public function voucherPaymentsTotal($voucher)
    {
        return $this->voucherPayments($voucher)->sum('amount');
    }

    public function notes()
    {
        return $this->hasMany(FinancialNote::class, "wa_supp_tran_id", 'wa_supp_tran_id');
    }

    public function allocation()
    {
        return $this->hasOne(AdvancePaymentAllocation::class, "wa_supp_trans_id", 'wa_supp_tran_id');
    }
}
