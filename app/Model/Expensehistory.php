<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class Expensehistory extends Model
{
 public $table = "expensehistory";
    
    public static function getData($limit, $offset, $search, $orderby,$order,$request){
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $where = '';
        if( $search){
            $where = 'having (license_plate LIKE "%'.$search.'%")';
            // $query = $query->where(function($q) use ($search){
            //     $q->orWhere('vehicle','LIKE',"%$search%");
            // });
        }

        if($request->from && $request->to){
            if( $search){
                $where .= ' AND';
            }else{
                $where .= 'having ';
            }
            $where .= '(dated between "'.$request->from.' 00:00:00" AND "'.$request->to.' 23:59:59")';
        }

        $query= DB::SELECT(DB::RAW("SELECT v.vehicle,v.type,v.id, vehicle_list.license_plate as license_plate, v.vendor,wa_suppliers.name as vendor_name, v.expense_type,expensetype.title,v.dated,v.amount FROM (SELECT vehicle,'fuel' as type, id, fuel_entry_date as dated,vendor_name as vendor,0 as expense_type,price as amount FROM `fuelentry` UNION ALL SELECT vehicle,'meter' as type, id, date as dated, 0 as vendor, 0 as expense_type, 0 as amount FROM `meterhistory`UNION ALL SELECT vehicle,'type' as type, id,date as dated,vendor,expense_type,amount FROM `expensehistory`  ) v LEFT JOIN vehicle_list ON vehicle_list.id = v.vehicle LEFT JOIN wa_suppliers ON wa_suppliers.id = v.vendor LEFT JOIN expensetype ON expensetype.id = v.expense_type ".$where." order BY ".$orderby." ".$order.""));

        
       
        $count = count($query);
        $response   =   $query;

        return ['response'=>$response,'count'=>$count];
    }


    public static function getDataVehicles($limit, $offset, $search, $orderby,$order,$request,$vehicle_id){
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $where = '';
        if( $search){
            $where = 'having (license_plate LIKE "%'.$search.'%")';
            // $query = $query->where(function($q) use ($search){
            //     $q->orWhere('vehicle','LIKE',"%$search%");
            // });
        }

        if($request->from && $request->to){
            if( $search){
                $where .= ' AND';
            }else{
                $where .= 'having ';
            }
            $where .= '(dated between "'.$request->from.' 00:00:00" AND "'.$request->to.' 23:59:59")';
        }

        
        //echo $where; die;

        $query= DB::SELECT(DB::RAW("SELECT v.vehicle,v.type,v.id, vehicle_list.license_plate as license_plate, v.vendor,wa_suppliers.name as vendor_name, v.expense_type,expensetype.title,v.dated,v.amount FROM (SELECT vehicle,'fuel' as type, id, fuel_entry_date as dated,vendor_name as vendor,0 as expense_type,price as amount FROM `fuelentry` UNION ALL SELECT vehicle,'meter' as type, id, date as dated, 0 as vendor, 0 as expense_type, 0 as amount FROM `meterhistory`UNION ALL SELECT vehicle,'type' as type, id,date as dated,vendor,expense_type,amount FROM `expensehistory`  ) v LEFT JOIN vehicle_list ON vehicle_list.id = v.vehicle LEFT JOIN wa_suppliers ON wa_suppliers.id = v.vendor LEFT JOIN expensetype ON expensetype.id = v.expense_type WHERE v.vehicle =  ".$vehicle_id."  ".$where." order BY ".$orderby." ".$order.""));

        
       
        $count = count($query);
        $response   =   $query;

        return ['response'=>$response,'count'=>$count];
    }


    public function LicensePlate(){
        return $this->hasOne("App\Model\Vehicle", 'id','vehicle');
      
    } 

    public function Type()
    {
        return $this->hasOne("App\Model\Expensetype", 'id','expense_type');
      
    }

    public function VendorName()
    {
        return $this->hasOne("App\Model\WaSupplier", 'id','vendor');
      
    } 
}