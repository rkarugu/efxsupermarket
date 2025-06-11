<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class ServiceIssues extends Model
{
 public $table = "service_issues";
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = self::select(['*'])
        // $query = ServiceTask::select('servicetask.*','subtypes.title as subtypes_title')
        // ->leftjoin('subtypes','servicetask.subtype','=','subtypes.id');
        // ->leftjoin('wa_suppliers','servicetask.vendor','=','wa_suppliers.id');
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


        public function servicetask()

    {

        return $this->belongsTo(ServiceTask::class,'service_task','id');

    }

   
}