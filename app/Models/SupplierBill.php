<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\WaGlTran;
use App\Model\WaSupplier;
use App\Model\WaSuppTran;
use App\PaymentVoucherItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBill extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'wa_supplier_id');
    }

    public function location()
    {
        return $this->belongsTo(Restaurant::class, 'location_id');
    }

    public function items()
    {
        return $this->hasMany(SupplierBillItem::class, 'supplier_bill_id');
    }

    public function suppTran()
    {
        return $this->hasOne(WaSuppTran::class, 'document_no', 'bill_no');
    }

    public function payment()
    {
        return $this->morphOne(PaymentVoucherItem::class, 'payable');
    }

    public function glTransactions()
    {
        return $this->hasMany(WaGlTran::class, 'transaction_no', 'bill_no');
    }
}
