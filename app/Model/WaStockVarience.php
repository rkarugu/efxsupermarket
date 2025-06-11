<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaStockVarience extends Model
{
    protected $guarded = [];

    public $table = 'wa_stock_variance';
    
    public function items()
    {
        return $this->hasMany(WaStockVarienceItem::class,'parent_id','id');
    }
    public function parent()
    {
        return $this->belongsTo(WaStockVarienceMain::class,'parent_id');
    }
}
