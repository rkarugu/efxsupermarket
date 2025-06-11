<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaRecipe extends Model
{
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'title',
            'onUpdate'=>true
        ]];
    }
    
    protected $fillable = ['recipe_number'];
    
    public function getUnitOfMeausureDetail() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'unit_of_mesaurement_id');
    }
    
    public function getAssociateIngredient() {
        return $this->hasMany('App\Model\WaRecipeIngredient', 'wa_recipe_id');
    }
    
    public function getAssociateLocation() {
        return $this->belongsTo('App\Model\WaLocationAndStore', 'wa_location_and_store_id');
    }
    
    public static function getRecipeList(){
        $list = WaRecipe::where('status','1')->pluck('title','id')->toArray();
        return $list;
    }
    
    public static function getRecipeListWithAmount(){
        $list = WaRecipe::where('status','1')->get()->toArray();
        return $list;
    }
}


