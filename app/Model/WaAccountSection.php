<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaAccountSection extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'section_name',
            'onUpdate'=>true
        ]];
    }


    public function getWaAccountGroup()
    {
        return $this->hasMany('App\Model\WaAccountGroup', 'wa_account_section_id');
    }



     
}


