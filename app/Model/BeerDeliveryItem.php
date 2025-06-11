<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class BeerDeliveryItem extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name',
            'onUpdate'=>true
        ]];
    }

    public function getItemCategoryRelation() {
        return $this->hasOne('App\Model\BeerItemsAndCategoryRelation', 'beer_delivery_item_id');
    }

    public function getManyRelativeTaxes() 
    {
        return $this->hasMany('App\Model\BeerItemTaxManager', 'beer_delivery_item_id');
    }

    /*public function familyGroupdetail() {
        return $this->belongsTo('App\Model\FamilyGroup', 'family_group_id');
    }

    

    public function getManyRelativeCondimentsGroup() 
    {
        return $this->hasMany('App\Model\ItemCondimentGroupRelation', 'food_item_id');
    }

     public function getManyRelativePrintClasses() 
    {
        return $this->hasMany('App\Model\FoodItemsPrintClassRelation', 'food_item_id');
    }

    */
     
}


