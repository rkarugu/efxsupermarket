<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Meterhistory extends Model
{    
    public $table = "meterhistory";
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = self::select(['*']);
        
        $query = Meterhistory::select('meterhistory.*','vehicle_list.license_plate as vehicle_list_license_plate')
        ->leftjoin('vehicle_list','meterhistory.vehicle','=','vehicle_list.id');



        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
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


    public static function getDataVehicles($limit, $offset, $search, $orderby,$order,$request,$vehicle_id)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = self::select(['*']);
        
        $query = Meterhistory::select('meterhistory.*','vehicle_list.license_plate as vehicle_list_license_plate')
        ->leftjoin('vehicle_list','meterhistory.vehicle','=','vehicle_list.id');



        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->whereBetween('created_at',$date);
            });
        }

        $query->where('meterhistory.vehicle',$vehicle_id);
        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();
        return ['response'=>$response,'count'=>$count];
    }
}
