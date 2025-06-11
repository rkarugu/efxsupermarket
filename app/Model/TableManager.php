<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class TableManager extends Model
{
    //
    use Sluggable;
     public function sluggable(): array {
        return ['slug'=>[
            'source'=>'name'
        ]];
    }


    

    public function tableRestaurent() {
        return $this->belongsTo('App\Model\Restaurant', 'restaurant_id');
    }

      public function tableAssignmentForrelatedTable() {
        return $this->hasOne('App\Model\EmployeeTableAssignment', 'table_manager_id');
    }

    


    
}
