<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaInventoryAdjustment extends Model
{
    
    protected $table = 'wa_inventory_adjustment';
    protected $guarded = [];
 
    public function childs()
    {
        return $this->hasMany(StockAdjustment::class,'wa_inventory_adjustment_id');
    }
    
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'wa_inventory_adjustment.id';
        $order    = $order ? $order : 'desc';
        $query = WaInventoryAdjustment::select(['wa_inventory_adjustment.*',
        'users.name as name',
        \DB::RAW('(SELECT count(*) from stock_adjustments where stock_adjustments.wa_inventory_adjustment_id = wa_inventory_adjustment.id) as no_of_adjustment')
        ]);
        $query = $query->leftjoin('users',function($join){
            $join->on('users.id','=','wa_inventory_adjustment.user_id');
        });
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_inventory_adjustment.document_no','LIKE',"%$search%");
                $q->orWhere('users.name','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->orWhereBetween('wa_inventory_adjustment.created_at',$date);
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


