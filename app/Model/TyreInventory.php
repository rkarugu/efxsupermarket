<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;
use DB;

class TyreInventory extends Model
{
    
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {

        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        
        $query=self::select(['*',
            
            DB::RAW('(select COUNT(wa_poi_stock_serial_moves.id) from wa_poi_stock_serial_moves where wa_poi_stock_serial_moves.wa_inventory_item_id=tyre_inventories.id and wa_poi_stock_serial_moves.status="new_tyre_in_stock") as new_tyre_in_stock_count'),
            
            DB::RAW('(select COUNT(wa_poi_stock_serial_moves.id) from wa_poi_stock_serial_moves where wa_poi_stock_serial_moves.wa_inventory_item_id=tyre_inventories.id and wa_poi_stock_serial_moves.status="in_motor_vehicle") as in_motor_vehicle_count'),
            
            DB::RAW('(select COUNT(wa_poi_stock_serial_moves.id) from wa_poi_stock_serial_moves where wa_poi_stock_serial_moves.wa_inventory_item_id=tyre_inventories.id and wa_poi_stock_serial_moves.status="retread_tyre_in_stock") as retread_tyre_in_stock_count'),

            DB::RAW('(select COUNT(wa_poi_stock_serial_moves.id) from wa_poi_stock_serial_moves where wa_poi_stock_serial_moves.wa_inventory_item_id=tyre_inventories.id and wa_poi_stock_serial_moves.status="tyres_in_retread") as tyres_in_retread_count'),

            DB::RAW('(select COUNT(wa_poi_stock_serial_moves.id) from wa_poi_stock_serial_moves where wa_poi_stock_serial_moves.wa_inventory_item_id=tyre_inventories.id and wa_poi_stock_serial_moves.status="damaged") as damaged_tyre_count'),

        ])->with(['getUnitOfMeausureDetail','getAllFromStockMoves','poiStockSerialMoves']);  
        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('tyre_inventories.code','LIKE',"%$search%");
                $q->orWhere('tyre_inventories.tyre_size','LIKE',"%$search%");
                $q->orWhere('tyre_inventories.tyre_make','LIKE',"%$search%");
                $q->orWhere('tyre_inventories.type','LIKE',"%$search%");
                $q->orWhere('tyre_inventories.pattern','LIKE',"%$search%");
                $q->orWhere('tyre_inventories.status','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to){
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->whereBetween('created_at',$date);
            });
        }
        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();
        
        return ['response'=>$response,'count'=>$count];
    }



    public function getAllFromStockMoves() {
        return $this->hasMany('App\Model\WaStockMove', 'stock_id_code','stock_id_code');
    }

    public function poiStockSerialMoves() {
        return $this->hasMany('App\Model\WaPoiStockSerialMoves', 'wa_inventory_item_id');
    }

    public function getUnitOfMeausureDetail(){
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'wa_unit_of_measure_id');
    }

    //getTaxesOfItem

}
