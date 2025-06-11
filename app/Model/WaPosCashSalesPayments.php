<?php
namespace App\Model;
use App\WaTenderEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class WaPosCashSalesPayments extends Model
{
    protected $table = 'wa_pos_cash_sales_payments';
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(WaPosCashSales::Class,'wa_pos_cash_sales_id');
    }
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::Class,'payment_method_id');
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function balancing_account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'balancing_account_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class,'cashier_id');
    }

    public function tender_entry()
    {
        return $this->belongsTo(WaTenderEntry::class,'wa_tender_entry_id');
    }

    public function PosCashSale(): BelongsTo
    {
        return $this->belongsTo(WaPosCashSales::class,'wa_pos_cash_sales_id');
    }

}