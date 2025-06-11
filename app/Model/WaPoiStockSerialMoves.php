<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaPoiStockSerialMoves extends Model
{
    protected $guarded = [];
    public function inventoryItem()
    {
        return $this->belongsTo(WaInventoryItem::class,'wa_inventory_item_id');
    }
    
    public function stock_move()
    {
        return $this->belongsTo(WaStockMove::class,'wa_stock_move_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }

    public function tyre_position(){
        return $this->belongsTo(TyrePosition::class,'tyre_position_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function new_serial()
    {
        return $this->hasOne(WaPoiStockSerialMoves::class,'serial_no','serial_no')->orderBy('created_at','DESC');
    }

    public static function getDataModel($limit , $start , $search, $orderby, $order)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'wa_assets.id';
        $query = WaAssets::with('vehicle','user','tyre_position')->query();
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
