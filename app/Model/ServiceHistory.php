<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class ServiceHistory extends Model
{
    public $table = "servicehistory";

    public static function getData($limit, $offset, $search, $orderby,$order,$request){
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        // $query = self::select(['*']
        $query = ServiceHistory::select('servicehistory.*','vehicle_list.license_plate as vehicle_list_license_plate','wa_suppliers.name as wa_suppliers_name','issues_types.issues as issues_types_issues')
        ->leftjoin('vehicle_list','servicehistory.vehicle','=','vehicle_list.id')
        ->leftjoin('wa_suppliers','servicehistory.vendor','=','wa_suppliers.id')
        ->leftjoin('issues_types','issues_types.issues','=','issues_types.id');
         

        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle','LIKE',"%$search%");
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


    public static function getDataVehicles($limit, $offset, $search, $orderby,$order,$request,$vehicle_id){
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        // $query = self::select(['*']
        $query = ServiceHistory::select('servicehistory.*','vehicle_list.license_plate as vehicle_list_license_plate','wa_suppliers.name as wa_suppliers_name','issues_types.issues as issues_types_issues')
        ->leftjoin('vehicle_list','servicehistory.vehicle','=','vehicle_list.id')
        ->leftjoin('wa_suppliers','servicehistory.vendor','=','wa_suppliers.id')
        ->leftjoin('issues_types','issues_types.issues','=','issues_types.id');
         

        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to){
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->whereBetween('created_at',$date);
            });
        }

        $query->where('vehicle',$vehicle_id);

        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();
        return ['response'=>$response,'count'=>$count];
    }


    //  public function IssuesTypes()
    // {
    //     return $this->hasMany("App\Model\IssuesType", 'issues','id');
      
    // }

     public function IssuesTypes()
    {
        return $this->hasOne("App\Model\IssuesType", 'id','issues');
      
    }


    public function Issues()

    {

        return $this->hasMany("App\Model\IssuesType",'servicehistory_id','id');

    }


     public function servicetask()

    {

        return $this->hasMany("App\Model\ServiceIssues",'servicehistory_id','id');

    }


     public function LicensePlate()
    {
        return $this->hasOne("App\Model\Vehicle", 'id','vehicle');
      
    } 


    
}