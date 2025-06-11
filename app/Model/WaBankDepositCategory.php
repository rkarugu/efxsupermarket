<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaBankDepositCategory extends Model
{
    protected $guarded = [];
    public function received_from()
    {
        if($this->receiver_type == 'Customer'){
            return $this->belongsTo(WaCustomer::class,'received_from_id')->select(['wa_customers.*','wa_customers.customer_code as code']);
        }
        return $this->belongsTo(WaSupplier::class,'received_from_id')->select(['wa_suppliers.*','wa_suppliers.supplier_code as code']);
    }
    public function account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'account_id');
    }
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
    public function vat()
    {
        return $this->belongsTo(TaxManager::class,'vat_id');
    }
}
