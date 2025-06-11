<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\BaseModel;

class WaCustomer extends BaseModel
{
    use Sluggable;

    protected $guarded = [];

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'customer_name',
            'onUpdate' => true
        ]];
    }

    public function deliveryCenter()
    {
        return $this->belongsTo(DeliveryCentres::class, 'delivery_centres_id');
    }

    public function internalrequisitions(): HasMany
    {
        return $this->hasMany(WaInternalRequisition::class, 'customer_id', 'id');
    }

    public function routeCustomers()
    {
        return $this->hasMany(WaRouteCustomer::class);
    }

   // protected $appends = ['c_amount_3', 'month_1_amount', 'month_2_amount', 'month_3_amount', 'month_last_amount'];

    public function getAssociatedPayemntterm()
    {
        return $this->belongsTo('App\Model\WaPaymentTerm', 'payment_term_id');
    }

    public function getAllDebtorsTrans()
    {
        return $this->hasMany('App\Model\WaDebtorTran', 'wa_customer_id')->orderBy('trans_date', 'desc');
    }

    public function getCAmount3Attribute()
    {
        $atasdate = Request::get('start-date');
        // echo $atasdate;
        $amount = WaDebtorTran::where('wa_customer_id', $this->id);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', $atasdate);
        }
        $amount = $amount->sum('amount');

        $alocatedAmount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', $atasdate);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $data = $amount - $alocatedAmount;
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'wa_customer_id')->orderBy('trans_date', 'desc');
    }

    public function getMonth1AmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));
        // echo $atasdate . ' = ' . $next_month . "<br>";
        $amount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $atasdate);
            $amount->whereDate('trans_date', '>=', $next_month);
        }
        $amount = $amount->sum('amount');

        $alocatedAmount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $data = $amount - $alocatedAmount;
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'wa_customer_id')->orderBy('trans_date', 'desc');
    }

    public function getMonth2AmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));
        //echo $atasdate . ' = ' . $next_month."<br>";
        $amount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $atasdate);
            $amount->whereDate('trans_date', '>=', $next_month);
        }
        $amount = $amount->sum('amount');

        $alocatedAmount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $data = $amount - $alocatedAmount;
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'wa_customer_id')->orderBy('trans_date', 'desc');
    }

    public function getMonth3AmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));
        // echo $atasdate.' = '.$next_month;
        $amount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $atasdate);
            $amount->whereDate('trans_date', '>=', $next_month);
        }
        $amount = $amount->sum('amount');

        $alocatedAmount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $atasdate);
            $alocatedAmount->whereDate('trans_date', '>=', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $data = $amount - $alocatedAmount;
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'wa_customer_id')->orderBy('trans_date', 'desc');
    }

    public function getMonthLastAmountAttribute()
    {
        $atasdate = Request::get('start-date');
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $atasdate = date("Y-m-d", strtotime("$atasdate -1 month"));
        $next_month = date("Y-m-d", strtotime("$atasdate -1 month"));

        $amount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $amount->whereDate('trans_date', '<', $next_month);
        }
        $amount = $amount->sum('amount');

        $alocatedAmount = WaDebtorTran::where('wa_customer_id', $this->id)->where('wa_sales_invoice_id', '!=', Null);
        if (!empty($atasdate)) {
            $alocatedAmount->whereDate('trans_date', '<', $next_month);
        }
        $alocatedAmount = $alocatedAmount->sum('allocated_amount');

        $data = $amount - $alocatedAmount;
        return $data;
        // return $this->hasMany('App\Model\WaDebtorTran', 'wa_customer_id')->orderBy('trans_date', 'desc');
    }

    public function getRoute()
    {
        return Route::find($this->route_id);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function associatedRouteCustomer()
    {
        return $this->hasOne(WaRouteCustomer::class, 'credit_customer_id');
    }
}
