<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ItemCondimentGroupRelation extends Model
{
    
     protected $fillable = array('condiment_group_id', 'food_item_id');

    public function getRelativeccondimentgroupDetail() {
        return $this->belongsTo('App\Model\CondimentGroup', 'condiment_group_id');
    }

    public function getRelativefooditemdetailDetail() {
        return $this->belongsTo('App\Model\FoodItem', 'food_item_id');
    }
   
}


