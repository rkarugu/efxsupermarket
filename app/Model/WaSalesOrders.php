<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;

class WaSalesOrders extends Model
{
    protected $table = 'wa_sales_orders';
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(WaSalesOrderItems::class,'wa_sales_orders_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'wa_sales_orders.document_no';
        $order    = $order ? $order : 'desc';
        $query = self::select(['wa_sales_orders.*',
            'users.name as salesman',
            'routes.route_name as salesman_route',
            DB::RAW("COUNT(wa_sales_orders_items.id) as no_of_items"),
            DB::RAW("SUM(wa_sales_orders_items.total) as total_price")
        ])->leftJoin('users',function($e){
            $e->on('users.id','=','wa_sales_orders.user_id');
        })->leftJoin('wa_sales_orders_items',function($e){
            $e->on('wa_sales_orders.id','=','wa_sales_orders_items.wa_sales_orders_id');
        })->leftJoin('routes',function($r){
            $r->on('routes.id','=','users.route');
        });
        if( $search)
        {
            $query = $query->where(function($e) use ($search){
                $e->orWhere('users.name','LIKE',$search."%");
                $e->orWhere('routes.route_name','LIKE',$search."%");
                $e->orWhere('wa_sales_orders.document_no','LIKE',$search."%");
                // $e->orWhere('email','LIKE',$search."%");
            });
        }
        if($request->from && $request->to){
            $query = $query->whereBetween('wa_sales_orders.created_at',[$request->from.' 00:00:00',$request->to.' 23:59:59']);
        }
        $count = count($query->groupBy('wa_sales_orders.id')->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)  
                                ->groupBy('wa_sales_orders.id')      
                                ->get();
        return ['response'=>$response,'count'=>$count];
    }
}