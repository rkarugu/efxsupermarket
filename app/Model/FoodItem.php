<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class FoodItem extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name',
            'onUpdate'=>true
        ]];
    }

    public function familyGroupdetail() {
        return $this->belongsTo('App\Model\FamilyGroup', 'family_group_id');
    }

    public function getItemCategoryRelation() {
        return $this->hasOne('App\Model\ItemCategoryRelation', 'item_id');
    }

    public function getManyRelativeCondimentsGroup() 
    {
        return $this->hasMany('App\Model\ItemCondimentGroupRelation', 'food_item_id');
    }

     public function getManyRelativePrintClasses() 
    {
        return $this->hasMany('App\Model\FoodItemsPrintClassRelation', 'food_item_id');
    }
    public function getClassName() 
    {
        return $this->hasMany('App\Model\FoodItemsPrintClassRelation', 'food_item_id')->with('getAssociatePrintClass');
    }
    public function getManyRelativeTaxes() 
    {
        return $this->hasMany('App\Model\CategoryAndFoodItemTaxManager', 'food_item_id');
    }
    
    public function getAssociateRecipe() 
    {
        return $this->belongsTo('App\Model\WaRecipe', 'wa_recipe_id');
    }
     
}


