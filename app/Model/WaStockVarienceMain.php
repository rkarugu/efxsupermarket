<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaStockVarienceMain extends Model
{
    protected $guarded = [];

    public $table = 'wa_stock_variance_main';
    
    public function items()
    {
        return $this->hasMany(WaStockVarience::class,'parent_id');
    }
}
