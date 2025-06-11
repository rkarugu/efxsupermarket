<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class CategoryRelation extends Model
{
    //
     protected $fillable = array('category_id', 'parent_id');

     public function getRelativeParentCategoryData() {
        return $this->hasOne('App\Model\CategoryRelation', 'category_id');
    }

    public function getRelativeCategorysData() {
        return $this->belongsTo('App\Model\Category', 'category_id');
    }
  
}
