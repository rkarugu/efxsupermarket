<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaRecipeIngredient extends Model
{
    
    protected $fillable = ['wa_inventory_item_id'];
    
    public function getAssociateItemDetail() {
        return $this->belongsTo('App\Model\WaInventoryItem', 'wa_inventory_item_id');
    }

}


