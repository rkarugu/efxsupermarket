<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class BeerKegCategory extends Model
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
        return $this->hasOne('App\Model\BeerAndKegCategoryRelation', 'beer_and_keg_category_id');
    }

     public function getManyRelativeData() {
        return $this->hasMany('App\Model\BeerAndKegCategoryRelation', 'beer_and_keg_category_id');
    }

     public function getManyRelativeChilds() {
        return $this->hasMany('App\Model\BeerAndKegCategoryRelation', 'parent_id');
    }
    /*

   

   

    public function getManyRelativeTaxes() 
    {
        return $this->hasMany('App\Model\CategoryAndFoodItemTaxManager', 'category_id');
    }*/

    

    
}
