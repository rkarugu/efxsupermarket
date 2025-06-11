<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaExpenseCategories extends Model
{
    protected $guarded = [];
    public function expense()
    {
        return $this->belongsTo(WaExpenses::class,'expense_id');
    }
    public function tax_manager()
    {
        return $this->belongsTo(TaxManager::class,'tax_manager_id');
    }
    public function category()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'category_id');
    }

    public function project()
    {
        return $this->belongsTo(Projects::class,'project_id');
    }
    
    public function gltag()
    {
        return $this->belongsTo(GlTags::class,'gltag_id');
    }
}
