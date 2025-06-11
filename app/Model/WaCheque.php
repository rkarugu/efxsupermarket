<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaCheque extends Model
{
    protected $guarded = [];

    public function payee()
    {
        return $this->belongsTo(WaSupplier::class,'supplier_id');
    }

    public function payment_account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'bank_account_id');
    }

    public function categories()
    {
        return $this->hasMany(WaChequeCategories::class,'cheque_id')->with(['category','tax_manager'])->orderBy('id','DESC');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }

    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'wa_cheques.id';
        $order    = $order ? $order : 'desc';
        $query = WaCheque::select(['wa_cheques.*',
        'wa_cheques.total as totalAmount',
        'wa_suppliers.supplier_code',
        'wa_charts_of_accounts.account_name',
        'restaurants.name'
        ]);
        $query = $query->join('wa_suppliers',function($join){
            $join->on('wa_suppliers.id','=','wa_cheques.supplier_id');
        });
        $query = $query->join('wa_charts_of_accounts',function($join){
            $join->on('wa_charts_of_accounts.id','=','wa_cheques.bank_account_id');
        });
        $query = $query->join('restaurants',function($join){
            $join->on('restaurants.id','=','wa_cheques.restaurant_id');
        });
        if($request->processed != ''){
            $query = $query->where('wa_cheques.is_processed',$request->processed);
        }
        
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_suppliers.supplier_code','LIKE',"%$search%");
                $q->orWhere('wa_charts_of_accounts.account_name','LIKE',"%$search%");
                $q->orWhere('wa_cheques.cheque_no','LIKE',"%$search%");
                $q->orWhere('wa_cheques.payment_date','LIKE',"%$search%");
                $q->orWhere('wa_cheques.memo','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->orWhereBetween('wa_cheques.created_at',$date)->orWhereBetween('wa_cheques.payment_date',$date);
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
