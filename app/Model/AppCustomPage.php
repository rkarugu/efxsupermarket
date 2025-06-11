<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class AppCustomPage extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'title',
            'onUpdate'=>true
        ]];
    }

    

    

     
}


