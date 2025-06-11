<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ItemCategoryRelation extends Model
{
    
    protected $fillable = array('category_id', 'item_id');
    public function getRelativecategoryDetail() {
        return $this->belongsTo('App\Model\Category', 'category_id');
    }

     public function getRelativeitemDetail() {
        return $this->belongsTo('App\Model\FoodItem', 'item_id');
    }
     
}
