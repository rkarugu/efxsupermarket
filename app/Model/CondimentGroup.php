<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class CondimentGroup extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'title',
            'onUpdate'=>true
        ]];
    }

    public function getManyRelativeCondiments() {
        return $this->hasMany('App\Model\CondimentGroupRelation', 'condiment_group_id');
    }

    

    

     
}


