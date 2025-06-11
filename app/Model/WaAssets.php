<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaAssets extends Model
{
    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo(WaAssetLocation::class,'wa_asset_location_id');
    }

    public static function getDataModel($limit , $start , $search, $orderby, $order)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'wa_assets.id';
        $query = WaAssets::query();
        if($search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('asset_description_short','LIKE','%'.$search.'%');
                $q->orWhere('asset_description_long','LIKE','%'.$search.'%');
                $q->orWhere('bar_code','LIKE','%'.$search.'%');
                $q->orWhere('serial_number','LIKE','%'.$search.'%');
            });
        }
        $count = $query->count('id');
        $query = $query->orderBy($orderby,$order)->limit($limit)->offset($start)->get();
        return ['count'=>$count,'response'=>$query];
    }
}
