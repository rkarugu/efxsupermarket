<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class AwayTakeHit extends Model
{
    
     public function getAssociateUser() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}


