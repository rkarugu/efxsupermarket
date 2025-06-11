<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaBankAccount;
use App\Model\WaNumerSeriesCode;
use App\Model\WaBanktran;
use App\Model\WaBankAccountUser;
use App\Model\UserPermission;
use App\Model\User;
use App\Models\WaPettyCashRequest;
use App\Models\WaPettyCashRequestItem;
use Illuminate\Support\Facades\DB;
use App\Models\PettyCashTransaction;
use App\Models\WaPettyCashRequestItemWithdrawal;
use Session;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ExcelDownloadService;

class BankAccountController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'bank-accounts';
        $this->title = 'Bank Accounts';
        $this->pmodule = 'bank-accounts';
    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = WaBankAccount::with(['getGlDetail'])->orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.bankaccounts.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function assignUsers($id)
    {
        $title = 'Assign to user '.$this->title;
        $model = $this->model;
        $row =  WaBankAccount::where('id',$id)->first();
        $breadcum = [$this->title =>route($model.'.index'),'Assign to user'=>''];
        $assinedUserIds = WaBankAccountUser::where('wa_bank_account_id',$id)->pluck('user_id')->toArray();
        $roles = UserPermission::where('module_name','Bank-accounts')->pluck('role_id')->toArray();
        $users = User::whereIn('role_id',$roles)->get();
        return view('admin.bankaccounts.assignUsers',compact('assinedUserIds','title','model','breadcum','users','row'));
    }

    public function postAssignUsers(Request $request,$id)
    {
        if($request->ajax()){
            $validator = Validator::make($request->all(),[
                'user'=>'required|array',
                'id'=>'required|exists:wa_bank_accounts,id',
                'user.*'=>'exists:users,id'
            ]);
            if($validator->fails()){
                return response()->json([
                    'result'=>0,
                    'errors'=>$validator->errors()
                ]);
            }
            $check = \DB::transaction(function () use ($request){
                WaBankAccountUser::where('wa_bank_account_id',$request->id)->delete();
                $data = [];
                $datetime = date('Y-m-d H:i:s');
                foreach($request->user as $user){
                    $data[] = [
                        'user_id'=>$user,
                        'wa_bank_account_id'=>$request->id,
                        'created_at'=>$datetime,
                        'updated_at'=>$datetime
                    ];
                }
                WaBankAccountUser::insert($data);
                return true;
            });
            if($check){
                return response()->json([
                    'result'=>1,
                    'message'=>'Users Assigned successfully',
                    'location'=>route('bank-accounts.index')
                ]);
            }
            return response()->json([
                'result'=>-1,
                'message'=>'Something went wrong'
            ]);
        }
        return redirect()->back();
    }
    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.bankaccounts.create',compact('title','model','breadcum'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    public function accountInquiry(Request $request)
    { 
         $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___account-inquiry']) || $permission == 'superadmin')
            {
                $number_series_list = WaNumerSeriesCode::getNumberSeriesTypeList();
                $bank =  WaBankAccount::with(['getGlDetail'])->whereSlug($request->slug)->first();
                $banks =  WaBankAccount::with(['getGlDetail'])->get();

                $getOpeningBlance = 0;
                $breadcum = [$this->title=>'','Account Inquiry'=>''];
                $model =$this->model;
                $title = $this->title;
                $row = [];
                $bankTypesArr=[];
                $narrationType=[];
                if($bank)
                {
     
   	                $date1 = $request->get('from');
	                $date2 = $request->get('to');
                    

                    $gl_code = $bank->getGlDetail?$bank->getGlDetail->account_code:'norecordfound';
                    $row = WaBanktran::where('wa_banktrans.bank_gl_account_code', $gl_code)
                        ->leftJoin('wa_petty_cash_request_item_withdrawals as pcw', 'wa_banktrans.document_no', '=', 'pcw.document_no')
                        ->leftJoin('wa_petty_cash_request_items as pcri', 'pcri.id', '=', 'pcw.request_item_id')
                        ->leftJoin('wa_petty_cash_requests as pcr', 'pcr.id', '=', 'pcri.wa_petty_cash_request_id')
                        ->leftJoin('wa_charts_of_accounts as cof', 'cof.id', '=', 'pcr.wa_charts_of_account_id')
                        ->leftJoin('wa_petty_cash_request_types as pcrt', 'pcrt.slug', '=', 'pcr.type')
                        ->leftJoin('petty_cash_transactions as pct', 'pct.document_no', '=', 'wa_banktrans.document_no')
                        ->leftJoin('users', 'users.id', '=', 'pct.user_id')
                        ->leftJoin('petty_cash_transactions as child', 'wa_banktrans.document_no', '=', 'child.document_no')
                        ->leftJoin('petty_cash_transactions as parent', 'child.parent_id', '=', 'parent.id')
                        ->leftJoin('travel_expense_transactions as tet', 'tet.document_no', '=', 'parent.document_no')
                        ->leftJoin('routes', 'routes.id', '=', 'tet.route_id')
                        ->leftJoin('wa_gl_trans', 'wa_gl_trans.transaction_no', '=', 'wa_banktrans.document_no')
                        ->select(
                            'wa_banktrans.id',
                            'wa_banktrans.account',
                            'wa_banktrans.sub_account',
                            'wa_banktrans.trans_date',
                            'wa_banktrans.type_number',
                            'wa_banktrans.document_no',
                            'wa_banktrans.reference',
                            'wa_banktrans.amount',
                            
                            DB::raw("MAX(CONCAT(UPPER(LEFT(cof.account_name, 1)), LOWER(SUBSTRING(cof.account_name, 2)))) as narration_account_name"),
                            DB::raw("MAX(pcrt.name) as narration_type"),
                            DB::raw("MAX(CONCAT(UPPER(LEFT(pcri.payee_name, 1)), LOWER(SUBSTRING(pcri.payee_name, 2)))) as narration_payee_name"),
                            DB::raw("MAX(pcri.payment_reason) as narration_payment_reason"),
                            DB::raw("MAX(CONCAT(UPPER(LEFT(users.name, 1)), LOWER(SUBSTRING(users.name, 2)))) as narrative_user"),
                            DB::raw("MAX(users.role_id) as narrative_role"),
                            DB::raw("Max(CONCAT(UPPER(LEFT(routes.route_name, 1)), LOWER(SUBSTRING(routes.route_name, 2)))) as narrative_route"),
                            DB::raw("MAX(wa_gl_trans.narrative) as narrative_gl_narrative"),
                            DB::raw("(select description from wa_numer_series_codes where code=SUBSTRING_INDEX(wa_banktrans.document_no, '-', 1)) as numseries_code")
                        )
                        ->groupBy(
                            'wa_banktrans.id',
                            'wa_banktrans.account',
                            'wa_banktrans.sub_account',
                            'wa_banktrans.trans_date',
                            'wa_banktrans.type_number',
                            'wa_banktrans.document_no',
                            'wa_banktrans.reference',
                            'wa_banktrans.amount'
                        );
                    if($date1!="" && $date2!=""){
                        $row->whereBetween('wa_banktrans.trans_date',[$date1.' 00:00:00',$date2.' 23:59:59']);
                    }
                    $clonerRow = $row->clone();
                    if ($request->filled('type')) {
                        $row->where('wa_banktrans.document_no','like',$request->type.'%');
                    }
                    if ($request->filled('narrative_type')) {
                        if ($request->narrative_type == 'Order Taking' || $request->narrative_type == 'Delivery') {
                            if ($request->narrative_type == 'Order Taking') {
                                $row->where('users.role_id',4);
                            } else {
                                $row->where('users.role_id','!=',4);
                            }
                        } else {
                            $row->where('pcrt.name',$request->narrative_type);
                        }
                        
                        $row->where('wa_banktrans.document_no','like',$request->type.'%');
                    }
                    
                    $row->orderBy('trans_date','ASC');
                    $row = $row->get()
                    ->map(function($data){
                        $narration = '';
                        if ($data->narration_account_name || 
                            $data->narration_type || 
                            $data->narration_payee_name || 
                            $data->narrative_role || 
                            $data->narrative_route || 
                            $data->narrative_user || $data->narration_payment_reason) {
                            $narrationParts = [];
                            
                           if ($data->narration_payment_reason) {
                            $narrationParts[] = $data->narration_payment_reason;
                           } else{
                            if ($data->narration_account_name) {
                                $narrationParts[] = $data->narration_account_name;
                            }
                            if ($data->narration_type) {
                                $narrationParts[] = $data->narration_type;
                            }
                            if ($data->narration_payee_name) {
                                $name = explode(' ', $data->narration_payee_name);
                                if (isset($name[0])) {
                                    $narrationParts[] = $name[0];
                                }
                            }
                           }
                            

                            if ($data->narrative_role) {
                                $role = (int)$data->narrative_role === 4 ? 'Order Taking' : 'Delivery';
                                $narrationParts[] = $role;
                            }
                            if ($data->narrative_route) {
                                $narrationParts[] = $data->narrative_route;
                            }
                            if ($data->narrative_user) {
                                $name = explode(' ', $data->narrative_user);
                                if (isset($name[0])) {
                                    $narrationParts[] = $name[0];
                                }
                            }
                           
                            $narration = implode(' / ', $narrationParts);
                        } else {
                            $narration = $data->narrative_gl_narrative;
                        }
                        

                        return [
                            'id' => $data->id,
                            'account' => $data->account,
                            'sub_account' => $data->sub_account,
                            'trans_date' => $data->trans_date,
                            'type_number' => $data->numseries_code,
                            'document_no' => $data->document_no,
                            'reference' => $data->reference,
                            'amount' => $data->amount,
                            'narration' => $narration,
                            'short_narration' => strlen($narration) > 40 ? substr($narration, 0, 40) : $narration
                        ];
                    });
                    
                    $title = $this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Account Inquiry'=>''];
                    $model =$this->model;


                    $getOpeningBlance = WaBanktran::select('*')->where('bank_gl_account_code',$gl_code);
                    if($date1!=""){
                        $getOpeningBlance->whereDate('trans_date', '<', $date1);
                    }
                    $getOpeningBlance = $getOpeningBlance->sum('amount');
                    
                    if($request->manage == 'pdf'){
                        $heading = $title;
                        $pdf = PDF::loadView('admin.bankaccounts.accountinquirypdf', compact('bank','row','title','heading','getOpeningBlance'));
                        // return $pdf->stream();
                        return $pdf->download('bank-accounts-'.date('Y-m-d-H-i-s').'.pdf');
                    }else if($request->manage == "excel"){

                        try {
                            $arrays = [];
                            $total_amount = [];
                            $credit_amount = [];
                            $debit_amount = [];
                             $OpeningBlance = $getOpeningBlance;
                             $arrays[] = [
                                'Date.' => '',
                                'Type' =>  '',
                                'Trans No' => '',
                                'Narration' => '',
                                // 'Parent Acc' => '',
                                // 'GL Acc' => '',
                                // 'Supplier Acc' => '',
                                'Particular' => '',
                                'Debit' => '',
                                'Credit' => '',
                                'Running Balance' =>  manageAmountFormat($getOpeningBlance),
                            ];
                            if (!empty($row)) {
                                // $accountsss = \App\Model\WaChartsOfAccount::get();
                                foreach ($row as $key => $list) {
                                    // $acGL = $accountsss->where('account_code',$list['account'])->first();
                                    // $acGL1 = $accountsss->where('account_code',$list['sub_account'])->first();


                                    $arrays[] = [
                                        'Date.' => date('d/M/Y',strtotime($list['trans_date'])),
                                        'Type' => $list['type_number'],//isset($number_series_list[$list['type_number']])?$number_series_list[$list['type_number']] : '',
                                        'Trans No' => $list['document_no'],
                                        'Narration' => $list['narration'],
                                        // 'Parent Acc' => $acGL1 ? $acGL1->account_name.'('.$list['account'].')' : NULL,
                                        // 'GL Acc' => @$acGL->account_name ?? NULL,
                                        // 'Supplier Acc' => @$acGL->supplier_account ?? NULL,
                                        'Particular' => $list['reference'],
                                        'Debit' => $list['amount'] > 0 ? @manageAmountFormat($list['amount']) : '-',
                                        'Credit' => $list['amount'] < 0 ? @manageAmountFormat(abs($list['amount'])) : '-',
                                        'Running Balance' => @manageAmountFormat($list['amount']+$OpeningBlance),
                                    ];


                                    $total_amount[] = $list['amount'];
                                    $credit_amount[] = $list['amount'] < 0 ? $list['amount'] : 0;
                                    $debit_amount[] = $list['amount'] > 0 ? $list['amount'] : 0;
                                    $OpeningBlance += $list['amount'];
                                }
                            }

                            $arrays[] = [
                                        'Date.' => '',
                                        'Type' =>  '',
                                        'Trans No' => '',
                                        'Narration' => '',
                                        // 'Parent Acc' => '',
                                        // 'GL Acc' => '',
                                        // 'Supplier Acc' => '',
                                        'Particular' => 'B/F : '.manageAmountFormat($getOpeningBlance),
                                        'Debit' => manageAmountFormat(array_sum($debit_amount)),
                                        'Credit' => manageAmountFormat(array_sum($credit_amount)),
                                        'Running Balance' =>  manageAmountFormat($OpeningBlance),
                                    ];

                                    $records = collect($arrays);
                                    $columns = ['Date.', 'Type','Trans No','Narration','Particular','Debit' ,'Credit','Running Balance',];
                                    return ExcelDownloadService::download('bank-accounts-'.date('Y-m-d-H-i-s'), $records, $columns);

                            // return \Excel::create('bank-accounts-'.date('Y-m-d-H-i-s'), function($excel) use ($arrays) {
                            //     $excel->sheet('mySheet', function($sheet) use ($arrays)
                            //     {                    
                            //         $sheet->fromArray($arrays);
                            //     });
                            // })->export('xls');            
                        } catch (\Exception $th) {
                            $request->session()->flash('danger','Something went wrong');
                            return redirect()->back();
                        }

                    }

                    $clonerRow->get()->map(function($data) use(&$bankTypesArr,&$narrationType){
                        $narration = '';
                        if ($data->narration_account_name || 
                            $data->narration_type || 
                            $data->narration_payee_name || 
                            $data->narrative_role || 
                            $data->narrative_route || 
                            $data->narrative_user) {
                            $narrationParts = [];
                            
                           
                            if ($data->narration_account_name) {
                                $narrationParts[] = $data->narration_account_name;
                            }
                            if ($data->narration_type) {
                                $narrationParts[] = $data->narration_type;
                                $narrationType[] = $data->narration_type;
                            }
                            if ($data->narration_payee_name) {
                                $name = explode(' ', $data->narration_payee_name);
                                if (isset($name[0])) {
                                    $narrationParts[] = $name[0];
                                }
                            }
                            if ($data->narrative_role) {
                                $role = (int)$data->narrative_role === 4 ? 'Order Taking' : 'Delivery';
                                $narrationParts[] = $role;
                                $narrationType[] = $role;
                            }
                            if ($data->narrative_route) {
                                $narrationParts[] = $data->narrative_route;
                            }
                            if ($data->narrative_user) {
                                $name = explode(' ', $data->narrative_user);
                                if (isset($name[0])) {
                                    $narrationParts[] = $name[0];
                                }
                            }
                           
                            $narration = implode(' / ', $narrationParts);
                        }

                        $bankTypesArr[]=$data->numseries_code;

                        return [
                            'id' => $data->id,
                            'account' => $data->account,
                            'sub_account' => $data->sub_account,
                            'trans_date' => $data->trans_date,
                            'type_number' => $data->numseries_code,
                            'document_no' => $data->document_no,
                            'reference' => $data->reference,
                            'amount' => $data->amount,
                            'narration' => $narration,
                        ];
                    });
                    $bankTypesArrU = array_unique($bankTypesArr);
                    $bankTypes = WaNumerSeriesCode::whereIn('description',$bankTypesArrU)->get();
                    $narrationTypeU = array_unique($narrationType);
                    

                    return view('admin.bankaccounts.accountinquiry',compact('title','model','breadcum','row','number_series_list','getOpeningBlance','banks','bankTypes','narrationTypeU')); 
                }
                else
                {
                    return view('admin.bankaccounts.accountinquiry',compact('title','model','breadcum','row','number_series_list','getOpeningBlance','banks','bankTypes','narrationTypeU')); 
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
    }


    public function __accountInquiry(Request $request,$slug)
    {
         $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $number_series_list = WaNumerSeriesCode::getNumberSeriesTypeList();
                $bank =  WaBankAccount::whereSlug($slug)->first();
               
                if($bank)
                {
                   
   	                $date1 = $request->get('from');
	                $date2 = $request->get('to');

                    $gl_code = $bank->getGlDetail?$bank->getGlDetail->account_code:'norecordfound';
                    $row = WaBanktran::where('bank_gl_account_code',$gl_code);
                    if($date1!="" && $date2!=""){
	                    $row->whereDate('trans_date', '>=', $date1)->whereDate('trans_date', '<=', $date2);	                    
                    }
                    $row->orderBy('trans_date','DESC');
                    $row = $row->get();
                    
//                    echo "<pre>"; print_r($row); die;
                    
                    $title = $this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Account Inquiry'=>''];
                    $model =$this->model;
                    return view('admin.bankaccounts.accountinquiry',compact('title','model','breadcum','row','number_series_list')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
    }


    public function store(Request $request)
    {
        try
        {
             $validator = Validator::make($request->all(), [
                'bank_account_gl_code_id' => 'required',
                'account_name' => 'required|max:255',
                'account_code' => 'required|max:255',
                'account_number' => 'required|max:255',
                'bank_address' => 'required|max:255',
                'currency' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
               
                $row = new WaBankAccount();
              
                $row->bank_account_gl_code_id= $request->bank_account_gl_code_id;
                $row->account_name= $request->account_name;
                $row->account_code= $request->account_code;
                $row->account_number= $request->account_number;
                $row->bank_address= $request->bank_address;
                $row->currency= $request->currency;
                $row->slug= strtotime(date('Y-m-d h:i:s')).'-'.rand(111,9999);

                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }

               
           
            
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {
        
    }


    public function edit($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  WaBankAccount::whereSlug($slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.bankaccounts.edit',compact('title','model','breadcum','row')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(Request $request, $slug)
    {
        try
        {
            $row =  WaBankAccount::whereSlug($slug)->first();
             $validator = Validator::make($request->all(), [
                 'bank_account_gl_code_id' => 'required',
                'account_name' => 'required|max:255',
                'account_code' => 'required|max:255',
                'account_number' => 'required|max:255',
                'bank_address' => 'required|max:255',
                'currency' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {

             
               $row->bank_account_gl_code_id= $request->bank_account_gl_code_id;
                $row->account_name= $request->account_name;
                $row->account_code= $request->account_code;
                $row->account_number= $request->account_number;
                $row->bank_address= $request->bank_address;
                $row->currency= $request->currency;
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try
        {
            
            WaBankAccount::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    
}
