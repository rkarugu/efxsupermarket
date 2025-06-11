<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CategoryAndFoodItemTaxManager extends Model
{
    protected $fillable = array('food_item_id', 'category_id', 'tax_manager_id');

     public function getRelativeTaxdetail() {
        return $this->belongsTo('App\Model\TaxManager', 'tax_manager_id');
    }

    
}


