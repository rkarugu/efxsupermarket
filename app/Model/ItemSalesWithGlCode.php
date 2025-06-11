<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ItemSalesWithGlCode extends Model
{
    
      public function getRelatedCategory() 
    {
        return $this->belongsTo('App\Model\Category', 'family_group_id');
    }

     public function getGlDetail() 
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'gl_code_id');
    }
}


