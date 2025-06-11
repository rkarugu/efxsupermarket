<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Model\WaLocationAndStore;
use App\Model\BankEquityTransaction;
use Illuminate\Support\Facades\Validator;
class EquityBankController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'equity-bank-deposits';
        $this->title = 'Equity Bank Deposits';
        $this->pmodule = 'equity-bank-deposits';
    } 
    
    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            if($request->ajax()){
                $sortable_columns = [
                    'bank_equity_transactions.id',
                    'bank_equity_transactions.transactionDate',
                    'bank_equity_transactions.CustomerRefNumber',
                    'bank_equity_transactions.bankreference',
                    'bank_equity_transactions.billAmount',
                    'bank_equity_transactions.created_at',
                ];
                $limit          = $request->input('length');
                $start          = $request->input('start');
                $search         = $request['search']['value'];
                $orderby        = $request['order']['0']['column'] ?? 'id';
                $order          = $request['order']['0']['dir'] ?? "DESC";
                $draw           = $request['draw'];          
                $data = BankEquityTransaction::where(function($w) use ($request,$search){
                    if($request->input('from_date') && $request->input('to_date')){
                        $w->whereBetween('transactionDate',[$request->input('from_date').' 00:00:00',$request->input('to_date').' 23:59:59']);
                    }         
                })->where(function($w) use ($search){
                    if($search){
                        $w->orWhere('bank_equity_transactions.CustomerRefNumber','LIKE',"%$search%");
                        $w->orWhere('bank_equity_transactions.billAmount','LIKE',"%$search%");
                        $w->orWhere('bank_equity_transactions.bankreference','LIKE',"%$search%");
                    }
                })->where(function($e) use ($request){
                    if($request->salesman){
                        $e->where('debitaccount',$request->salesman);
                    }
                })->orderBy($sortable_columns[$orderby],$order)->where('transaction_type','Credit');       
                $totalCms       = count($data->get());          
                $response       = $data->limit($limit)->offset($start)->get()->map(function($item) use ($permission,$pmodule){
                    $item->links = '';                   
                    if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
                    {
                        $item->links = '<form class="deleteMe" action="'.route('equity-bank-deposits.destroy',$item->id).'" method="post">
                        '.csrf_field().'
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="id" value="'.$item->id.'">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-trash"></i></button>
                        </form>
                        ';
                    }
                    $item->transactionDate = date('d/M/Y',strtotime($item->transactionDate));
                    // $item->created_at = date('d/M/Y',strtotime($item->created_at));
                    $item->amount = $item->billAmount;
                    $item->billAmount = manageAmountFormat($item->billAmount);
                    return $item;
                });    
                $total = 0;
                foreach ($response as $value) {
                    $total += $value->amount;
                }   
                $return = [
                    "draw"              =>  intval($draw),
                    "recordsFiltered"   =>  intval( $totalCms),
                    "recordsTotal"      =>  intval( $totalCms),
                    "data"              =>  $response,
                    'total'             =>  manageAmountFormat($total)
                ];
                return $return;
            }
            $locations = WaLocationAndStore::where('account_no','!=','')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.equity_bank_deposits.index',compact('title','model','breadcum','pmodule','permission','locations'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }             
    }

    public function create(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
        {
            $locations = WaLocationAndStore::where('account_no','!=','')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.equity_bank_deposits.create',compact('title','model','breadcum','pmodule','permission','locations'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  
    }   

    public function store(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
        {
            $validation = Validator::make($request->all(),[
                'salesman'=>'required|exists:wa_location_and_stores,id',
                'billAmount'=>'required|numeric|min:0|max:99999999999',
                'CustomerRefNumber'=>'required|string|max:250',
                'bankreference'=>'required|string|max:250'
            ]);
            if($validation->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validation->errors()
                ]);
            }
            $userid = 1;
            $locationid = null;
            $location = \App\Model\WaLocationAndStore::where('id',$request->salesman)->first();
            if($location){
                $user = \App\Model\User::where('wa_location_and_store_id',$location->id)->first();
                if($user){
                    $userid = $user->id;
                }
                $locationid = $location->id;
            }
            $item = new BankEquityTransaction;
            $item->user_id = $userid;
            $item->billAmount = $request->billAmount;
            $item->CustomerRefNumber = $request->CustomerRefNumber;
            $item->bankreference = $request->bankreference;
            $item->transactionDate = date('Y-m-d H:i:s');
            $item->debitaccount = $location->account_no;
            $item->transaction_type = 'Credit';
            $item->wa_location_and_store_id = $locationid;
            $item->save();
            return response()->json([
                'result'=>1,
                'message'=>'Equity Bank Transaction added successfully',
                'location'=>route('equity-bank-deposits.index')
            ]);
        }
        
        return response()->json([
            'result'=>-1,
            'message'=>'Restricted! You Dont have access'
        ]);

    }

    // public function edit(Request $request,$id)
    // {
    //     $permission =  $this->mypermissionsforAModule();
    //     $pmodule = $this->pmodule;
    //     $title = $this->title;
    //     $model = $this->model;
    //     if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
    //     {
    //         $item = BankEquityTransaction::where('id',$id)->first();
    //         if($item){
    //             $locations = WaLocationAndStore::where('account_no','!=','')->get();
    //             $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
    //             return view('admin.equity_bank_deposits.edit',compact('title','model','breadcum','pmodule','permission','locations','item'));
    //         }
    //     }
    //     Session::flash('warning', 'Invalid Request');
    //     return redirect()->back();
    // }   

    // public function update(Request $request,$id)
    // {
    //     $validation = Validator::make($request->all(),[
    //         'id'=>'required|exists:bank_equity_transactions,id|in:'.$id,
    //         'salesman'=>'required|exists:wa_location_and_stores,id',
    //         'billAmount'=>'required|numeric|min:0|max:99999999999',
    //         'CustomerRefNumber'=>'required|string|max:250',
    //         'bankreference'=>'required|string|max:250'
    //     ]);
    //     if($validation->fails()){
    //         return response()->json([
    //             'result'=>0,
    //             'errors'=>$validation->errors()
    //         ]);
    //     }
    //     $userid = 1;
    //     $locationid = null;
    //     $location = \App\Model\WaLocationAndStore::where('id',$request->salesman)->first();
    //     if($location){
    //         $user = \App\Model\User::where('wa_location_and_store_id',$location->id)->first();
    //         if($user){
    //             $userid = $user->id;
    //         }
    //         $locationid = $location->id;
    //     }
    //     $item = BankEquityTransaction::where('id',$id)->first();
    //     $item->user_id = $userid;
    //     $item->billAmount = $request->billAmount;
    //     $item->CustomerRefNumber = $request->CustomerRefNumber;
    //     $item->bankreference = $request->bankreference;
    //     $item->transactionDate = =date('Y-m-d H:i:s');
    //     $item->debitaccount = $location->account_no;
    //     $item->transaction_type = 'Credit';
    //     $item->wa_location_and_store_id = $locationid;
    //     $item->save();
    //     return response()->json([
    //         'result'=>1,
    //         'message'=>'Equity Bank Transaction saved successfully'
    //     ]);
    // }
    public function destroy(Request $request,$id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')
        {
            $item = BankEquityTransaction::where('id',$id)->first();
            if($item){
                $item->delete();
                return response()->json([
                    'result'=>1,
                    'message'=>'Equity Bank Transaction deleted successfully',
                    'location'=>route('equity-bank-deposits.index')
                ]);
            }
            return response()->json([
                'result'=>-1,
                'message'=>'Something went wrong',
            ]);
        }
         return response()->json([
            'result'=>-1,
            'message'=>'Restricted! You dont have access',
        ]);
    }
}