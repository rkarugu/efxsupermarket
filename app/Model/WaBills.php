<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaBills extends Model
{
    protected $guarded = [];

    public function categories()
    {
        return $this->hasMany(WaBillCategories::class,'bill_id')->with(['category','tax_manager','project','gltag'])->orderBy('id','DESC');
    }
    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class,'supplier_id');
    }
    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }
    public function terms()
    {
        return $this->belongsTo(WaPaymentTerm::class,'terms_id');
    }
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = WaBills::select(['wa_bills.*',
        'wa_bills.total as totalAmount',
        \DB::RAW('CONCAT(wa_suppliers.name," (",wa_suppliers.supplier_code,")") as supplier_code'),
        'restaurants.name',
        ]);
        $query = $query->join('wa_suppliers',function($join){
            $join->on('wa_suppliers.id','=','wa_bills.supplier_id');
        });
        $query = $query->join('restaurants',function($join){
            $join->on('restaurants.id','=','wa_bills.restaurant_id');
        });
        if ($request->processed!= '') {
            $query = $query->where('wa_bills.is_processed',$request->processed);
        }
        // $query= $query->where('balance','>',0);
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_suppliers.name','LIKE',"%$search%");
                $q->orWhere('wa_suppliers.supplier_code','LIKE',"%$search%");
                $q->orWhere('wa_bills.bill_no','LIKE',"%$search%");
                $q->orWhere('restaurants.name','LIKE',"%$search%");
                $q->orWhere('wa_bills.bill_date','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->orWhereBetween('wa_bills.created_at',$date)->orWhereBetween('wa_bills.bill_date',$date);
            });
        }
        $count = count($query->get());
        $response   =   $query->orderBy($orderby, $order)
                                ->offset($offset)
                                ->limit($limit)        
                                ->get();
        return ['response'=>$response,'count'=>$count];
    }
    public function getOpeningBalanceAttribute()
    {
        return '0.00' ;
    }
}
