<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaPoiStockSerialMovesHistory extends Model
{
    protected $guarded = [];
    protected $table = 'wa_poi_stock_serial_moves_history';
    
    public function stock_move(){
        return $this->belongsTo(WaStockMove::class,'wa_stock_move_id');
    }

    public function vehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function tyre_position(){
        return $this->belongsTo(TyrePosition::class,'tyre_position_id');
    }


   

    

    public static function getDataModel($limit , $start , $search, $orderby, $order)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'id';
        $query = self::with('vehicle','user','tyre_position')->query();
        if($search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('serial_number','LIKE','%'.$search.'%');
            });
        }
        $count = $query->count('id');
        $query = $query->orderBy($orderby,$order)->limit($limit)->offset($start)->get();
        return ['count'=>$count,'response'=>$query];
    }
}
