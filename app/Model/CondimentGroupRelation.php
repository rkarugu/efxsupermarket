<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class CondimentGroupRelation extends Model
{
    
     protected $fillable = array('condiment_group_id', 'condiment_id');
     public function getRelativecondimentdetail() {
        return $this->belongsTo('App\Model\Condiment', 'condiment_id');
    }
   
}


