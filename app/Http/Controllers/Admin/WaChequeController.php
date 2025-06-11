<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class WaChequeController extends Controller
{
    protected $pmodule = 'expenses';
    public function modulePermissions($type)
    {
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;

    }
    public function list(Request $request)
    {
        if(!$this->modulePermissions('cheque')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = [
                'wa_cheques.id',
                'wa_suppliers.supplier_code',
                'wa_charts_of_accounts.account_name',
                'wa_cheques.cheque_no',
                'wa_cheques.total',
                'wa_cheques.payment_date',
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaCheque::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '';
                if($data[$key]['is_processed'] == 0){
                    $data[$key]['links'] .= '<a href="'.route('cheques.edit',['id'=>$re['id']]).'"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>';
                }
                if($data[$key]['is_processed'] == 1){
                    $data[$key]['links'] .= '<a href="'.route('cheques.show',['id'=>$re['id']]).'"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }
                $data[$key]['payment_date'] = getDateFormatted($re['payment_date']);
                $data[$key]['totalAmount'] = manageAmountFormat($re['totalAmount']);
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
        $data['model'] = 'genralLedger-cheque';
        $data['title'] = 'Cheque';
        $data['pmodule'] = 'cheque';
        
        $data['permission'] =  $this->mypermissionsforAModule();
        return view('admin.cheque.list')->with($data);
    }

    public function new()
    {
        if(!$this->modulePermissions('cheque')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-cheque';
        $data['title'] = 'Cheque';
        $data['pmodule'] = 'cheque';
        return view('admin.cheque.new')->with($data);
    }
    public function bank_accounts(Request $request)
    {
        $data = \DB::table('wa_bank_accounts')->select(['id as id',\DB::RAW('CONCAT(account_name," (",account_code,")") as text')]);
        if($request->q)
        {
            $data = $data->orWhere('account_name','LIKE',"%$request->q%");
            $data = $data->orWhere('account_code','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }
    public function bank_accounts_find(Request $request)
    {
        $data = \DB::table('wa_bank_accounts')->where('id',$request->id)->first();
        return response()->json($data);
    }
    public function validations(Request $request)
    {
        $inputArray = [               
            'supplier' => 'required|exists:wa_suppliers,id',
            'bank_account' => 'required|exists:wa_charts_of_accounts,id',
            'payment_date'=>'required|date',
            'memo'=>'nullable|string',
            'branch'=>'required|exists:restaurants,id',
            'mailing_address'=>'required|string',
            'cheque_no'=>'required|string|unique:wa_cheques,cheque_no',
            'tax_check'=>'required|in:'.implode(',',array_keys(tax_amount_type())),
            
            'category_list' => 'required|array',
            'description' => 'nullable|array',
            'vat_list' => 'nullable|array',
            'amount' => 'nullable|array',
            'description.*' => 'nullable',
            'category_list.*' => "required",
            'amount.*' => 'nullable',
            'vat_list.*' => 'nullable',
        ];
        if(isset($request->category_list)){
            foreach($request->category_list as $key => $val)
            {
                $inputArray['category_list.'.$key] = 'required|exists:wa_charts_of_accounts,id';
                $inputArray['description.'.$key] = 'required|string|min:1';
                $inputArray['amount.'.$key] = 'required|numeric|min:1';
                $inputArray['vat_list.'.$key] = 'required_if:tax_check,==,Inclusive,Exclusive|exists:tax_managers,id';
            }
        }else
        {
            $inputArray['category_lists']='required';
        }
        return  $inputArray;
    }

    public function store(Request $request)
    {
        if(!$this->modulePermissions('cheque')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $validator = Validator::make($request->all(),$this->validations($request),['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'description.*'=>'description',
            'vat_list.*'=>'vat list'] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }

        $check = \DB::transaction(function () use ($request) {
            $expense = new \App\Model\WaCheque();
            $expense->restaurant_id = $request->branch;
            $expense->supplier_id = $request->supplier;
            $expense->bank_account_id = $request->bank_account ;
            $expense->payment_date = $request->payment_date ;
            $expense->cheque_no = $request->cheque_no ;
            $expense->memo = $request->memo ;
            $expense->mailing_address = $request->mailing_address ;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            $sub_total = 0;
            $total = 0;
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaChequeCategories();
                $category->cheque_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $vatAm = 0;
                if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive'])){
                    $category->tax_manager_id = $request->vat_list[$key];
                    $vat = \DB::table('tax_managers')->where('id',$category->tax_manager_id)->first();
                    $vatAm =  $vat->tax_value;
                }
                $category->description = $request->description[$key];
                if($request->tax_check == 'Inclusive'){
                    $category->total = $request->amount[$key]; //100 vat=16
                    $category->amount = ($request->amount[$key] - (($request->amount[$key]*$vatAm)/100));
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
                $category->tax = $vatAm;
                $sub_total +=  $category->amount;
                $total +=  $category->total;
                $category->save();
            }
            $expense->sub_total = $sub_total;
            $expense->total = $total;
            $expense->save();
            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Cheque added successfully',
                'location'=>route('cheques.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function edit($id)
    {
        if(!$this->modulePermissions('cheque')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-cheque';
        $data['title'] = 'Cheque';
        $data['pmodule'] = 'cheque';
        $data['data'] = \App\Model\WaCheque::where('is_processed',0)->findOrFail($id);
        return view('admin.cheque.edit')->with($data);
    }
    public function show($id)
    {
        if(!$this->modulePermissions('cheque')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-cheque';
        $data['title'] = 'Cheque';
        $data['pmodule'] = 'cheque';
        $data['data'] = \App\Model\WaCheque::where('is_processed',1)->findOrFail($id);
        return view('admin.cheque.show')->with($data);
    }
    public function update(Request $request,$id)
    {
        if(!$this->modulePermissions('cheque')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $validations = $this->validations($request);
        $validations['id'] = 'required|exists:wa_cheques,id,is_processed,0';
        $validations['cheque_no'] = 'required|string|unique:wa_cheques,cheque_no,'.$request->id;
        $validator = Validator::make($request->all(),$validations,['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'description.*'=>'description',
            'vat_list.*'=>'vat list'] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }

        $check = \DB::transaction(function () use ($request) {
            $expense = \App\Model\WaCheque::find($request->id);
            $expense->supplier_id = $request->supplier;
            $expense->restaurant_id = $request->branch;
            $expense->bank_account_id = $request->bank_account ;
            $expense->payment_date = $request->payment_date ;
            $expense->cheque_no = $request->cheque_no ;
            $expense->memo = $request->memo ;
            $expense->mailing_address = $request->mailing_address ;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            $sub_total = 0;
            $total = 0;
            \App\Model\WaChequeCategories::where('cheque_id',$expense->id)->delete();
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaChequeCategories();
                $category->cheque_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $vatAm = 0;
                if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive'])){
                    $category->tax_manager_id = $request->vat_list[$key];
                    $vat = \DB::table('tax_managers')->where('id',$category->tax_manager_id)->first();
                    $vatAm =  $vat->tax_value;
                }
                $category->description = $request->description[$key];
                if($request->tax_check == 'Inclusive'){
                    $category->total = $request->amount[$key]; //100 vat=16
                    $category->amount = ($request->amount[$key] - (($request->amount[$key]*$vatAm)/100));
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
                $category->tax = $vatAm;
                $sub_total +=  $category->amount;
                $total +=  $category->total;
                $category->save();
            }
            $expense->sub_total = $sub_total;
            $expense->total = $total;
            $expense->save();
            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Cheque updated successfully',
                'location'=>route('cheques.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function processCheque(Request $request,$id)
    {
        if(!$this->modulePermissions('cheque')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $validations = $this->validations($request);
        $validations['id'] = 'required|exists:wa_cheques,id,is_processed,0';
        $validations['cheque_no'] = 'required|string|unique:wa_cheques,cheque_no,'.$request->id;
        $validator = Validator::make($request->all(),$validations,['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'description.*'=>'description',
            'vat_list.*'=>'vat list'] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }

        $check = \DB::transaction(function () use ($request) {
            $expense = \App\Model\WaCheque::find($request->id);
            $expense->supplier_id = $request->supplier;
            $expense->bank_account_id = $request->bank_account ;
            $expense->payment_date = $request->payment_date ;
            $expense->cheque_no = $request->cheque_no ;
            $expense->memo = $request->memo ;
            $expense->mailing_address = $request->mailing_address ;
            $expense->is_processed = 1;
            $expense->restaurant_id = $request->branch;

            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            $sub_total = 0;
            $total = 0;
            \App\Model\WaChequeCategories::where('cheque_id',$expense->id)->delete();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','CHEQUE')->first();
            $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaChequeCategories();
                $category->cheque_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $vatAm = 0;
                if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive'])){
                    $category->tax_manager_id = $request->vat_list[$key];
                    $vat = \DB::table('tax_managers')->where('id',$category->tax_manager_id)->first();
                    $vatAm =  $vat->tax_value;
                }
                $category->description = $request->description[$key];
                if($request->tax_check == 'Inclusive'){
                    $category->total = $request->amount[$key]; //100 vat=16
                    $category->amount = ($request->amount[$key] - (($request->amount[$key]*$vatAm)/100));
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
                $category->tax = $vatAm;
                $sub_total +=  $category->amount;
                $total +=  $category->total;
                $category->save();
                $account = \DB::table('wa_charts_of_accounts')->where('id',$request->category_list[$key])->first();
                $dataA[] = (Object)[
                    'banking_expense_id'=>$category->id,
                    'banking_expense_type'=>'cheque_category',
                    'transaction_type'=>$series_module->description,
                    'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                    'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                    'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                    'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                    'type'=>'Debit',
                    'description'=>$category->description,
                    'account'=>$account ? $account->account_code : NULL,
                    'amount'=>$category->total,
                ];
            }
            $expense->sub_total = $sub_total;
            $expense->total = $total;
            $expense->save();
            $account = \DB::table('wa_charts_of_accounts')->where('id',$request->bank_account_id)->first();
            $dataA[] = (Object)[
                'banking_expense_id'=>$expense->id,
                'banking_expense_type'=>'cheque',
                'transaction_type'=>$series_module->description,
                'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                'type'=>'Credit',
                'description'=>'',
                'account'=>$account ? $account->account_code : NULL,
                'amount'=> $total,
            ];
            foreach ($dataA as $key => $value) {
                $cr = new \App\Model\WaGlTran();
                $cr->period_number = $value->period_number;
                $cr->banking_expense_id = $value->banking_expense_id;
                $cr->banking_expense_type = $value->banking_expense_type;
                $cr->grn_type_number = $value->grn_type_number;
                $cr->trans_date = $request->payment_date;
                $cr->restaurant_id = $request->branch;
                $cr->grn_last_used_number = $value->last_number_used;
                $cr->transaction_type = $value->transaction_type;
                $cr->transaction_no = $value->transaction_no;
                $cr->account = $value->account;
                $cr->amount = $value->amount;
                $cr->narrative = $value->description;
                $cr->reference = $expense->cheque_no;
                if($value->type == 'Credit')
                {
                    $btran = new \App\Model\WaBanktran;
                    $btran->type_number = $value->grn_type_number;
                    $btran->document_no = $value->transaction_no;
                    $btran->bank_gl_account_code = $expense->payment_account->account_code;
                    $btran->reference =  $expense->cheque_no ;
                    $btran->trans_date = $request->payment_date;
                    $btran->wa_payment_method_id = 1;
                    $btran->amount = '-'.$value->amount;
                    $btran->wa_curreny_id = 1;
                    $btran->save();

                    $newSupplierTrans = new \App\Model\WaSuppTran();
                    $newSupplierTrans->document_no = $value->transaction_no;
                    $newSupplierTrans->total_amount_inc_vat  = $value->amount;
                    $newSupplierTrans->trans_date  = $request->payment_date;
                    $newSupplierTrans->due_date  = $request->payment_date;
                    $newSupplierTrans->suppreference  = $expense->cheque_no;
                    $newSupplierTrans->supplier_no  =  $expense->payee->supplier_code;
                    $newSupplierTrans->grn_type_number  = $value->grn_type_number;
                    $newSupplierTrans->save();


                    $cr->amount = '-'.$value->amount;
                }
                $cr->save();
            }
            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Cheque processed successfully',
                'location'=>route('cheques.list')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function report_download(Request $request)
    {
        if(!$this->modulePermissions('cheque')){
            return redirect()->back();
        }
        if(!isset($request->processed) || $request->processed == 0){
            \Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $response       = \App\Model\WaCheque::getData(10000000, 0 , '', NULL, NULL,$request);   
        $data['data'] = $response['response'];        
        $data['title'] = 'Cheques-Report';
        $data['heading'] = 'Cheques';
        // return view('admin.expenses.pdf', $data)->with($data);
        $pdf = \PDF::loadView('admin.cheque.pdf', $data)->setPaper('a4','landscape');
        // return $pdf;
        // return $pdf->stream();
        return $pdf->download('Cheques-Report-'.time().'.pdf');
    }
}
