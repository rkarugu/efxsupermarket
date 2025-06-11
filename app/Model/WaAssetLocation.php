<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaAssetLocation extends Model
{
    protected $guarded = [];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public static function getDataModel($limit , $start , $search, $orderby, $order)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'wa_asset_locations.id';
        $query = WaAssetLocation::with('branch');
        if($search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('location_ID','LIKE','%'.$search.'%');
                $q->orWhere('location_description','LIKE','%'.$search.'%');
            });
        }
        $count = $query->count('id');
        $query = $query->orderBy($orderby,$order)->limit($limit)->offset($start)->get();
        return ['count'=>$count,'response'=>$query];
    }
}
