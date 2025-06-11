<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class BeerAndKegCategoryRelation extends Model
{
    //
     protected $fillable = array('beer_and_keg_category_id', 'parent_id');

     public function getRelativeParentCategoryData() {
        return $this->hasOne('App\Model\BeerAndKegCategoryRelation', 'beer_and_keg_category_id');
    }

    public function getRelativeCategorysData() {
        return $this->belongsTo('App\Model\BeerKegCategory', 'beer_and_keg_category_id');
    }
  
}
