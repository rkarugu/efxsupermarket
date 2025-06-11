<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaBranch extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'branch_name',
            'onUpdate' => true
        ]];
    }
}
