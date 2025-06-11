<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaBankDeposit extends Model
{
    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'payment_account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'branch_id');
    }

    public function categories()
    {
        return $this->hasMany(WaBankDepositCategory::class,'wa_bank_deposite_id')->orderBy('id','desc');
    }

    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = WaBankDeposit::select(['wa_bank_deposits.*',
        \DB::RAW('CONCAT(wa_charts_of_accounts.account_name," (",wa_charts_of_accounts.account_code,")") as account'),
        'restaurants.name',
        ]);
        $query = $query->join('wa_charts_of_accounts',function($join){
            $join->on('wa_charts_of_accounts.id','=','wa_bank_deposits.payment_account_id');
        });
        $query = $query->join('restaurants',function($join){
            $join->on('restaurants.id','=','wa_bank_deposits.branch_id');
        });
        if ($request->processed != '') {
            $query = $query->where('wa_bank_deposits.is_processed',$request->processed);
        }
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_bank_deposits.total','LIKE',"%$search%");
                $q->orWhere('wa_bank_deposits.date','LIKE',"%$search%");

                $q->orWhere('wa_charts_of_accounts.account_code','LIKE',"%$search%");
                $q->orWhere('wa_charts_of_accounts.account_name','LIKE',"%$search%");
                $q->orWhere('restaurants.name','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->orWhereBetween('wa_bank_deposits.created_at',$date)->orWhereBetween('wa_bank_deposits.date',$date);
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
