<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Model\Restaurant;
use App\Model\WaDebtorTran;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Interfaces\Finance\BankReconciliationInterface;
use App\Exports\Finance\DailyReconciliationExport;
use Excel;

class WaBankingController extends Controller
{
    private BankReconciliationInterface $bankReconRepository;

    public function __construct(BankReconciliationInterface $bankReconRepository) {
        $this->bankReconRepository = $bankReconRepository;
    }

    public function modulePermissions($type)
    {
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission['bank-accounts___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;

    }
    public function transferList(Request $request)
    {
        if(!$this->modulePermissions('banktransfer')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = [
                'wa_bank_transfers.id',
                'from.account_name',
                'to.account_name',
                'wa_bank_transfers.date',
                'wa_bank_transfers.amount',
                'wa_bank_transfers.memo',
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaBankTransfer::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['date'] = getDateFormatted($re['date']);
                $data[$key]['amount'] = manageAmountFormat($re['amount']);
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $data['model'] = 'genralLedger-bank-transfer';
        $data['title'] = 'Bank Transfer';
        $data['pmodule'] = 'bank-transfer';
        $data['permission'] =  $this->mypermissionsforAModule();
        
        return view('admin.banking.transfer.list')->with($data);
    }

    public function transferNew()
    {
        if(!$this->modulePermissions('banktransfer')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bank-transfer';
        $data['title'] = 'Bank Transfer';
        $data['pmodule'] = 'bank-transfer';
        return view('admin.banking.transfer.new')->with($data);
    }

    public function transfer_report_download(Request $request)
    {
        if(!$this->modulePermissions('banktransfer')){
            return redirect()->back();
        }
        $response       = \App\Model\WaBankTransfer::getData(10000000, 0 , '', NULL, NULL,$request);   
        $data['data'] = $response['response'];        
        $data['title'] = 'FUND TRANSFER-Report';
        $data['heading'] = 'FUND TRANSFER';
        // return view('admin.expenses.pdf', $data)->with($data);
        $pdf = \PDF::loadView('admin.banking.transfer.pdf', $data)->setPaper('a4','landscape');
        // return $pdf;
        // return $pdf->stream();
        return $pdf->download('Fund-Transfer-Report-'.time().'.pdf');
    }
    public function deposite_report_download(Request $request)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return redirect()->back();
        }
        if(!isset($request->processed) || $request->processed == 0){
            \Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $response       = \App\Model\WaBankDeposit::getData(10000000, 0 , '', NULL, NULL,$request);   
        $data['data'] = $response['response'];        
        $data['title'] = 'Deposit-Report';
        $data['heading'] = 'Deposit';
        // return view('admin.expenses.pdf', $data)->with($data);
        $pdf = \PDF::loadView('admin.banking.deposite.pdf', $data)->setPaper('a4','landscape');
        // return $pdf;
        // return $pdf->stream();
        return $pdf->download('Deposit-Report-'.time().'.pdf');
    }
    public function depositeList(Request $request)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = [
                'wa_bank_deposits.id',
                'wa_charts_of_accounts.account_name',
                'restaurants.name',
                'wa_bank_transfers.total',
                'wa_bank_transfers.date',
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaBankDeposit::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '';
                if($data[$key]['is_processed'] == 0){
                    $data[$key]['links'] = '<a href="'.route('banking.deposite.edit',['id'=>$re['id']]).'"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>';
                }else
                {
                    $data[$key]['links'] = '<a href="'.route('banking.deposite.show',['id'=>$re['id']]).'"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }

                $data[$key]['date'] = getDateFormatted($re['date']);
                $data[$key]['amount'] = manageAmountFormat($re['total']);
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $data['model'] = 'genralLedger-bank-deposite';
        $data['title'] = 'Bank deposit';
        $data['pmodule'] = 'bank-deposite';
        $data['permission'] =  $this->mypermissionsforAModule();
        return view('admin.banking.deposite.list')->with($data);
    }
    public function depositeNew()
    {
        if(!$this->modulePermissions('bankdeposit')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bank-deposite';
        $data['title'] = 'Bank deposit';
        $data['pmodule'] = 'bank-deposite';
        return view('admin.banking.deposite.new')->with($data);
    }

    public function transferAccountGet(Request $request)
    {
        $gl = \DB::table('wa_charts_of_accounts')->where('id',$request->id)->first();
        $data['amount'] = manageAmountFormat(\DB::table('wa_banktrans')->where('bank_gl_account_code',$gl->account_code)->sum('amount'));
        return response()->json($data);
    }
    public function validations()
    {
        $inputArray = [               
            'transfer_from' => 'required|exists:wa_charts_of_accounts,id',
            'transfer_to' => 'required|exists:wa_charts_of_accounts,id|different:transfer_from',
            'amount'=>'required|numeric|digits_between:1,100|min:1',
            'memo'=>'nullable|string',
            'date'=>'required|date',
        ];       
        return  $inputArray;
    }
    public function transferStore(Request $request)
    {
        if(!$this->modulePermissions('banktransfer')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $validator = Validator::make($request->all(),$this->validations() );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        $check = \DB::transaction(function () use ($request) {
            $expense = new \App\Model\WaBankTransfer();
            $expense->transfer_from = $request->transfer_from;
            $expense->transfer_to = $request->transfer_to;
            $expense->amount = $request->amount;
            $expense->memo = $request->memo;
            $expense->date = $request->date;
            $expense->save();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','TRANSFER_AMOUNT')->first();
            $inID = "TRN-".str_pad($expense->id, 6, "0", STR_PAD_LEFT);
            $expense =  \App\Model\WaBankTransfer::find($expense->id);
            //transfer from
            $btran = new \App\Model\WaBanktran;
            $btran->type_number = $series_module ? $series_module->type_number : NULL;
            $btran->document_no = $inID;
            $btran->bank_gl_account_code = $expense->transfer_from_account->account_code;
            $btran->reference =  $inID;
            $btran->trans_date = $request->date;
            $btran->wa_payment_method_id = 1;
            $btran->amount = '-'.$expense->amount;
            $btran->wa_curreny_id = 1;
            $btran->save();

            //transfer to
            $btran1 = new \App\Model\WaBanktran;
            $btran1->type_number = $series_module ? $series_module->type_number : NULL;
            $btran1->document_no = $inID;
            $btran1->bank_gl_account_code = $expense->transfer_to_account->account_code;
            $btran1->reference =  $inID;
            $btran1->trans_date = $request->date;
            $btran1->wa_payment_method_id = 1;
            $btran1->amount = $expense->amount;
            $btran1->wa_curreny_id = 1;
            $btran1->save();
            return true;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'fund transfer successfully',
                'location'=>route('banking.transfer.list')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function deposit_save(Request $request)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $inputArray = [               
            'tax_check'=>'required|in:'.implode(',',array_keys(tax_amount_type())),
            'type'=>'required|in:'.implode(',',array_keys(receivedType())),
            'payment_account' => 'required|exists:wa_charts_of_accounts,id',
            'branch' => 'required|exists:restaurants,id',
            'date'=>'required|date',
            'memo'=>'nullable|string',

            'receiver' => 'required|array',
            'account' => 'nullable|array',
            'description' => 'nullable|array',
            'payment_method' => 'nullable|array',
            'ref_no' => 'nullable|array',
            'amount' => 'nullable|array',
            'vat_list' => 'nullable|array',
        ];
        if(isset($request->receiver)){
            foreach($request->receiver as $key => $val)
            {
                $inputArray['receiver.'.$key] = 'required|min:1';
                if($val != ''){
                    if($request->type == 'Customer')
                    {
                        $inputArray['receiver.'.$key] .= '|exists:wa_customers,id';
                    }else
                    {
                        $inputArray['receiver.'.$key] .= '|exists:wa_suppliers,id';
                    }
                    $inputArray['account.'.$key] = 'required|min:1|exists:wa_charts_of_accounts,id';
                    $inputArray['description.'.$key] = 'required|min:1';
                    $inputArray['payment_method.'.$key] = 'required|min:1|exists:payment_methods,id';
                    $inputArray['ref_no.'.$key] = 'required|min:1|unique:wa_bank_deposit_categories,ref_no';
                    $inputArray['amount.'.$key] = 'required|numeric|min:1';
                    $inputArray['vat_list.'.$key] = 'required_if:tax_check,==,Inclusive,Exclusive|exists:tax_managers,id|min:1';
                }
            }
        }else
        {
            $inputArray['receivers']='required';
        }
        // dd($request->all());
       
        $validator = Validator::make($request->all(),$inputArray,$this->bank_messages(),
            [
                'account.*' => 'account',
                'receiver.*' => 'receiver',
                'description.*' => 'description',
                'payment_method.*' => 'payment method',
                'ref_no.*' => 'ref no',
                'amount.*' => 'amount',
                'vat_list.*' => 'vat list'
            ] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        $check = \DB::transaction(function () use ($request){
            $parent = new \App\Model\WaBankDeposit;
            $parent->payment_account_id = $request->payment_account;
            $parent->branch_id = $request->branch;
            $parent->date = $request->date;
            $parent->memo = $request->memo;
            $parent->is_processed = 0;
            $parent->tax_check = tax_amount_type()[$request->tax_check];
            $parent->receiver_type = receivedType()[$request->type];
            $total = 0;
            $subTotal = 0;
            $parent->save();
            if(isset($request->receiver)){
                foreach($request->receiver as $key => $val)
                {
                    if($val != ''){
                        $item = new \App\Model\WaBankDepositCategory;
                        $item->wa_bank_deposite_id = $parent->id;
                        $item->receiver_type =  receivedType()[$request->type];
                        $item->received_from_id = $request->receiver[$key];
                        $item->account_id = $request->account[$key];
                        $item->description = $request->description[$key];
                        $item->payment_method_id = $request->payment_method[$key];
                        $item->ref_no = $request->ref_no[$key];
                        $vatAm = 0;
                        if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive']) ){
                            $item->vat_id = $request->vat_list[$key];
                            $vat = \DB::table('tax_managers')->where('id',$item->vat_id)->first();
                            $vatAm =  $vat->tax_value;
                        }
                        $item->tax_percent = $vatAm;
                        if($request->tax_check == 'Inclusive'){
                            $item->total = $request->amount[$key];
                            $item->amount = $request->amount[$key] - (($request->amount[$key]*$vatAm)/100);
                        }else{
                            $item->amount = $request->amount[$key];
                            $item->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                        }
                        $subTotal +=  $item->amount;
                        $total +=  $item->total;
                        $item->save();
                    }
                }
            }
            $parent->total = $total;
            $parent->sub_total = $subTotal;
            $parent->save();
            return $parent;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bank Deposit added successfully',
                'location'=>route('banking.deposite.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function deposit_edit($id)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bank-deposite';
        $data['title'] = 'Bank deposit';
        $data['pmodule'] = 'bank-deposite';
        $data['data'] = \App\Model\WaBankDeposit::with(['account','branch','categories'])->where('is_processed',0)->findOrFail($id);
        return view('admin.banking.deposite.edit')->with($data);
    }
    public function deposit_show($id)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bank-deposite';
        $data['title'] = 'Bank deposit';
        $data['pmodule'] = 'bank-deposite';
        $data['data'] = \App\Model\WaBankDeposit::with(['account','branch','categories'])->where('is_processed',1)->findOrFail($id);
        return view('admin.banking.deposite.show')->with($data);
    }
    public function bank_messages()
    {
        
        $messages= [
            'account.*.required' => 'This field is required',
            'account.*.exists' => 'Invalid data provided',
            'receiver.*.required' => 'This field is required',
            'receiver.*.exists' => 'Invalid data provided',
            'payment_method.*.required' => 'This field is required',
            'payment_method.*.exists' => 'Invalid data provided',
            'description.*.*' => 'This field is required',

            'ref_no.*.required' => 'This field is required',
            'ref_no.*.unique' => 'This ref no is already exists',
            'amount.*.required' => 'This field is required',
            'amount.*.numeric' => 'Invalid data provided',
            'vat_list.*.exists' => 'Invalid data provided',
            'vat_list.*.required_if' => 'This field is required',
            'vat_list.*.required' => 'This field is required',
            'receivers.required'=>'Recerivers are required'
        ];
        return $messages;
    }
    public function deposit_update(Request $request,$id)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $inputArray = [               
            'id'=>'required|exists:wa_bank_deposits,id,is_processed,0',
            'tax_check'=>'required|in:'.implode(',',array_keys(tax_amount_type())),
            'type'=>'required|in:'.implode(',',array_keys(receivedType())),
            'payment_account' => 'required|exists:wa_charts_of_accounts,id',
            'branch' => 'required|exists:restaurants,id',
            'date'=>'required|date',
            'memo'=>'nullable|string',

            'receiver' => 'required|array',
            'account' => 'nullable|array',
            'description' => 'nullable|array',
            'payment_method' => 'nullable|array',
            'ref_no' => 'nullable|array|distinct',
            'amount' => 'nullable|array',
            'vat_list' => 'nullable|array',

          
        ];
        if(isset($request->receiver)){
            foreach($request->receiver as $key => $val)
            {
                $inputArray['receiver.'.$key] = 'required|min:1';
                if($val != ''){
                    if($request->type == 'Customer')
                    {
                        $inputArray['receiver.'.$key] .= '|exists:wa_customers,id';
                    }else
                    {
                        $inputArray['receiver.'.$key] .= '|exists:wa_suppliers,id';
                    }
                    $inputArray['account.'.$key] = 'required|exists:wa_charts_of_accounts,id|min:1';
                    $inputArray['description.'.$key] = 'required';
                    $inputArray['payment_method.'.$key] = 'required|exists:payment_methods,id|min:1';
                    $idK = '';
                    if(isset($request->category_id[$key])){
                        $idK = ','.$request->category_id[$key];
                    }
                    $inputArray['ref_no.'.$key] = 'required|min:1|unique:wa_bank_deposit_categories,ref_no'.$idK;
                    $inputArray['amount.'.$key] = 'required|min:1|numeric';
                    $inputArray['vat_list.'.$key] = 'required_if:tax_check,==,Inclusive,Exclusive|exists:tax_managers,id|min:1';
                }
            }
        }else
        {
            $inputArray['receivers']='required';
        }
        
        // dd($this->bank_messages());

        $validator = Validator::make($request->all(),$inputArray,$this->bank_messages(),
            [
                'account.*' => 'account',
                'receiver.*' => 'receiver',
                'description.*' => 'description',
                'payment_method.*' => 'payment method',
                'ref_no.*' => 'ref no',
                'amount.*' => 'amount',
                'vat_list.*' => 'vat list'
            ] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        $check = \DB::transaction(function () use ($request,$id){
            $parent = \App\Model\WaBankDeposit::find($id);
            $parent->payment_account_id = $request->payment_account;
            $parent->branch_id = $request->branch;
            $parent->date = $request->date;
            $parent->memo = $request->memo;
            $parent->is_processed = 0;
            $parent->tax_check = tax_amount_type()[$request->tax_check];
            $parent->receiver_type = receivedType()[$request->type];
            $total = 0;
            $subTotal = 0;
            $parent->save();
            if(isset($request->receiver)){
                \App\Model\WaBankDepositCategory::where('wa_bank_deposite_id',$parent->id)->delete();
                foreach($request->receiver as $key => $val)
                {
                    if($val != ''){
                        $item = new \App\Model\WaBankDepositCategory;
                        $item->wa_bank_deposite_id = $parent->id;
                        $item->receiver_type =  receivedType()[$request->type];
                        $item->received_from_id = $request->receiver[$key];
                        $item->account_id = $request->account[$key];
                        $item->description = $request->description[$key];
                        $item->payment_method_id = $request->payment_method[$key];
                        $item->ref_no = $request->ref_no[$key];
                        $vatAm = 0;
                        if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive']) ){
                            $item->vat_id = $request->vat_list[$key];
                            $vat = \DB::table('tax_managers')->where('id',$item->vat_id)->first();
                            $vatAm =  $vat->tax_value;
                        }
                        $item->tax_percent = $vatAm;
                        if($request->tax_check == 'Inclusive'){
                            $item->total = $request->amount[$key];
                            $item->amount = $request->amount[$key] - (($request->amount[$key]*$vatAm)/100);
                        }else{
                            $item->amount = $request->amount[$key];
                            $item->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                        }
                        $subTotal +=  $item->amount;
                        $total +=  $item->total;
                        $item->save();
                    }
                }
            }
            $parent->total = $total;
            $parent->sub_total = $subTotal;
            $parent->save();
            return $parent;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bank Deposit updated successfully',
                'location'=>route('banking.deposite.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function deposit_process(Request $request,$id)
    {
        if(!$this->modulePermissions('bankdeposit')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $inputArray = [               
            'id'=>'required|exists:wa_bank_deposits,id,is_processed,0',
            'tax_check'=>'required|in:'.implode(',',array_keys(tax_amount_type())),
            'type'=>'required|in:'.implode(',',array_keys(receivedType())),
            'payment_account' => 'required|exists:wa_charts_of_accounts,id',
            'branch' => 'required|exists:restaurants,id',
            'date'=>'required|date',
            'memo'=>'nullable|string',

            'receiver' => 'required|array',
            'account' => 'nullable|array',
            'description' => 'nullable|array',
            'payment_method' => 'nullable|array',
            'ref_no' => 'nullable|array|distinct',
            'amount' => 'nullable|array',
            'vat_list' => 'nullable|array',

          
        ];
        if(isset($request->receiver)){
            foreach($request->receiver as $key => $val)
            {
                $inputArray['receiver.'.$key] = 'required|min:1';
                if($val != ''){
                    if($request->type == 'Customer')
                    {
                        $inputArray['receiver.'.$key] .= '|exists:wa_customers,id';
                    }else
                    {
                        $inputArray['receiver.'.$key] .= '|exists:wa_suppliers,id';
                    }
                    $inputArray['account.'.$key] = 'required|exists:wa_charts_of_accounts,id|min:1';
                    $inputArray['description.'.$key] = 'required';
                    $inputArray['payment_method.'.$key] = 'required|exists:payment_methods,id|min:1';
                    $idK = '';
                    if(isset($request->category_id[$key])){
                        $idK = ','.$request->category_id[$key];
                    }
                    $inputArray['ref_no.'.$key] = 'required|min:1|unique:wa_bank_deposit_categories,ref_no'.$idK;
                    $inputArray['amount.'.$key] = 'required|numeric|min:1';
                    $inputArray['vat_list.'.$key] = 'required_if:tax_check,==,Inclusive,Exclusive|exists:tax_managers,id|min:1';
                }
            }
        }else
        {
            $inputArray['receivers']='required';
        }
        
        // dd($this->bank_messages());

        $validator = Validator::make($request->all(),$inputArray,$this->bank_messages(),
            [
                'account.*' => 'account',
                'receiver.*' => 'receiver',
                'description.*' => 'description',
                'payment_method.*' => 'payment method',
                'ref_no.*' => 'ref no',
                'amount.*' => 'amount',
                'vat_list.*' => 'vat list'
            ] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        $check = \DB::transaction(function () use ($request,$id){
            $parent = \App\Model\WaBankDeposit::find($id);
            $parent->payment_account_id = $request->payment_account;
            $parent->branch_id = $request->branch;
            $parent->date = $request->date;
            $parent->memo = $request->memo;
            $parent->is_processed = 1;
            $parent->tax_check = tax_amount_type()[$request->tax_check];
            $parent->receiver_type = receivedType()[$request->type];
            $total = 0;
            $subTotal = 0;
            $parent->save();

            $dataA = [];
            $series_module = \App\Model\WaNumerSeriesCode::where('module','BANK_DEPOSIT')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
           
            if(isset($request->receiver)){
                \App\Model\WaBankDepositCategory::where('wa_bank_deposite_id',$parent->id)->delete();
                foreach($request->receiver as $key => $val)
                {
                    if($val != ''){
                        $item = new \App\Model\WaBankDepositCategory;
                        $item->wa_bank_deposite_id = $parent->id;
                        $item->receiver_type =  receivedType()[$request->type];
                        $item->received_from_id = $request->receiver[$key];
                        $item->account_id = $request->account[$key];
                        $item->description = $request->description[$key];
                        $item->payment_method_id = $request->payment_method[$key];
                        $item->ref_no = $request->ref_no[$key];
                        $vatAm = 0;
                        if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive']) ){
                            $item->vat_id = $request->vat_list[$key];
                            $vat = \DB::table('tax_managers')->where('id',$item->vat_id)->first();
                            $vatAm =  $vat->tax_value;
                        }
                        $item->tax_percent = $vatAm;
                        if($request->tax_check == 'Inclusive'){
                            $item->total = $request->amount[$key];
                            $item->amount = $request->amount[$key] - (($request->amount[$key]*$vatAm)/100);
                        }else{
                            $item->amount = $request->amount[$key];
                            $item->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                        }
                       
                        $subTotal +=  $item->amount;
                        $total +=  $item->total;
                        $item->save();
                        $account = \DB::table('wa_charts_of_accounts')->where('id',$request->account[$key])->first();
                        $dataA[] = (Object)[
                            'banking_expense_id'=>$item->id,
                            'banking_expense_type'=>'bank_deposit_category',
                            'transaction_type'=>$series_module->description,
                            'transaction_no'=>$series_module->code.str_pad($item->id,6, "0", STR_PAD_LEFT),
                            'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                            'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                            'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                            'type'=>'CREDIT',
                            'description'=>'',
                            'ref_no'=> $request->ref_no[$key],
                            'account'=>$account ? $account->account_code : NULL,
                            'amount'=> $total,
                            'customer_id' => $request->receiver[$key],
                        ];
                        if($item->receiver_type == 'Customer'){
                            $debtorTran = new \App\Model\WaDebtorTran();
                            $debtorTran->type_number =  $series_module ? $series_module->type_number : '';
                            $debtorTran->wa_customer_id = $request->receiver[$key];
                            $customer = \DB::table('wa_customers')->where('id',$request->receiver[$key])->first();
                            $debtorTran->customer_number = $customer ? $customer->customer_code : NULL;
                            $debtorTran->trans_date = $parent->date;
                            $debtorTran->input_date = date('Y-m-d H:i:s');
                            $debtorTran->wa_accounting_period_id = $accountuingPeriod ? $accountuingPeriod->id : null;
                            $debtorTran->amount = '-'.$item->total;
                            $debtorTran->document_no = $request->ref_no[$key];
                            $debtorTran->reference = $request->ref_no[$key];
                            $debtorTran->save();
                        }
                    }
                }
            }
            $parent->total = $total;
            $parent->sub_total = $subTotal;
            //BANK AND GL DEBIT
            $account = \DB::table('wa_charts_of_accounts')->where('id',$request->payment_account)->first();
            $dataA[] = (Object)[
                'banking_expense_id'=>$parent->id,
                'banking_expense_type'=>'bank_deposit',
                'transaction_type'=>$series_module->description,
                'transaction_no'=>$series_module->code.str_pad($parent->id,6, "0", STR_PAD_LEFT),
                'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                'type'=>'DEBIT',
                'description'=>'',
                'ref_no'=>'',
                'account'=>$account ? $account->account_code : NULL,
                'amount'=> $total,
            ];
            foreach ($dataA as $key => $value) {
                $cr = new \App\Model\WaGlTran();
                $cr->period_number = $value->period_number;
                $cr->banking_expense_id = $value->banking_expense_id;
                $cr->banking_expense_type = $value->banking_expense_type;
                $cr->grn_type_number = $value->grn_type_number;
                $cr->trans_date = $request->date;
                $cr->restaurant_id = $request->branch;
                $cr->grn_last_used_number = $value->last_number_used;
                $cr->transaction_type = $value->transaction_type;
                $cr->transaction_no = $value->transaction_no;
                $cr->account = $value->account;
                $cr->amount = '-'.$value->amount;
                $cr->narrative = $value->description;
                $cr->reference = $value->ref_no;
                if (isset($value->customer_id)) {
                    $cr->customer_id = $value->customer_id;
                }                
                if($value->type == 'DEBIT')
                {
                    $btran = new \App\Model\WaBanktran;
                    $btran->type_number = $value->grn_type_number;
                    $btran->document_no = $value->transaction_no;
                    $btran->bank_gl_account_code = $parent->account->account_code;
                    $btran->reference =  $value->ref_no ;
                    $btran->trans_date = $request->date;
                    $btran->wa_payment_method_id = 1;
                    $btran->amount = $value->amount;
                    $btran->wa_curreny_id = 1;
                    $btran->save();
                    $cr->amount = $value->amount;
                }
                $cr->save();
            }
            $parent->save();
            return $parent;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bank Deposit Processed successfully',
                'location'=>route('banking.deposite.list')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function reconcile_daily_transactions()
    {
        $permission =  $this->mypermissionsforAModule();
        $model = 'reconcile-daily-transaction';
        $title = 'Bank deposit';
        $pmodule = 'bank-deposite';
        

        

        $channels = \App\WaTenderEntry::distinct()->pluck('channel');//dd($channels);

        $channel = '';

        $date = request()->filled('date') ? request()->date . ' 00:00:00' : now()->format('Y-m-d 00:00:00');

        // if (request()->filled('channel')) {
        //     $channel .= request()->channel;
        //     $query->where('channel', request()->channel);
        // }

        $debtorTrans = WaDebtorTran::query(); //DB::table('wa_debtor_trans')
        $debtorTrans->where('reconciled', false)
                    ->whereRaw("LENGTH(reference) > 4")
                    ->whereDate('wa_debtor_trans.trans_date', $date);
                    if (request()->filled('branch')) {
                        $debtorTrans->where('restaurants.id', request()->branch);
                    }
        $debtorTrans->select(
                        'wa_debtor_trans.id',
                        'wa_debtor_trans.trans_date',
                        'wa_debtor_trans.input_date',
                        'wa_debtor_trans.reference',
                        'wa_debtor_trans.amount',
                        'wa_debtor_trans.document_no',
                        'wa_debtor_trans.amount',
                        'wa_customers.customer_name',
                        'restaurants.name as branch'
                    )
                    ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                    ->join('routes','routes.id','wa_customers.route_id')
                    ->join('restaurants','restaurants.id','routes.restaurant_id')
                    ->where('document_no', 'like', '%RCT%')
                    ->get()
                    ->map(function ($record) {
                        $record->amount = abs($record->amount);
                        return $record;
                    });

        if (request()->wantsJson()) {
            return DataTables::eloquent($debtorTrans)
                ->addIndexColumn()
                ->editColumn('trans_date', function ($transaction) {
                    return $transaction->trans_date->format('Y-m-d H:i:s');
                })
                ->editColumn('amount', function ($transaction) {
                    return manageAmountFormat(abs($transaction->amount));
                })
                ->with('total', function () use ($debtorTrans) {
                    return $debtorTrans->sum('amount');
                })
                ->toJson();
        }
        return view('admin.banking.reconcile_daily.index', compact('model','title','pmodule','permission'));
    }

    public function reconcile_daily_transactions_datatable(Request $request)
    {
        dd($request);
    }
    public function validate_first_step(Request $request, $id = "")
    {
        $validator = Validator::make($request->all(), [
           
            // 'profit_margin' => 'required|numeric'
        ]);
        return !$validator->fails() ? false : $validator->errors();
    }
    
    public function validate_second_step(Request $request, $id = "")
    {
        $validator = Validator::make($request->all(), [
           
            // 'profit_margin' => 'required|numeric'
        ]);
        return !$validator->fails() ? false : $validator->errors();
    }
    
    public function reconcile_daily_transactions_store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), ['current_step' => "required|in:1,2,3"]);
            if ($validator->fails()) {
                return response()->json(['result' => 0, 'errors' => $validator->errors()]);
            }
            if ($request->current_step == 1 && $st_first = $this->validate_first_step($request)) {
                return response()->json(['result' => 0, 'errors' => $st_first]);
            }
            if ($request->current_step == 2 && $st_sec = $this->validate_second_step($request, 'required')) {
                return response()->json(['result' => 0, 'errors' => $st_sec]);
            }
            if ($request->current_step == 3 && $st_third = $this->validate_third_step($request)) {
                return response()->json(['result' => 0, 'errors' => $st_third]);
            }
            if ($request->current_step != 3) {
                return response()->json(['result' => 1, 'next_step' => $request->current_step + 1]);
            }

        } catch(\Exception $e){
            
        }
    }

    public function reconcile_daily_transactions_upload(Request $request)
    {        
        $xlsxReader = new Xlsx();
        $xlsxReader->setReadDataOnly(true);

        $fileName = $request->file('channel_file_upload');
        $spreadsheet = $xlsxReader->load($fileName);
        $data = $spreadsheet->getActiveSheet()->toArray();
        return $this->bankReconRepository->processReconciliation($data);
        
    }

    public function reconcile_daily_transactions_approve(Request $request)
    {
        $data = [
            'branch' => $request->branch,
            'channel' => $request->channel,
            'date' => $request->date,
            'data' => $request->reconJson
        ];

        foreach ($request->reconJson as $key => $value) {
            $debtorTrans = DB::table('wa_debtor_trans')->find($value['id']);
            
            $data = [
                'id'=> $debtorTrans->id,
                'accounting_period' => $debtorTrans->wa_accounting_period_id,
                'branch' => $request->branch,
                'document_no' => $debtorTrans->document_no,
                'reference' => $debtorTrans->reference,
                'amount' => $debtorTrans->amount,
                'bank_ref' => $value['bank_ref']
            ];
           $this->bankReconRepository->glTransApprovedReconciliation($data);
        }

        return response()->json([
            'result' => 1,
            'message' => 'Reconciliation Approved successfully',
            'location'=>route('banking.deposite.list')
        ]);
        
    }

    public function reconcile_daily_transactions_download(Request $request)
    {
        $records = $request->data;
        $arrays=[];
        if ($request->type =='Reconciliation') {
            foreach($records as $record){
                $arrays[]=[
                    'date' => $request->date,
                    'route' => $record['customer_name'],
                    'reference' => $record['reference'],
                    'document_no' => $record['document_no'],
                    'amount' => manageAmountFormat($record['amount']),
                    'Bank Ref' => $record['bank_ref']
                ];
            }
        } else{
            foreach($records as $record){
                $arrays[]=[
                    'date' => $request->date,
                    'route' => '-',
                    'reference' => $record[1],
                    'document_no' => '-',
                    'amount' => $record[4],
                    'Bank Ref' => '-'
                ];
            }
        }
        
        $export = new DailyReconciliationExport(collect($arrays));
        return Excel::download($export, 'daily-reconciliation-inquiry-'.date('Y-m-d-H-i-s').'.xlsx');  
    }

    
}
