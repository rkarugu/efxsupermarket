<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaExpenses extends Model
{
    protected $guarded = [];

    public function categories()
    {
        return $this->hasMany(WaExpenseCategories::class,'expense_id')->with(['category','tax_manager','project','gltag'])->orderBy('id','DESC');
    }
    public function payee()
    {
        return $this->belongsTo(WaSupplier::class,'payee_id');
    }
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function payment_account()
    {
        return $this->belongsTo(WaChartsOfAccount::class,'payment_account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }


    public static function getData($limit, $offset, $search, $orderby,$order,$request)
    {
        $orderby  = $orderby ? $orderby : 'wa_expenses.id';
        $order    = $order ? $order : 'desc';
        $query = WaExpenses::select(['wa_expenses.*',
        'wa_expenses.total as totalAmount',
        // 'wa_suppliers.supplier_code',
        'wa_charts_of_accounts.account_name',
        'payment_methods.title'
        ]);
        // $query = $query->join('wa_suppliers',function($join){
        //     $join->on('wa_suppliers.id','=','wa_expenses.payee_id');
        // });
        $query = $query->join('wa_charts_of_accounts',function($join){
            $join->on('wa_charts_of_accounts.id','=','wa_expenses.payment_account_id');
        });
        $query = $query->join('payment_methods',function($join){
            $join->on('payment_methods.id','=','wa_expenses.payment_method_id');
        });
        
            $query = $query->where('wa_expenses.is_processed',$request->processed);
        
        if( $search)
        {
            $query = $query->where(function($q) use ($search){
                // $q->orWhere('wa_suppliers.supplier_code','LIKE',"%$search%");
                $q->orWhere('wa_charts_of_accounts.account_name','LIKE',"%$search%");
                $q->orWhere('payment_methods.title','LIKE',"%$search%");
                $q->orWhere('wa_expenses.ref_no','LIKE',"%$search%");
                $q->orWhere('wa_expenses.payment_date','LIKE',"%$search%");
                $q->orWhere('wa_expenses.memo','LIKE',"%$search%");
            });
        }
        if($request->from && $request->to)
        {
            $query = $query->where(function($dates) use ($request){
                $date = [$request->from.' 00:00:00',$request->to.' 23:59:59'];
                $dates->orWhereBetween('wa_expenses.created_at',$date)->orWhereBetween('wa_expenses.payment_date',$date);
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
