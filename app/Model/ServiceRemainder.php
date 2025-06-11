<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class ServiceRemainder extends Model{ 
    
   
    protected $appends=['main_next_due_date'];
    
    public static function getData($limit, $offset, $search, $orderby,$order,$request){
       
        $orderby  = $orderby ? $orderby : 'service_remainders.id';
        $order    = $order ? $order : 'desc';
        $query = self::select(['service_remainders.*','vehicle_list.license_plate as vehicle_license_plate','servicetask.name as service_task_name'])->with(['vehicle','service_task'])->where('service_remainders.is_archived','0');
        
        $query->leftjoin('vehicle_list','service_remainders.vehicle_id','=','vehicle_list.id');
        $query->leftJoin('servicetask', 'service_remainders.service_task_id','=', 'servicetask.id');
        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle_list.vin_sn','LIKE',"%$search%");
                $q->orWhere('vehicle_list.license_plate','LIKE',"%$search%");
                $q->orWhere('servicetask.name','LIKE',"%$search%");
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
        $orderby  = $orderby ? $orderby : 'service_remainders.id';
        $order    = $order ? $order : 'desc';
        $query = self::select(['service_remainders.*','vehicle_list.license_plate as vehicle_license_plate','servicetask.name as service_task_name'])->with(['vehicle','service_task'])->where('service_remainders.is_archived','0');
        
        $query->leftjoin('vehicle_list','service_remainders.vehicle_id','=','vehicle_list.id');
        $query->leftJoin('servicetask', 'service_remainders.service_task_id','=', 'servicetask.id');
        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle_list.vin_sn','LIKE',"%$search%");
                $q->orWhere('vehicle_list.license_plate','LIKE',"%$search%");
                $q->orWhere('servicetask.name','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to){
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->whereBetween('created_at',$date);
            });
        }

        $query->where('service_remainders.vehicle_id',$vehicle_id);

        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();

        
                       
        return ['response'=>$response,'count'=>$count];
    }




    
    public function getMainNextDueDateAttribute(){
        return ($this->next_due_date)?date('j, M d, Y H:ia',strtotime($this->next_due_date)):'-';
    }

    public function vehicle() {
        return $this->belongsTo('App\Model\Vehicle', 'vehicle_id');
    }

    public function service_task() {
        return $this->belongsTo('App\Model\ServiceTask', 'service_task_id');
    }

   

     
}


