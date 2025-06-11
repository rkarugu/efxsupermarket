<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class FoodItemsPrintClassRelation extends Model
{
    protected $fillable = array('print_class_id', 'food_item_id');

    public function getAssociatePrintClass() {
        return $this->belongsTo('App\Model\PrintClass', 'print_class_id');
    }
}


