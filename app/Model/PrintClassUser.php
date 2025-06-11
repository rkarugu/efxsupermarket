<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class PrintClassUser extends Model
{
    //
    use Sluggable;
     public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name',
            'onUpdate'=>true
        ]];
    }

    public function printClassUserRestaurent() 
    {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

    public function printClassUserPrintClass() 
    {
        return $this->belongsTo('App\Model\PrintClass', 'print_class_id');
    }
}
