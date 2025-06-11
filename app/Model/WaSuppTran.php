<?php

namespace App\Model;

use App\FinancialNote;
use App\Models\AdvancePaymentAllocation;
use App\PaymentVoucherItem;
use App\WaSupplierInvoice;
use Illuminate\Database\Eloquent\Model;

class WaSuppTran extends Model
{
    protected $casts = [
        'trans_date' => 'datetime'
    ];

    protected $guarded = [];

    public function getSupplierName()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'supplier_no', 'supplier_code')->select('supplier_code', 'name');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'supplier_no', 'supplier_code');
    }

    public function getNumberSystem()
    {
        return $this->belongsTo('App\Model\WaNumerSeriesCode', 'grn_type_number', 'type_number');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(WaPurchaseOrder::class, 'wa_purchase_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function payments()
    {
        return $this->morphMany(PaymentVoucherItem::class, 'payable');
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
        return $this->hasMany(FinancialNote::class, "wa_supp_tran_id");
    }

    public function invoice()
    {
        return $this->hasOne(WaSupplierInvoice::class, "wa_supp_tran_id");
    }

    public function allocation()
    {
        return $this->hasOne(AdvancePaymentAllocation::class, "wa_supp_trans_id");
    }
}
