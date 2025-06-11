<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Role extends BaseModel
{
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'title'
        ]];
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class,'role_id','id');
    }
}
