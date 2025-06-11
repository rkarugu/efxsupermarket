<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemSubCategories extends Model
{
    protected $table = 'wa_item_sub_categories';

    protected $guarded = [];
    public function category_relation(){
        return $this->hasMany(WaInventoryCategorySubCategory::class, 'sub_category_id');
    }
}


