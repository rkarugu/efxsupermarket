<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPettyCash;
use App\Model\WaPettyCashItem;
use App\Model\WaPettyCashApprovals;
use App\Model\WaChartsOfAccount;
use Session;
use DB;

class PettyCashController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'petty-cash';
        $this->title = 'Petty Cash';
        $this->pmodule = 'petty-cash';
    }

    public function create(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.petty_cash.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = WaPettyCash::with(['user','approvals'=>function($e){
                $e->where('status','Pending');
            }])->where(function ($w) use ($request) {
                if ($request->input('start-date') && $request->input('end-date')) {
                    $w->whereBetween('created_at', [$request->input('start-date') . ' 00:00:00', $request->input('end-date') . ' 23:59:59']);
                }
            })->orderBy('id', 'desc')->paginate(20);
            return view('admin.petty_cash.index', compact('data', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___add']) && $permission != 'superadmin') {
            return response()->json(['result' => -1, 'message' => "Restricted: You Don't Have enough permissions"]);
        }
        if (!$request->ajax()) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        if (!isset($request->category_list) || count($request->category_list) == 0) {
            return response()->json(['result' => 0, 'errors' => ['category_lists' => ['Add atleast 1 one item to process']]]);
        }
        $validator = \Validator::make($request->all(), [
            'amount.*' => 'required|min:1|numeric',
            'category_list.*' => 'required|exists:wa_charts_of_accounts,id',
            // 'name.*'=>'required|min:0|max:200',
            'branch_id.*'=>'required|exists:restaurants,id',
            'payment_for.*' => 'required|min:0|max:200',
            'collected_by.*' => 'required|min:0|max:200',
            'payment_date'=>'required|date|date_format:Y-m-d',
            'payment_method_id'=>'required|exists:payment_methods,id',
            'wa_bank_account_id'=>'required|exists:wa_bank_accounts,id',
            'type'=>'required|in:save,process'
        ], [], [
            'amount.*' => 'Amount',
            'category_list.*' => 'Account',
            // 'name.*'=>'Name',
            // 'receive_from.*'=>'Received From',
            'payment_for.*' => 'Payment For',
            'collected_by.*' => 'Collected By',
            'payment_method_id'=>'payment method',
            'wa_bank_account_id'=>'bank account',
        ]);
        if ($validator->fails()) {
            return response()->json(['result' => 0, 'errors' => $validator->errors()]);
        }
        $category = WaChartsOfAccount::whereIn('id', $request->category_list)->get();
        if (count($category) == 0) {
            return response()->json(['result' => 0, 'errors' => ['category_lists' => ['No accounts found']]]);
        }
        try {
            $check = DB::transaction(function () use ($request, $category) {
                $WaAccountingPeriod = \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
                $series_module = \App\Model\WaNumerSeriesCode::where('module', 'PETTY_CASH')->first();
                $grn_number = getCodeWithNumberSeries('PETTY_CASH');
                $user = getLoggeduserProfile();
                $petty = new WaPettyCash;
                $petty->petty_cash_no = $grn_number;
                $petty->user_id = $user->id;
                $petty->payment_date = $request->payment_date;
                $petty->wa_bank_account_id = $request->wa_bank_account_id;
                $petty->payment_method_id = $request->payment_method_id;
                $petty->type = $request->type == 'process' ? 'processed' : 'saved';     
                $petty->total_amount = array_sum($request->amount);
                $petty->save();
                $petty_items = [];
                $total_amount = 0;
                $datetime = date('Y-m-d H:i:s');
                foreach ($request->category_list as $key => $value) {
                    $petty_items[] = [
                        'wa_petty_cash_id' => $petty->id,
                        'amount' => $request->amount[$key],
                        'wa_charts_of_account_id' => $value,
                        'branch_id'=>@$request->branch_id[$key],
                        'payment_for' => $request->payment_for[$key],
                        'collected_by' => $request->collected_by[$key],
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ];
                }
                if (count($petty_items) > 0) {
                    WaPettyCashItem::insert($petty_items);
                }
                if($request->type == 'process'){
                    $ins = [];
                    for($i=1;$i<=1;$i++){
                        $ins[] = [
                            'petty_cash_id'=>$petty->id,
                            'status'=>'Pending',
                            'stage'=>$i
                        ];
                    }
                    WaPettyCashApprovals::insert($ins);
                }
                
                updateUniqueNumberSeries('PETTY_CASH', $grn_number);
                return $petty;
            });
            $message = 'Petty Cash Saved successfully.';
            $location = route('petty-cash.index');
            $print = 0;
            if($request->type == 'process'){
                $message = 'Petty Cash Processed successfully.';
                // $location = route('petty-cash.print',base64_encode($check->id));
                // $print = 1;
            }
            if($check){
                return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'print'=>$print]);         
            }
            return response()->json(['result' => -1, 'message' => 'Something went wrong']);
        } catch (\Exception $th) {
            return response()->json(['result' => -1, 'message' => $th->getMessage()]);
        }
    }

    public function category_list(Request $request)
    {
        # Payment Accounts
        $data = WaChartsOfAccount::select(['id as id', \DB::RAW('concat(account_name," (",account_code,")") as text')])->where(function($e) use ($request){
            if ($request->q) {
                $e->orWhere('account_name', 'LIKE', "%$request->q%");
                $e->orWhere('account_code', 'LIKE', "%$request->q%");
            }
        });
        
        $data = $data->whereHas('getSubAccountSection.getParentAccountGroup.getAccountSection',function($w){
            $w->whereIn('section_name', ['EXPENSES']);
        })->get();
        return $data;
    }

    public function print($id)
    {
        $id = base64_decode($id);
        $data['data'] = WaPettyCash::with(['items', 'items.chart_of_account'])->where('id', $id)->first();
        if (!$data['data']) {
            return 'Something went wrong';
        }
        return view('admin.petty_cash.print')->with($data);
    }

    public function exportpdf($id)
    {
        $id = base64_decode($id);
        $data = WaPettyCash::with(['items', 'items.chart_of_account'])->where('id', $id)->first();
        if (!$data) {
            Session::flash('alert-danger', 'Invalid Request');
            return redirect()->back();
        }
        $pdf = \PDF::loadView('admin.petty_cash.print', compact('data'));
        $report_name = 'petty_cash' . date('Y_m_d_H_i_A');
        return $pdf->download($report_name . '.pdf');
    }

    public function show($id)
    {
        $id = base64_decode($id);
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = WaPettyCash::with(['items', 'user', 'items.chart_of_account'])->where('id', $id)->first();
            if (!$data) {
                Session::flash('alert-danger', 'Invalid Request');
                return redirect()->back();
            }
            return view('admin.petty_cash.show', compact('data', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = WaPettyCash::with(['bank_account',
            'payment_method','items','user',
            'items.chart_of_account','items.branch'])->where('type','!=','processed')->where('id',$id)->first();
            if(!$data){
                Session::flash('alert-danger','Invalid Request');
                return redirect()->back();
            }
            return view('admin.petty_cash.edit', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function bank_accounts(Request $request)
    {
        # Payment Accounts          
        $user = getLoggeduserProfile();
        $data = \App\Model\WaBankAccount::select(['id as id','account_number as text'])->where(function($e) use ($request,$user){
            if($user->role_id != 1){
                $e->whereHas('assigned_users',function($d) use ($request,$user){
                    $d->where('user_id',$user->id);
                });
            }
        });
        if($request->q)
        {
            $data = $data->where('account_number','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return response()->json($data);
    }
    public function update($id,Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___add']) && $permission != 'superadmin') {
            return response()->json(['result'=>-1,'message'=>"Restricted: You Don't Have enough permissions"]);
        }
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        if(!isset($request->category_list) || count($request->category_list) == 0){
            return response()->json(['result'=>0,'errors'=>['category_lists'=>['Add atleast 1 one item to process']]]);
        }
        $validator = \Validator::make($request->all(),[
            'amount.*' => 'required|min:1|numeric',
            'category_list.*' => 'required|exists:wa_charts_of_accounts,id',
            'branch_id.*'=>'required|exists:restaurants,id',
            'payment_for.*' => 'required|min:0|max:200',
            'collected_by.*' => 'required|min:0|max:200',
            'payment_date'=>'required|date|date_format:Y-m-d',
            'payment_method_id'=>'required|exists:payment_methods,id',
            'wa_bank_account_id'=>'required|exists:wa_bank_accounts,id',
            'type'=>'required|in:save,process'
        ], [], [
            'amount.*' => 'Amount',
            'category_list.*' => 'Account',
            'payment_for.*' => 'Payment For',
            'collected_by.*' => 'Collected By',
            'payment_method_id'=>'payment method',
            'wa_bank_account_id'=>'bank account',
        ]);
        if($validator->fails()){
            return response()->json(['result'=>0,'errors'=>$validator->errors()]);
        }
        $category = WaChartsOfAccount::whereIn('id',$request->category_list)->get();
        if(count($category) == 0){
            return response()->json(['result'=>0,'errors'=>['category_lists'=>['No accounts found']]]);
        }
        try {
            $petty = WaPettyCash::where('id',$id)->where('type','!=','processed')->first();
            if (!$petty) {
                return response()->json(['result'=>-1,'message'=>'Invalid Request']);
            }
            $check = DB::transaction(function () use ($request,$category,$petty) {
                $series_module = \App\Model\WaNumerSeriesCode::where('module', 'PETTY_CASH')->first();
                $user = getLoggeduserProfile();
                $grn_number = $petty->petty_cash_no;
                // $petty->user_id = $user->id;
                $petty->payment_date = $request->payment_date;
                $petty->wa_bank_account_id = $request->wa_bank_account_id;
                $petty->payment_method_id = $request->payment_method_id;
                $petty->type = $request->type == 'process' ? 'processed' : 'saved';     
                $petty->total_amount = array_sum($request->amount);         
                $petty->save();
                $petty_items = $dataA = [];
                $total_amount = 0;
                $datetime = date('Y-m-d H:i:s');
                $accountSS = [];
                WaPettyCashItem::where('wa_petty_cash_id',$petty->id)->delete();
                foreach ($request->category_list as $key => $value) {                    
                    $petty_items[] = [
                        'wa_petty_cash_id' => $petty->id,
                        'amount' => $request->amount[$key],
                        'wa_charts_of_account_id' => $value,
                        'branch_id'=>@$request->branch_id[$key],
                        'payment_for' => $request->payment_for[$key],
                        'collected_by' => $request->collected_by[$key],
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ];
                }

                if(count($petty_items)>0){
                    WaPettyCashItem::insert($petty_items);
                }
                if($request->type == 'process'){
                    $ins = [];
                    for($i=1;$i<=1;$i++){
                        $ins[] = [
                            'petty_cash_id'=>$petty->id,
                            'status'=>'Pending',
                            'stage'=>$i
                        ];
                    }
                    WaPettyCashApprovals::insert($ins);
                }
                return $petty;
            });
            $message = 'Petty Cash Saved successfully.';
            $location = route('petty-cash.index');
            $print = 0;
            if($request->type == 'process'){
                $message = 'Petty Cash Processed successfully.';
                // $location = route('petty-cash.print',base64_encode($check->id));
                // $print = 1;
            }
            if($check){
                return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'print'=>$print]);         
            }
            return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
        } catch (\Exception $th) {
            return response()->json(['result'=>-1,'message'=>$th->getMessage()]);
        }
    }

    public function pending_approvals(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'petty-cash-approvals';
        if(!$request->stage || $request->stage < 1 || $request->stage > 2){
            $request->stage = 1;
        }
        if (isset($permission[$pmodule . '___pending-approval']) || $permission == 'superadmin') {
            $breadcum = [$title => route($this->model . '.pending_approvals'), 'Listing' => ''];
            $data = WaPettyCashApprovals::with(['petty_cash.user'])->where(function ($w) use ($request) {
                if ($request->input('start-date') && $request->input('end-date')) {
                    $w->whereBetween('created_at', [$request->input('start-date') . ' 00:00:00', $request->input('end-date') . ' 23:59:59']);
                }
            })->where('status','Pending')->where('stage',$request->stage)->orderBy('id', 'desc')->paginate(20);
            return view('admin.petty_cash.pending_approvals', compact('data', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function completed_approvals(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'petty-cash-approvals';
        if(!$request->stage || $request->stage < 1 || $request->stage > 2){
            $request->stage = 1;
        }
        $status = $request->status ?? 'Approved';
        if (isset($permission[$pmodule . '___completed_approvals']) || $permission == 'superadmin') {
            $breadcum = [$title => route($this->model . '.completed_approvals'), 'Listing' => ''];
            $data = WaPettyCashApprovals::with(['petty_cash.user'])->where(function ($w) use ($request) {
                if ($request->input('start-date') && $request->input('end-date')) {
                    $w->whereBetween('created_at', [$request->input('start-date') . ' 00:00:00', $request->input('end-date') . ' 23:59:59']);
                }
            })->where('status',$status)->where('stage',$request->stage)->orderBy('id', 'desc')->paginate(20);
            return view('admin.petty_cash.completed_approvals', compact('data', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function pending_approval_show($id)
    {
        $id = base64_decode($id);
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'petty-cash-approvals';
        if (isset($permission[$pmodule . '___pending-approval']) || $permission == 'superadmin') {
            $breadcum = [$title => route($this->model.'.pending_approvals'), 'Listing' => ''];
            $data = WaPettyCashApprovals::with(['petty_cash.user','petty_cash.items'])->whereHas('petty_cash')->where('id', $id)->first();
            if (!$data) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            return view('admin.petty_cash.pending_approval_show', compact('data', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function pending_approval_update(Request $request, $id){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (!isset($permission[$pmodule . '___pending-approval']) && $permission != 'superadmin') {
            return response()->json(['result'=>-1,'message'=>"Restricted: You Don't Have enough permissions"]);
        }
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $validator = \Validator::make($request->all(),[
            'status'=>'required|in:reject,approve'
        ]);
        if($validator->fails()){
            return response()->json(['result'=>0,'errors'=>$validator->errors()]);
        }
        
        try {
            $data = WaPettyCashApprovals::with(['petty_cash.items'])->whereHas('petty_cash')->where('status','Pending')->where('id', $id)->first();

            if (!$data) {
                return response()->json(['result'=>-1,'message'=>'Invalid Request']);
            }
            $WaAccountingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period', '1')->first();
            $check = DB::transaction(function () use ($request,$data,$WaAccountingPeriod) {
                $datetime = date('Y-m-d H:i:s');

                $series_module = \App\Model\WaNumerSeriesCode::where('module', 'PETTY_CASH')->first();
                $data->status = $request->status == 'approve' ? 'Approved' : 'Rejected';
                $data->approved_at = $datetime;
                $data->save();
                if($request->status == 'approve' && $data->stage == 1){
                    $old = WaPettyCashApprovals::where([
                        'petty_cash_id'=>$data->petty_cash->id,
                        'stage'=>2
                    ])->first();
                    if(!$old){
                        $ins = [
                            'petty_cash_id'=>$data->petty_cash->id,
                            'status'=>'Pending',
                            'stage'=>2
                        ];
                        
                        WaPettyCashApprovals::insert($ins);
                    }
                }
                if($request->status == 'approve' && $data->stage == 2){
                    $dataA = [];
                    $total_amount = 0;
                    $accountSS = [];
                    $grn_number = $data->petty_cash->petty_cash_no;
                    foreach ($data->petty_cash->items as $key => $value) {                    
                        
                        $total_amount += $value->amount;
                        
                        $account = \DB::table('wa_charts_of_accounts')->where('id',$value->wa_charts_of_account_id)->first();
                        $dataA[] = (Object)[
                            'petty_cash_id'=>$data->petty_cash->id,
                            'transaction_type'=>$series_module->description,
                            'transaction_no'=>$grn_number,
                            'period_number'=>$WaAccountingPeriod?$WaAccountingPeriod->period_no:null,
                            'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                            'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                            'type'=>'Debit',
                            'restaurant_id'=>@$value->branch_id,
                            'description'=>''.$value->payment_for.' - '.$value->collected_by,
                            'account'=>$account ? $account->account_code : NULL,
                            'account_name'=>$account->parent_id,
                            'amount'=>$value->amount,
                            'balancing_gl_account'=>1
                        ];
                        $accountSS[] = $account->parent_id;
                    }
                    $accountsss = WaChartsOfAccount::whereIn('id',$accountSS)->get();
    
                    $account = \App\Model\WaBankAccount::with(['getGlDetail'])->where('id',$data->petty_cash->wa_bank_account_id)->first();
                    $dataA[] = (Object)[
                        'petty_cash_id'=>$data->petty_cash->id,
                        'transaction_type'=>$series_module->description,
                        'transaction_no'=>$grn_number,
                        'period_number'=>$WaAccountingPeriod?$WaAccountingPeriod->period_no:null,
                        'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                        'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                        'type'=>'Credit',
                        'description'=>'',
                        'restaurant_id'=>NULL,//$user->restaurant_id,
                        'account_name'=>NULL,
                        'account'=>$account ? @$account->getGlDetail->account_code : NULL,
                        'amount'=> $total_amount,
                        'balancing_gl_account'=>NULL
                    ];
    
                    foreach ($dataA as $key => $value) {
                        $cr = new \App\Model\WaGlTran();
                        $cr->user_id = $data->petty_cash->user->id;
                        $cr->period_number = $value->period_number;
                        $cr->petty_cash_id = $value->petty_cash_id;
                        $cr->grn_type_number = $value->grn_type_number;
                        $cr->trans_date = $data->petty_cash->payment_date;
                        $cr->restaurant_id = $value->restaurant_id;
                        $cr->grn_last_used_number = $value->last_number_used;
                        $cr->transaction_type = $value->transaction_type;
                        $cr->transaction_no = $value->transaction_no;
                        $cr->account = $value->account;
                        $cr->amount = '-'.$value->amount;
                        $cr->narrative = $value->description;
                        $cr->reference = NULL;
                        $cr->balancing_gl_account = ($value->balancing_gl_account == 1 ? ($account ? @$account->getGlDetail->account_code : NULL) : NULL);
                        if($value->type == 'Debit')
                        {
                            $btran = new \App\Model\WaBanktran;
                            $btran->type_number = $value->grn_type_number;
                            $btran->document_no = $value->transaction_no;
                            $btran->bank_gl_account_code = $account ? @$account->getGlDetail->account_code : NULL;
                            $btran->reference =  $value->description;
                            $btran->trans_date = $data->petty_cash->payment_date;
                            $btran->wa_payment_method_id = $data->petty_cash->payment_method_id;
                            $btran->amount = '-'.$value->amount;
                            $btran->wa_curreny_id = 1;
                            $btran->account = $value->account;
                            $acGL = $accountsss->where('id',$value->account_name)->first();
                            $btran->sub_account = @$acGL->account_code ?? NULL;
                            $btran->save();
                            $cr->amount = $value->amount;
                        }
                        $cr->save();
                    }
                }
                return $data;
            });
            $message = 'Petty Cash Request'.$check->status.' successfully.';
            $location = route('petty-cash.pending_approvals',['stage'=>2]);
            $print = 0;
            if($check){
                $petty_cash = WaPettyCashApprovals::where('status','Pending')->where('petty_cash_id',$data->petty_cash_id)->count();
                if($petty_cash == 0){
                    $print=1;
                    $location = route('petty-cash.print',base64_encode($data->petty_cash_id));
                }
                return response()->json(['result'=>1,'message'=>$message,'location'=>$location,'print'=>$print]);         
            }
            return response()->json(['result'=>-1,'message'=>'Something went wrong']);  
        } catch (\Exception $th) {
            return response()->json(['result'=>-1,'message'=>$th->getMessage()]);
        }
    }

}