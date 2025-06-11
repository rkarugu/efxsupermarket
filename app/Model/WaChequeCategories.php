<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaChequeCategories extends Model
{
    protected $guarded = [];
    public function tax_manager()
    {
        return $this->belongsTo(TaxManager::class,'tax_manager_id');
    }
    public function category()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'category_id');
    }
}
