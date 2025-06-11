<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class TaxManager extends Model
{

     public function getInputGlAccount() {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'input_tax_gl_account');
    }


     public function getOutputGlAccount() {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'output_tax_gl_account');
    }


    public function pos_items()
    {
        return $this->hasMany(WaPosCashSalesItems::class,'tax_manager_id');
    }
    public function sales_items()
    {
        return $this->hasMany(WaInventoryLocationTransfer::class,'tax_manager_id');
    }

    

     
}


