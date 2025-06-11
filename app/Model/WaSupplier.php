<?php

namespace App\Model;

use App\PaymentVoucher;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class WaSupplier extends Model
{

    use Sluggable;
    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'supplier_code',
            'onUpdate' => true
        ]];
    }
    //protected $appends = ['c_amount','total_amount', 'month_1_amount', 'month_2_amount', 'month_3_amount', 'month_last_amount'];

    protected $guarded = [];


    public function getPaymentTerm()
    {
        return $this->belongsTo('App\Model\WaPaymentTerm', 'wa_payment_term_id');
    }

    public function paymentTerm()
    {
        return $this->belongsTo('App\Model\WaPaymentTerm', 'wa_payment_term_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Restaurant::class, 'wa_supplier_branches', 'wa_supplier_id', 'restaurant_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'wa_user_suppliers', 'wa_supplier_id', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(WaInventoryItem::class, 'wa_inventory_item_suppliers', 'wa_supplier_id', 'wa_inventory_item_id');
    }

    public function grns()
    {
        return $this->hasMany(WaGrn::class, 'wa_supplier_id');
    }

    public function grnsValue($branch = null)
    {
        $grns = is_null($branch) ? $this->grns()->whereDoesntHave('invoice')->get() :
            $this->grns()->whereHas('purchaseOrder', function ($query) use ($branch) {
                $query->where('wa_location_and_store_id', $branch);
            })->whereDoesntHave('invoice')->get();

        $totalValue = 0;
        foreach ($grns as $grn) {
            $info = json_decode($grn->invoice_info);
            $totalValue += floatval($info->qty) * floatval($info->order_price) - floatval($info->total_discount ?? 0);
        }

        return $totalValue;
    }

    public function stockValue($branch = null)
    {
        $value = 0;

        foreach ($this->products as $product) {
            $value += $product->selling_price * $product->itemQuantity($branch);
        }

        return $value;
    }

    public function getAllTrans()
    {
        return $this->hasMany('App\Model\WaSuppTran', 'supplier_no', 'supplier_code');
    }

    public function balance()
    {
        return $this->suppTrans()->sum('total_amount_inc_vat');
    }

    public function suppTrans()
    {
        return $this->hasMany(WaSuppTran::class, 'supplier_no', 'supplier_code');
    }

    public function canBeDeleted()
    {
        return !$this->grns()->exists() && !$this->suppTrans()->exists();
    }

    public function getCAmountAttribute()
    {
        $atasdate = Request::get('start-date');
        // echo $atasdate;
        $amount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', $atasdate);
        }
        $amount = $amount->sum('total_amount_inc_vat');

        $alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', $atasdate);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $journel_entry_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_id->whereDate('trans_date',  $atasdate);
        }
        $journel_entry_id = $journel_entry_id->sum('total_amount_inc_vat');

        $journel_entry_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_alocatedAmount->whereDate('trans_date', $atasdate);
        }
        $journel_entry_alocatedAmount = $journel_entry_alocatedAmount->sum('allocated_amount');

        $bill_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_id->whereDate('trans_date',  $atasdate);
        }
        $bill_id = $bill_id->sum('total_amount_inc_vat');

        $bill_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_alocatedAmount->whereDate('trans_date', $atasdate);
        }
        $bill_alocatedAmount = $bill_alocatedAmount->sum('allocated_amount');

        $data = ($amount - $alocatedAmount) + ($journel_entry_id - $journel_entry_alocatedAmount) + ($bill_id - $bill_alocatedAmount);
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'supplier_no')->orderBy('trans_date', 'desc');
    }

    public function getMonth1AmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));
        // echo $atasdate . ' = ' . $next_month . "<br>";
        $amount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $atasdate);
            $amount->whereDate('trans_date', '>=', $next_month);
        }
        $amount = $amount->sum('total_amount_inc_vat');

        $alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $journel_entry_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_id->whereDate('trans_date', '<', $atasdate);
            $journel_entry_id->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_id = $journel_entry_id->sum('total_amount_inc_vat');

        $bill_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_id->whereDate('trans_date', '<', $atasdate);
            $bill_id->whereDate('trans_date', '>=', $next_month);
        }
        $bill_id = $bill_id->sum('total_amount_inc_vat');


        $journel_entry_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $journel_entry_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_alocatedAmount = $journel_entry_alocatedAmount->sum('allocated_amount');

        $bill_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $bill_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $bill_alocatedAmount = $bill_alocatedAmount->sum('allocated_amount');


        $data = ($amount - $alocatedAmount) + ($journel_entry_id - $journel_entry_alocatedAmount) + ($bill_id - $bill_alocatedAmount);

        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'supplier_no')->orderBy('trans_date', 'desc');
    }

    public function getMonth2AmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));
        //echo $atasdate . ' = ' . $next_month."<br>";
        $amount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $atasdate);
            $amount->whereDate('trans_date', '>=', $next_month);
        }
        $amount = $amount->sum('total_amount_inc_vat');

        $alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $journel_entry_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_id->whereDate('trans_date', '<', $atasdate);
            $journel_entry_id->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_id = $journel_entry_id->sum('total_amount_inc_vat');

        $bill_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_id->whereDate('trans_date', '<', $atasdate);
            $bill_id->whereDate('trans_date', '>=', $next_month);
        }
        $bill_id = $bill_id->sum('total_amount_inc_vat');

        $journel_entry_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $journel_entry_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_alocatedAmount = $journel_entry_alocatedAmount->sum('allocated_amount');

        $bill_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $bill_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $bill_alocatedAmount = $bill_alocatedAmount->sum('allocated_amount');

        $data = ($amount - $alocatedAmount) + ($journel_entry_id - $journel_entry_alocatedAmount) + ($bill_id - $bill_alocatedAmount);
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'supplier_no')->orderBy('trans_date', 'desc');
    }

    public function getMonth3AmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));
        // echo $atasdate.' = '.$next_month;
        $amount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $atasdate);
            $amount->whereDate('trans_date', '>=', $next_month);
        }
        $amount = $amount->sum('total_amount_inc_vat');

        $alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $journel_entry_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_id->whereDate('trans_date', '<', $atasdate);
            $journel_entry_id->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_id = $journel_entry_id->sum('total_amount_inc_vat');

        $bill_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_id->whereDate('trans_date', '<', $atasdate);
            $bill_id->whereDate('trans_date', '>=', $next_month);
        }
        $bill_id = $bill_id->sum('total_amount_inc_vat');

        $journel_entry_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $journel_entry_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_alocatedAmount = $journel_entry_alocatedAmount->sum('allocated_amount');

        $bill_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $bill_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $bill_alocatedAmount = $bill_alocatedAmount->sum('allocated_amount');

        $data = ($amount - $alocatedAmount) + ($journel_entry_id - $journel_entry_alocatedAmount) + ($bill_id - $bill_alocatedAmount);
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'supplier_no')->orderBy('trans_date', 'desc');
    }
    public function getMonthLastAmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));

        $amount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $next_month);
        }
        $amount = $amount->sum('total_amount_inc_vat');

        $alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');
        $journel_entry_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_id->whereDate('trans_date', '<', $atasdate);
            $journel_entry_id->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_id = $journel_entry_id->sum('total_amount_inc_vat');

        $bill_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_id->whereDate('trans_date', '<', $atasdate);
            $bill_id->whereDate('trans_date', '>=', $next_month);
        }
        $bill_id = $bill_id->sum('total_amount_inc_vat');

        $journel_entry_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        if (!empty($atasdate)) {
            $journel_entry_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $journel_entry_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $journel_entry_alocatedAmount = $journel_entry_alocatedAmount->sum('allocated_amount');

        $bill_alocatedAmount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        if (!empty($atasdate)) {
            $bill_alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $bill_alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $bill_alocatedAmount = $bill_alocatedAmount->sum('allocated_amount');

        $data = ($amount - $alocatedAmount) + ($journel_entry_id - $journel_entry_alocatedAmount) + ($bill_id - $bill_alocatedAmount);
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'supplier_no')->orderBy('trans_date', 'desc');
    }

    public function getTotalAmountAttribute()
    {
        $atasdate = Request::get('start-date');
        // echo $atasdate;
        $amount = WaSuppTran::where('supplier_no', $this->supplier_code)->where('wa_purchase_order_id', '!=', Null);
        $amount = $amount->sum('total_amount_inc_vat');
        $journel_entry_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('journel_entry_id', '!=', Null);
        $journel_entry_id = $journel_entry_id->sum('total_amount_inc_vat');

        $bill_id = WaSuppTran::where('supplier_no', $this->supplier_code)->where('bill_id', '!=', Null);
        $bill_id = $bill_id->sum('total_amount_inc_vat');

        return $amount + $journel_entry_id + $bill_id;
        // return $this->hasMany('App\Model\WaDebtorTran', 'supplier_no')->orderBy('trans_date', 'desc');
    }

    public function scopeProcurementRole(Builder $query, $user)
    {
        // Check for HQ Procurement role using hardcoded role_id
        return $query->when($user->role_id == 154, function ($query) use ($user) {
            $query->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        });
    }

    public function trage_agreement()
    {
        return $this->hasOne(\App\Models\TradeAgreement::class, 'wa_supplier_id');
    }

    public function locked_trade()
    {
        return $this->trage_agreement()->where('is_locked', 1);
    }

    public function getUnpaidVouchers()
    {
        return PaymentVoucher::where('wa_supplier_id', $this->id)->where('status', '!=', 2)->sum('amount');
    }
}
