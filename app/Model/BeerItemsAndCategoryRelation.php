<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class BeerItemsAndCategoryRelation extends Model
{
    
    protected $fillable = array('beer_keg_category_id', 'beer_delivery_item_id');
   /* public function getRelativecategoryDetail() {
        return $this->belongsTo('App\Model\Category', 'category_id');
    }

     */
    public function getRelativeitemDetail() {
        return $this->belongsTo('App\Model\BeerDeliveryItem', 'beer_delivery_item_id');
    }
     
}
