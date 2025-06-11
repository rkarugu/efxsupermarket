<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Excel;
use App\Model\WaGlTran;
use Yajra\DataTables\Facades\DataTables;

class GeneralLedgersController extends Controller {
    
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'genralLedger';
        $this->title = 'Genral Ledgers';
        $this->pmodule = 'genralLedger';
    }
    
    public function glEntries(Request $request){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'GL Entries';
        $model = $this->model.'-gl_entries';
        // if (isset($permission[$pmodule . '___gl_entries_view']) || $permission == 'superadmin') {
                $date1 = $request->get('to');
                $date2 = $request->get('from');
        $restroList = $this->getRestaurantList();
        
        $data = WaGlTran::orderBy('wa_gl_trans.id', 'desc')
                ->with('restaurant','customer','getAccountDetail');//->where('wa_gl_trans.customer_id','!=',NULL);
                
                $negativeAMount =  WaGlTran::where('amount', '<=', '0');
                $positiveAMount =  WaGlTran::where('amount', '>=', '0');
    
                if($request->restaurant){
                    $data->where('wa_gl_trans.restaurant_id', $request->restaurant);
                    $negativeAMount->where('wa_gl_trans.restaurant_id', $request->restaurant);
                    $positiveAMount->where('wa_gl_trans.restaurant_id', $request->restaurant);
                }
                if($request->get('to') && $request->get('from')){
                    $data->whereBetween('wa_gl_trans.trans_date', [$date1 . ' 00:00:00', $date2 . ' 23:59:59']);
                    $negativeAMount->whereBetween('wa_gl_trans.trans_date', [$date1 . ' 00:00:00', $date2 . ' 23:59:59']);
                    $positiveAMount->whereBetween('wa_gl_trans.trans_date', [$date1 . ' 00:00:00', $date2 . ' 23:59:59']);
                }
                $negativeAMount = $negativeAMount->sum('amount');
                $positiveAMount = $positiveAMount->sum('amount');

        if (request()->wantsJson()) {           
            
            
            return DataTables::eloquent($data)
            ->addIndexColumn()
            ->editColumn('trans_date',function($data){
                return getDateFormatted($data->trans_date); 
            })
            ->editColumn('restaurant.name', function($data){
                return (isset($data->restaurant->name)) ? $data->restaurant->name : '----';
            })
            ->addColumn('account_no', function($data){
                if ($data->customer_id) {
                    return $data->customer->customer_name;
                } else{
                    return '---';
                    // if($data->transaction_type=="Sales Invoice" && $data->amount > 0){
                    //     $accountno = explode(':',$data->narrative);                
                    //     return (count($accountno)> 1 ) ? $accountno[0] : '---' ;
                    // }else{
                    //     $accountno = explode('/',$data->narrative);
                    // return (count($accountno)> 1 ) ? $accountno[1] : '---';
                    // }
                }
                
            })
            // ->addColumn('account_code',function($data){
            //     return isset($account_codes[$row->account]) ? $account_codes[$data->account] : '';
            // })
            ->addColumn('debit',function($data){
                return $data->amount>='0'? manageAmountFormat($data->amount):'';
            })
            ->addColumn('credit',function($data){
                return $data->amount<='0'? manageAmountFormat($data->amount):'';
            })
            ->with('negativeAMount',function() use($negativeAMount){
                return manageAmountFormat($negativeAMount);
            })
            ->with('positiveAMount',function() use($positiveAMount){
                return manageAmountFormat($positiveAMount);
            })
            ->toJson();
        }
            



           //     echo "<pre>"; print_r($data); die;
            $breadcum = [$title => ''];
            return view('admin.general_ledgers.gl_entries', compact('restroList','title','model', 'breadcum', 'pmodule', 'permission'));
        // } else {
        //     Session::flash('warning', 'Invalid Request');
        //     return redirect()->back();
        // }
    }
    
}
