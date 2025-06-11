<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaNonStockInventoryItems extends Model
{
    protected $guarded = [];

    public function gl_code()
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'gl_code_id');
    }
    public function getTaxesOfItem() {
        return $this->belongsTo('App\Model\TaxManager', 'vat_id');
    }
}
