<?php

namespace App\Http\Controllers\Admin;

use App\Model\WaCustomer;
use App\Models\ChequeBank;
use App\Services\ChequeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\RegisterCheque;
use Session;
use App\Model\WaLocationAndStore;
use App\Model\User;
class RegisterChequeController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected $status;

    public function __construct(Request $request)
    {
        $this->pmodule = 'cheque-management';
        $this->model = 'cheque-management';
        $this->status = 'Manage';
        $this->title = $this->status.' Cheques';
    }


    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        $start  = $request->get('date_from') ?? today()->toDateString();
        $end  = $request->get('date_to') ?? today()->toDateString();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $data = RegisterCheque::with(['salesman','depositer','customer'])
                ->whereIn('status',['Deposited','Registered'])
                ->orderBy('id','DESC')
                ->get();
            $others = RegisterCheque::with(['salesman','depositer','customer','bank'])
                ->whereIn('status',['Cleared','Bounced'])
                ->whereBetween('clearance_date', [$start,$end])
                ->orderBy('id','DESC')
                ->get();
            $bounced = $others->where('status','Bounced');
            $cleared = $others->where('status','Cleared');
            $deposited = $data->where('status','Deposited');
            $registered = $data->where('status','Registered');
            $ready = $data->where('status','Registered')->where('cheque_date','<=', today());

            return view('admin.register_cheque.index', compact('data','user','title', 'model', 'breadcum', 'pmodule', 'permission','bounced','cleared','deposited','registered','ready'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function create(Request $request)
    {
        if($request->source != 'register-cheque'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('register-cheque.index',['source'=>$request->source]);
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $customers = WaCustomer::where('is_invoice_customer', true)->get();
            $banks = ChequeBank::get();
            return view('admin.register_cheque.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission','customers','banks'));
        } else {
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
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $validation = \Validator::make($request->all(),[
                'source'=>'required|in:register-cheque',
                'cheque_no'=>'required|string|max:250',
                'drawers_name'=>'required|string|max:250',

                'drawers_bank'=>'required|string|max:250',
                'amount'=>'required|numeric|max:999999999999',
                'cheque_date'=>'required|date|date_format:Y-m-d',
                'salesman_id'=>'required|exists:wa_customers,id',
                'date_received'=>'required|date|date_format:Y-m-d',
                'cheque_image'=>'nullable|mimes:jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG'
            ],[],[]);

            if($validation->fails()){
                return response()->json([
                    'result'=>0,
                    'message'=>$validation->errors(),
                    'errors'=>$validation->errors()
                ]);
            }
            $customer = WaCustomer::with('route')->find($request->salesman_id);
            $user = getLoggeduserProfile();
            $new = new RegisterCheque;
            $new->cheque_no = $request->cheque_no;
            $new->drawers_name = $request->drawers_name;
            $new->drawers_bank = $request->drawers_bank;

            $new->amount = $request->amount;
            $new->cheque_date = $request->cheque_date;
            $new->salesman_id =null;
            $new->wa_customer_id = $request->salesman_id;
            $new->date_received = $request->date_received;
            $new->user_id = $user->id;
            $new->branch_id = $customer->route->restaurant_id;
            if($request->hasFile('cheque_image')){

                $file = $request->file('cheque_image');
                $destinationPath = public_path('/uploads/cheque_images/');
                $filename = 'cheque-image'.time().'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath,$filename);
                $new->cheque_image = '/uploads/cheque_images/'.$filename;
            }
            $new->save();
            $chequeService = new ChequeService();
            $chequeService->add($new);
            return response()->json([
                'result'=>1,
                'message'=>'Cheque registered successfully',
                'location'=>route($this->model.'.index')
            ]);
        }
        else{
            return response()->json([
                'result'=>-1,
                'message'=>'Un-Authorized! You don\'t have permission',
                'location'=>route($this->model.'.index')
            ]);
        }
    }
    public function edit(Request $request,$id)
    {
        if($request->source != 'register-cheque'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('register-cheque.index',['source'=>$request->source]);
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
                $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
                $locations = WaLocationAndStore::get();
                $customers = WaCustomer::where('is_invoice_customer', true)->get();
                $data = RegisterCheque::where('id',$id)->where('status','Registered')->first();
                $data->id;
                $banks = ChequeBank::get();
                return view('admin.register_cheque.edit', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission','customers','banks'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }
    public function update(Request $request,$id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
            $validation = \Validator::make($request->all(),[
                'source'=>'required|in:register-cheque',
                'id'=>'required|exists:register_cheque,id,status,Registered',
                'cheque_no'=>'required|string|max:250',
                'drawers_name'=>'required|string|max:250',

                'drawers_bank'=>'required|string|max:250',
                'amount'=>'required|numeric|max:999999999999',
                'cheque_date'=>'required|date|date_format:Y-m-d',
                'salesman_id'=>'required|exists:wa_customers,id',
                'date_received'=>'required|date|date_format:Y-m-d',
                'cheque_image'=>'nullable|mimes:jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG'
            ],[],[]);

            if($validation->fails()){
                return response()->json([
                    'result'=>0,
                    'message'=>$validation->errors(),
                    'errors'=>$validation->errors()
                ]);
            }
            $user = getLoggeduserProfile();
            $new = RegisterCheque::where('id',$request->id)->where('status','Registered')->first();
            $new->cheque_no = $request->cheque_no;
            $new->drawers_name = $request->drawers_name;

            $new->drawers_bank = $request->drawers_bank;
            $new->amount = $request->amount;
            $new->cheque_date = $request->cheque_date;
            $new->salesman_id = null;
            $new->wa_customer_id = $request->salesman_id;
            $new->date_received = $request->date_received;
            $new->user_id = $user->id;
            if($request->hasFile('cheque_image')){
                $file = $request->file('cheque_image');
                $destinationPath = public_path('/uploads/cheque_images/');
                $filename = 'cheque-image'.time().'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath,$filename);
                $new->cheque_image = '/uploads/cheque_images/'.$filename;
            }
            $new->save();
            return response()->json([
                'result'=>1,
                'message'=>'Cheque updated successfully',
                'location'=>route($this->model.'.index')
            ]);
        }
        else{
            return response()->json([
                'result'=>-1,
                'message'=>'Un-Authorized! You don\'t have permission',
                'location'=>route($this->model.'.index')
            ]);
        }
    }

    public function show(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        try {
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') {
                $new = RegisterCheque::where('id',$id)->where('status','Registered')->first();
                $new->delete();
                return response()->json([
                    'result'=>1,
                    'message'=>'Cheque deleted successfully',
                    'location'=>route($this->model.'.index')
                ]);
            }
            throw new Exception("Error Processing Request");
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,
                'message'=>$th->getMessage(),
                'location'=>route($this->model.'.index')
            ]);
        }
    }

    public function deposit_cheque(Request $request, $id)
    {
        if($request->source != 'register-cheque'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('register-cheque.index',['source'=>$request->source]);
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (isset($permission[$pmodule . '___deposit-cheque']) || $permission == 'superadmin') {
                $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
                $users = User::get();
                $data = RegisterCheque::where('id',$id)->where('status','Registered')->first();
                $title = 'Deposit Cheque : '.$data->cheque_no;
                $banks = ChequeBank::get();
                return view('admin.register_cheque.deposit_cheque', compact('data','title', 'model', 'breadcum', 'pmodule', 'permission','users','banks'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function deposit_cheque_update(Request $request, $id)
    {
        if($request->source != 'register-cheque'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('register-cheque.index',['source'=>$request->source]);
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            $title = 'Deposit Cheque';
            if (isset($permission[$pmodule . '___deposit-cheque']) || $permission == 'superadmin') {
                $validation = \Validator::make($request->all(),[
                    'source'=>'required|in:register-cheque',
                    'id'=>'required|exists:register_cheque,id,status,Registered',
                    'deposited_date'=>'required|date|date_format:Y-m-d',
                    'deposited_by'=>'required|exists:users,id',
                    'bank_deposited'=>'required|string|max:250',
                ],[],[]);

                if($validation->fails()){
                    return response()->json([
                        'result'=>0,
                        'message'=>$validation->errors(),
                        'errors'=>$validation->errors()
                    ]);
                }

                $user = getLoggeduserProfile();
                $new = RegisterCheque::where('id',$request->id)->where('status','Registered')->first();
                $new->bank_deposited = $request->bank_deposited;
                $new->deposited_date = $request->deposited_date;
                $new->deposited_by = $request->deposited_by;
                $new->status = 'Deposited';

                $new->save();
                return response()->json([
                    'result'=>1,
                    'message'=>'Cheque Deposited successfully',
                    'location'=>route($this->model.'.index')
                ]);
            }
            else{
                return response()->json([
                    'result'=>-1,
                    'message'=>'Un-Authorized! You don\'t have permission',
                    // 'location'=>route($this->model.'.index')
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,
                'message'=>$th->getMessage(),
                // 'location'=>route($this->model.'.index')
            ]);
        }
    }

    public function deposit_cheque_update_status(Request $request, $id)
    {
        if($request->source != 'deposit-cheque'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('deposit-cheque.index',['source'=>$request->source]);
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            $title = 'Deposit Cheque';
            if (isset($permission[$pmodule . '___update-status']) || $permission == 'superadmin') {
                $validation = \Validator::make($request->all(),[
                    'source'=>'required|in:deposit-cheque',
                    'id'=>'required|exists:register_cheque,id,status,Deposited',
                    'status'=>'required|in:Cleared,Bounced',
                    'clearance_date'=>'nullable|required_if:status,==,Cleared|date|date_format:Y-m-d'
                ],[],[]);

                if($validation->fails()){
                    return response()->json([
                        'result'=>0,
                        'message'=>$validation->errors(),
                        'errors'=>$validation->errors()
                    ]);
                }

                $user = getLoggeduserProfile();
                $new = RegisterCheque::where('id',$request->id)->where('status','Deposited')->first();
                $service = New ChequeService();
                $service->clear($new, $request->status);

                return response()->json([
                    'result'=>1,
                    'message'=>'Cheque '.$request->status.' successfully',
                    'location'=>route('deposit-cheque.index',['source'=>$request->source])
                ]);
            }
            else{
                return response()->json([
                    'result'=>-1,
                    'message'=>'Un-Authorized! You don\'t have permission',
                    // 'location'=>route('deposit-cheque.index',['source'=>$request->source])
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,
                'message'=>$th->getMessage(),
            ]);
        }
    }
    public function bounced_cheque_transfer(Request $request,$id)
    {

        if($request->source != 'bounced-cheque'){
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('bounced-cheque.index',['source'=>$request->source]);
        }
        try {
            $permission =  $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            $title = 'Deposit Cheque';
            if (isset($permission[$pmodule . '___transfer']) || $permission == 'superadmin') {
                $validation = \Validator::make($request->all(),[
                    'source'=>'required|in:bounced-cheque',
                    'id'=>'required|exists:register_cheque,id,status,Bounced,is_bounced_transfer,0',
                ],[],[]);

                if($validation->fails()){
                    return response()->json([
                        'result'=>0,
                        'message'=>$validation->errors(),
                        'errors'=>$validation->errors()
                    ]);
                }
                $check = \DB::transaction(function() use ($id, $request){

                    $user = getLoggeduserProfile();
                    $new = RegisterCheque::where('id',$id)->where('status','Bounced')->first();
                    $new->is_bounced_transfer = 1;
                    $new->save();
                    $series_module = \App\Model\WaNumerSeriesCode::where('module','CQ')->first();
//                    $location = \App\Model\WaLocationAndStore::where('id',$new->salesman_id)->first();
//                    $getUserData = \App\Model\User::where('wa_location_and_store_id',$new->salesman_id)->first();
                    $bank = ChequeBank::find($new->bank_deposited);
                    $fine  = $bank->bounce_penalty;
                    $customer = \App\Model\WaCustomer::find($new ->salesman_id);
                    $grn_number = getCodeWithNumberSeries('CQ');
                    updateUniqueNumberSeries('CQ', $grn_number);
                    $WaAccountingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
                    $dateTime = date('Y-m-d H:i:s');
//                    $WaDebtorTran[] = [
////                        'salesman_id'=>$new->salesman_id,
////                        'salesman_user_id'=> @$getUserData->id,
//                        'type_number'=>$series_module->type_number,
//                        'wa_customer_id'=>@$customer->id,
//                        'customer_number'=>@$customer->customer_code,
//                        'invoice_customer_name'=>@$customer->customer_name,
//                        'trans_date'=>$dateTime,
//                        'input_date'=>$dateTime,
//                        'wa_accounting_period_id'=>$WaAccountingPeriod ? $WaAccountingPeriod->id : null,
//                        'shift_id'=>NULL,
//                        'reference'=>$new->drawers_bank.'/Bounced Cheque : '.$new->cheque_no,
//                        'amount'=> $new->amount,
//                        'document_no'=>$grn_number,
//                        'route_id'=>$customer->route_id,
//                         'updated_at'=>date('Y-m-d H:i:s'),
//                         'created_at'=>date('Y-m-d H:i:s'),
//                         'register_cheque_id'=>$new->id
//                    ];
                    $WaDebtorTran[] = [
//                        'salesman_id'=>$new->salesman_id,
//                        'salesman_user_id'=> @$getUserData->id,
                        'type_number'=>$series_module->type_number,
                        'wa_customer_id'=>@$customer->id,
                        'customer_number'=>@$customer->customer_code,
                        'invoice_customer_name'=>@$customer->customer_name,
                        'trans_date'=>$dateTime,
                        'input_date'=>$dateTime,
                        'wa_accounting_period_id'=>$WaAccountingPeriod ? $WaAccountingPeriod->id : null,
                        'shift_id'=>NULL,
                        'reference'=> $new->drawers_bank.'/Bounced Cheque : '.$new->cheque_no.' bank Charge',
                        'amount'=>$fine,
                        'document_no'=>$grn_number,
                        'route_id'=>$customer->route_id,
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'created_at'=>date('Y-m-d H:i:s'),
                        'register_cheque_id'=>$new->id
                    ];
//                    dd($WaDebtorTran);
                    if(count($WaDebtorTran)>0){
                        \App\Model\WaDebtorTran::insert($WaDebtorTran);
                    }
                    if(count($WaDebtorTran)>0){
                        \App\Model\WaSalesmanTran::insert($WaDebtorTran);
                    }
                    return true;
                });
                if ($check) {
                    return response()->json([
                        'result'=>1,
                        'message'=>'Cheque '.$request->status.' successfully',
                        'location'=>route('bounced-cheque.index',['source'=>$request->source])
                    ]);
                }
                throw new Exception("Error Processing Request");

            }
            else{
                return response()->json([
                    'result'=>-1,
                    'message'=>'Un-Authorized! You don\'t have permission',
                    // 'location'=>route('deposit-cheque.index',['source'=>$request->source])
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,
                'message'=>$th->getMessage(),
                // 'location'=>route('deposit-cheque.index',['source'=>$request->source])
            ]);
        }

    }

    public function report(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'cheque-report';
        $title = 'Cheque Report';
        $model = 'cheque-report';
        $user = getLoggeduserProfile();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route('register-cheque.report'), 'Listing' => ''];

            $data = RegisterCheque::with(['salesman','depositer'])->where(function($e) use ($request){
                if($request->status && $request->status != 'All'){
                    $e->where('status',$request->status);
                }
            })->where(function($e) use ($request){
                if($request->from || $request->to){
                    $e->whereBetween('date_received',[$request->from,$request->to]);
                }else{
                    $e->where('date_received',date('Y-m-d'));
                }
            })->orderBy('id','DESC');
            if($request->manage == 'pdf'){
                $data = $data->get();
                $pdf = \PDF::loadView('admin.register_cheque.reportpdf', compact('data','user','title', 'model', 'breadcum', 'pmodule', 'permission'))->setPaper('A4', 'landscape');
                $report_name = 'pcheque_report_'.date('Y_m_d_H_i_A');
                return $pdf->download($report_name.'.pdf');
            }
            $data = $data->paginate(20);


            return view('admin.register_cheque.report', compact('data','user','title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

}
