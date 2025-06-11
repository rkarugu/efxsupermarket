<?php

namespace App\Model;

use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaNumerSeriesCode extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $guarded = [];    

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'module'
            ]
        ];
    }

    public static function getNumberSeriesTypeList()
    {
        $list = WaNumerSeriesCode::pluck('description', 'type_number')->toArray();
        return $list;
    }
}
