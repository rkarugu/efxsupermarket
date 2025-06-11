<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaAssetCategory extends Model
{
    protected $guarded = [];
    public static function getDataModel($limit , $start , $search, $orderby, $order)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'wa_asset_categories.id';
        $query = WaAssetCategory::query();
        if($search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('category_code','LIKE','%'.$search.'%');
                $q->orWhere('category_description','LIKE','%'.$search.'%');
            });
        }
        $count = $query->count('id');
        $query = $query->orderBy($orderby,$order)->limit($limit)->offset($start)->get();
        return ['count'=>$count,'response'=>$query];
    }
}
