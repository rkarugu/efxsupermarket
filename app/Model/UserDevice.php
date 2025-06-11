<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class UserDevice extends Model
{    
    protected $fillable = array('user_id', 'device_id','device_type');
}
