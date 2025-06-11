<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    //
    use Sluggable;
     public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name',
            'onUpdate'=>false
        ]];
    }

    

    public function getRelativeData() {
        return $this->hasOne('App\Model\CategoryRelation', 'category_id');
    }

    public function getManyRelativeData() {
        return $this->hasMany('App\Model\CategoryRelation', 'category_id');
    }

    public function getManyRelativeChilds() {
        return $this->hasMany('App\Model\CategoryRelation', 'parent_id');
    }

    public function getManyRelativeTaxes() 
    {
        return $this->hasMany('App\Model\CategoryAndFoodItemTaxManager', 'category_id');
    }

    public static function getMajorGroupslist(){
        $lists = Category::whereLevel(0)->orderBy('display_order', 'asc')->pluck('name', 'id')->toArray();
        return $lists;
    } 

    
}
