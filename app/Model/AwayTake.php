<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class AwayTake extends Model
{
    
    public function getAssociateRestro() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }
     public function getHits() {
        return $this->hasMany('App\Model\AwayTakeHit', 'away_take_id');
    }
  
}


