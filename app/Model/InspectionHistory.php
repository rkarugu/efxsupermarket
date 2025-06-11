<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
class InspectionHistory extends Model
{
    
   
    protected $table = "inspection_history";
    
    

    public static function getData($limit, $offset, $search, $orderby,$order,$request){
        $orderby  = $orderby ? $orderby : 'inspection_history.id';
        $order    = $order ? $order : 'desc';
        $query = self::select('inspection_history.*')->with(['vehicle','form','user'])->where('inspection_history.status','1');
        
        $query->leftjoin('vehicle_list','inspection_history.vehicle_id','=','vehicle_list.id');
        $query->leftJoin('inspection_forms', 'inspection_history.inspection_form_id','=', 'inspection_forms.id');
        
        
        // $query = self::select(['*','model.title as model_title'])
        // ->leftJoin('types.title as type_title', 'makes.title as make_title', 'models.title as model_title', 'bodytypes.title as bodyype_title')
        // ->get();
        // ->join('users', 'users.id', '=', 'exams.user_id')
        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle_list.vin_sn','LIKE',"%$search%");
                $q->orWhere('inspection_forms.title','LIKE',"%$search%");
                $q->orWhere('inspection_forms.description','LIKE',"%$search%");
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
        $orderby  = $orderby ? $orderby : 'inspection_history.id';
        $order    = $order ? $order : 'desc';
        $query = self::select('inspection_history.*')->with(['vehicle','form','user'])->where('inspection_history.status','1');
        
        $query->leftjoin('vehicle_list','inspection_history.vehicle_id','=','vehicle_list.id');
        $query->leftJoin('inspection_forms', 'inspection_history.inspection_form_id','=', 'inspection_forms.id');
        
        
        // $query = self::select(['*','model.title as model_title'])
        // ->leftJoin('types.title as type_title', 'makes.title as make_title', 'models.title as model_title', 'bodytypes.title as bodyype_title')
        // ->get();
        // ->join('users', 'users.id', '=', 'exams.user_id')
        if( $search){
            $query = $query->where(function($q) use ($search){
                $q->orWhere('vehicle_list.vin_sn','LIKE',"%$search%");
                $q->orWhere('inspection_forms.title','LIKE',"%$search%");
                $q->orWhere('inspection_forms.description','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to){
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->whereBetween('created_at',$date);
            });
        }

        $query->where('inspection_history.vehicle_id',$vehicle_id);

        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();

        
                       
        return ['response'=>$response,'count'=>$count];
    }
    
    public function vehicle() {
        return $this->belongsTo('App\Model\Vehicle', 'vehicle_id');
    }

    public function form() {
        return $this->belongsTo('App\Model\InspectionsForms', 'inspection_form_id');
    }

    public function user() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function items() {
        return $this->hasMany('App\Model\InspectionHistoryItems', 'inspection_history_id');
    }

     
}


