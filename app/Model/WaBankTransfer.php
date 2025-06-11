<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaBankTransfer extends Model
{
    protected $guarded = [];

    public function transfer_from_account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'transfer_from');
    }
    public function transfer_to_account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'transfer_to');
    }
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'id';
        $order    = $order ? $order : 'desc';
        $query = WaBankTransfer::select(['wa_bank_transfers.*',
        \DB::RAW('CONCAT(from.account_name," (",from.account_code,")") as from_code'),
        \DB::RAW('CONCAT(to.account_name," (",to.account_code,")") as to_code'),
        ]);
        $query = $query->join('wa_charts_of_accounts as from',function($join){
            $join->on('from.id','=','wa_bank_transfers.transfer_from');
        });
        $query = $query->join('wa_charts_of_accounts as to',function($join){
            $join->on('to.id','=','wa_bank_transfers.transfer_to');
        });
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->orWhereBetween('wa_bank_transfers.created_at',$date)->orWhereBetween('wa_bank_transfers.date',$date);
            });
        }
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_bank_transfers.amount','LIKE',"%$search%");
                $q->orWhere('wa_bank_transfers.date','LIKE',"%$search%");
                $q->orWhere('wa_bank_transfers.memo','LIKE',"%$search%");

                $q->orWhere('from.account_code','LIKE',"%$search%");
                $q->orWhere('to.account_code','LIKE',"%$search%");
                $q->orWhere('from.account_name','LIKE',"%$search%");
                $q->orWhere('to.account_name','LIKE',"%$search%");
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
