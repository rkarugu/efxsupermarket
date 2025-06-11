<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;
use DB;

class TyreFitting extends Model
{
    
   



    public function getAllFromStockMoves() {
        return $this->hasMany('App\Model\WaStockMove', 'stock_id_code','stock_id_code');
    }

    
    //getTaxesOfItem

}
