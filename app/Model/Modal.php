<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Modal extends Model
{    
    public $table = "model";
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = self::select(['*']);
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('title','LIKE',"%$search%");
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
}
