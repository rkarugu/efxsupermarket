<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaCompanyPreference extends Model
{
    use Sluggable;
    
    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'name',
            'onUpdate' => true
        ]];
    }

    public function creditorControlGlAccount()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'creditors_control_gl_account');
    }

    public function debtorsControlGlAccount()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'debtors_control_gl_account');
    }

    public function discountReceivedGlAccount()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'discount_recieved_gl_account');
    }

    public function withholdingVatGlAccount()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'withholding_vat_gl_account');
    }

    public function good_receive()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'goods_received_clearing_gl_account');
    }

    public function vat()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'vat_control_account');
    }

    public function cash_sales()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'cash_sales_control_account');
    }

    public function sales()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'sales_control_account');
    }
}
