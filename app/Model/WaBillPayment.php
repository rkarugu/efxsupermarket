<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaBillPayment extends Model
{
    protected $guarded = [];
    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'wa_bill_payments.id';
        $order    = $order ? $order : 'desc';
        $query = WaBillPayment::select(['wa_bill_payments.*',
        'wa_bill_payments.amount as totalAmount',
        'wa_suppliers.supplier_code',
        \DB::RAW('CONCAT(wa_charts_of_accounts.account_name," (",wa_charts_of_accounts.account_code,") ") as account'),
        ]);
        $query = $query->join('wa_suppliers',function($join){
            $join->on('wa_suppliers.id','=','wa_bill_payments.supplier_id');
        });
        $query = $query->join('wa_charts_of_accounts',function($join){
            $join->on('wa_charts_of_accounts.id','=','wa_bill_payments.bank_account_id');
        });
        $query= $query->where('wa_bill_id',$request->id);
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_suppliers.supplier_code','LIKE',"%$search%");
                $q->orWhere('wa_bill_payments.ref_no','LIKE',"%$search%");
                $q->orWhere('wa_charts_of_accounts.account_name','LIKE',"%$search%");
                $q->orWhere('wa_charts_of_accounts.account_code','LIKE',"%$search%");
                $q->orWhere('wa_bill_payments.payment_date','LIKE',"%$search%");
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
