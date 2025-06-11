<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Restaurant extends Model
{
    //
    use Sluggable;
     public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name'
        ]];
    }

     public function getAssociateCompany() {
        return $this->belongsTo('App\Model\WaCompanyPreference', 'wa_company_preference_id');
    }
    public function storeLocation() {
        
        return $this->hasMany(WaLocationAndStore::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(WaLocationAndStore::class, 'wa_branch_id', 'id');
    }

}
