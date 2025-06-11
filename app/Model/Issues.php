<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class Issues extends Model{
    public $table = "issues";
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        // $query = self::select(['*']
        $query = Issues::select('issues.*','vehicle_list.license_plate as vehicle_list_license_plate',
            'users.name as user_name')
        ->leftjoin('vehicle_list','issues.asset','=','vehicle_list.id')
        ->leftjoin('users','issues.assigned','=','users.id');
        ;
         

        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('asset','LIKE',"%$search%");
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
        $query = Issues::select('issues.*','vehicle_list.license_plate as vehicle_list_license_plate',
            'users.name as user_name')
        ->leftjoin('vehicle_list','issues.asset','=','vehicle_list.id')
        ->leftjoin('users','issues.assigned','=','users.id');
        ;
         

        if($search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('asset','LIKE',"%$search%");
            });
        }

        if($request->from && $request->to){
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->whereBetween('created_at',$date);
            });
        }

        $query->where('issues.asset',$vehicle_id);
        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();
        return ['response'=>$response,'count'=>$count];
    }


     public function User()
    {
        return $this->hasOne("App\Model\User", 'id','assigned');
      
    }

     public function Vehicle()
    {
        return $this->hasOne("App\Model\Vehicle", 'id','asset');
      
    }
}