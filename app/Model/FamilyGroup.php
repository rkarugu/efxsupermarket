<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class FamilyGroup extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name',
            'onUpdate'=>true
        ]];
    }

     public function tableMenuItemGroupDetail() {
        return $this->belongsTo('App\Model\MenuItemGroup', 'menu_item_group_id');
    }

    

     
}


