<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class Reservation extends Model
{
    
   
    public function getAssociateRestro() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

     public function getAssociateUser() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
  
}


