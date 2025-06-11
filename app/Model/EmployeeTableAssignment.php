<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class EmployeeTableAssignment extends Model
{
    
    protected $fillable = array('user_id', 'table_manager_id');

     public function tableraletaedwaiter() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

     
}
