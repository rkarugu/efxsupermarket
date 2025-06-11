<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class Fuelentry extends Model
{
    public $table = "fuelentry";
    
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        // $query = self::select(['*']
        $query = Fuelentry::select('fuelentry.*','vehicle_list.license_plate as vehicle_list_license_plate','wa_suppliers.name as wa_suppliers_name')
        ->leftjoin('vehicle_list','fuelentry.vehicle','=','vehicle_list.id')
        ->leftjoin('wa_suppliers','fuelentry.vendor_name','=','wa_suppliers.id');
        ;
         

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
        // $query = self::select(['*']
        $query = Fuelentry::select('fuelentry.*','vehicle_list.license_plate as vehicle_list_license_plate','wa_suppliers.name as wa_suppliers_name')
        ->leftjoin('vehicle_list','fuelentry.vehicle','=','vehicle_list.id')
        ->leftjoin('wa_suppliers','fuelentry.vendor_name','=','wa_suppliers.id');
        ;
        
        $query->where('fuelentry.vehicle',$vehicle_id); 

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


    public function ServiceCost()
    {
        return $this->hasOne("App\Model\ServiceHistory", 'id','total');
      
    } 

}