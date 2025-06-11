<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WaBillsController extends Controller
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
        $data['model'] = 'genralLedger-bills';
        $data['title'] = 'Bills';
        $data['pmodule'] = 'bills';
        if(!$this->modulePermissions('bill')){
            return redirect()->back();
        }
        $data['permission'] =  $this->mypermissionsforAModule();
        if($request->ajax()){
            $sortable_columns = [
                'wa_suppliers.supplier_code',
                'restaurants.name',
                'wa_bills.bill_no',
                'wa_bills.bill_date',
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaBills::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '';
                if($data[$key]['is_processed'] == 0){
                    $data[$key]['links'] .= '<a href="'.route('bills.edit',['id'=>$re['id']]).'"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>';
                }
                if($data[$key]['is_processed'] == 1){
                    if($data[$key]['balance'] > 0){
                        $data[$key]['links'] .= '<a href="'.route('bills.bill_payment',['id'=>$re['id']]).'" style="margin-right:5px" class="btn btn-sm btn-primary">Make Payment</a>';
                    }
                    $data[$key]['links'] .= '<a href="'.route('bills.bill_payment_list',['id'=>$re['id']]).'" class="btn btn-sm btn-warning">Payments</a>';

                }
                $data[$key]['payment_date'] = getDateFormatted($re['bill_date']);
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
        return view('admin.bills.list')->with($data);
    }

    public function new()
    {
        if(!$this->modulePermissions('bill')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bills';
        $data['title'] = 'Bills';
        $data['pmodule'] = 'bills';
        return view('admin.bills.new')->with($data);
    }
    public function payment_terms(Request $request)
    {
        $data = \DB::table('wa_payment_terms')->select(['id as id','term_code as text']);
        if($request->q)
        {
            $data = $data->where('term_code','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }
    public function payment_terms_find(Request $request)
    {
        $term = \DB::table('wa_payment_terms')->where('id',$request->id)->first();
        $data = [];
        // $data['due_date'] = date('Y-m-d',strtotime($request->bill_date.' +'.$term->due_after_given_month.' months'));
        $data['due_date'] = date('Y-m-d',strtotime($request->bill_date.' +'.$term->days_in_following_months.' days'));
       
        return response()->json($data);
    }
    public function validations(Request $request)
    {
        $inputArray = [               
            'supplier' => 'required|exists:wa_suppliers,id',
            'wa_payment_terms' => 'required|exists:wa_payment_terms,id',
            'main_branch' => 'required|exists:restaurants,id',
            'branch.*' => 'required|exists:restaurants,id',
            'project_list.*' => "required|exists:projects,id",
            'gltag_list.*' => "required|exists:gl_tags,id",
            'mailing_address'=>'required|string|min:1|max:255',
            // 'bill_no'=>'required|string|min:1|max:255|unique:wa_bills,bill_no',
            'bill_date'=>'required|date',
            'due_date'=>'required|date',
            'tax_check'=>'required|in:'.implode(',',array_keys(tax_amount_type())),
            'memo'=>'nullable|string|min:1|max:255',
            'category_list' => 'required|array',
            'description' => 'nullable|array',
            'amount' => 'nullable|array',
            'description.*' => 'nullable',
            'category_list.*' => "required",
            'amount.*' => 'nullable',
            'vat_list.*' => 'nullable',            
        ];
        if(isset($request->category_list)){
            foreach($request->category_list as $key => $val)
            {
                if($val != ''){
                    $inputArray['description.'.$key] = 'required|string|min:1|max:255|min:1';
                    $inputArray['item_bill_no.'.$key] = 'nullable|string|max:255';
                    $inputArray['amount.'.$key] = 'required|numeric|min:1';
                    $inputArray['vat_list.'.$key] = 'required_if:tax_check,==,Inclusive,Exclusive|exists:tax_managers,id';
                }
            }
        }else
        {
            $inputArray['category_lists']='required';
        }
        return  $inputArray;
    }
    public function store(Request $request)
    {
        if(!$this->modulePermissions('bill')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $validator = Validator::make($request->all(),$this->validations($request),['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'description.*'=>'description',
            'item_bill_no.*'=>'bill no',
            'wa_payment_terms'=>'payment terms',
            'vat_list.*'=>'vat list'] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
       
        $check = \DB::transaction(function () use ($request) {
            $expense = new \App\Model\WaBills();
            $expense->supplier_id = $request->supplier ;
            $expense->terms_id = $request->wa_payment_terms;
            $expense->due_date = $request->due_date ;
            $expense->bill_date = $request->bill_date ;
            $expense->mailing_address = $request->mailing_address ;
            $expense->memo = $request->memo ;
            $expense->restaurant_id = $request->main_branch;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            $expense->bill_no = 'BILL-'.str_pad($expense->id, 8, '0', STR_PAD_LEFT) ;//$request->bill_no ;
            $expense->save();
            $subTotal = 0;
            $total = 0;
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaBillCategories();
                $category->bill_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $category->branch_id = $request->branch[$key];
                $category->item_bill_no = $request->item_bill_no[$key];
                $category->project_id  = $request->project_list[$key];
                $category->gltag_id  = $request->gltag_list[$key];
                $vatAm = 0;
                if(isset($request->vat_list[$key]) && in_array($request->tax_check,['Inclusive','Exclusive'])){
                    $category->tax_manager_id = $request->vat_list[$key];
                    $vat = \DB::table('tax_managers')->where('id',$category->tax_manager_id)->first();
                    $vatAm =  $vat->tax_value;
                }
                $category->description = $request->description[$key];
                if($request->tax_check == 'Inclusive'){
                    $category->total = $request->amount[$key];
                    $category->amount = $request->amount[$key] - (($request->amount[$key]*$vatAm)/100);
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
               
                $category->tax = $vatAm;

                $subTotal +=  $category->amount;
                $total +=  $category->total;
                $category->save();
            }
            $expense->subTotal = $subTotal;
            $expense->total = $total;
            $expense->balance = $total;
            $expense->save();
            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bill added successfully',
                'location'=>route('bills.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function edit($id)
    {
        if(!$this->modulePermissions('bill')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bills';
        $data['title'] = 'Bills';
        $data['pmodule'] = 'bills';
        $data['data'] = \App\Model\WaBills::with(['categories'])->where('is_processed',0)->findOrFail($id);
        return view('admin.bills.edit')->with($data);
    }
    public function update(Request $request)
    {
        if(!$this->modulePermissions('bill')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $inputArray= $this->validations($request);
        $inputArray['id'] = 'required|exists:wa_bills,id,is_processed,0';
        // $inputArray['bill_no'] = $inputArray['bill_no'].','.$request->id;
        $validator = Validator::make($request->all(),$inputArray,['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'description.*'=>'description',
            'wa_payment_terms'=>'payment terms',
            'vat_list.*'=>'vat list'] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
       
        $check = \DB::transaction(function () use ($request) {
            $expense = \App\Model\WaBills::find($request->id);
            $expense->supplier_id = $request->supplier ;
            $expense->terms_id = $request->wa_payment_terms;
            $expense->due_date = $request->due_date ;
            $expense->bill_date = $request->bill_date ;
            $expense->mailing_address = $request->mailing_address ;
            // $expense->bill_no = $request->bill_no ;
            $expense->memo = $request->memo ;
            $expense->restaurant_id = $request->main_branch;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            \App\Model\WaBillCategories::where('bill_id',$expense->id)->delete();
            $subTotal = 0;
            $total = 0;
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaBillCategories();
                $category->bill_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $category->branch_id = $request->branch[$key];
                $category->project_id  = $request->project_list[$key];
                $category->gltag_id  = $request->gltag_list[$key];
                $category->item_bill_no = $request->item_bill_no[$key];
                $vatAm = 0;
                if(isset($request->vat_list[$key])){
                    $category->tax_manager_id = $request->vat_list[$key];
                    $vat = \DB::table('tax_managers')->where('id',$category->tax_manager_id)->first();
                    $vatAm =  $vat->tax_value;
                }
                $category->description = $request->description[$key];
                if($request->tax_check == 'Inclusive'){
                    $category->total = $request->amount[$key];
                    $category->amount = $request->amount[$key] - (($request->amount[$key]*$vatAm)/100);
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
                $category->tax = $vatAm;
                $category->save();
                $subTotal +=  $category->amount;
                $total +=  $category->total;
            }
            $expense->subTotal = $subTotal;
            $expense->total = $total;
            $expense->balance = $total;
            $expense->save();
            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bill updated successfully',
                'location'=>route('bills.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function process(Request $request)
    {
        if(!$this->modulePermissions('bill')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $inputArray= $this->validations($request);
        $inputArray['id'] = 'required|exists:wa_bills,id,is_processed,0';
        // $inputArray['bill_no'] = $inputArray['bill_no'].','.$request->id;
        $validator = Validator::make($request->all(),$inputArray,['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'description.*'=>'description',
            'wa_payment_terms'=>'payment terms',
            'vat_list.*'=>'vat list'] );
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
        if(!$accountuingPeriod || !$request->bill_date || strtotime($request->bill_date) > strtotime($accountuingPeriod->end_date) || strtotime($request->bill_date) < strtotime($accountuingPeriod->start_date)){
            return response()->json([
                'result' => -1,
                'message' => 'The posting date is not within range of the Actice Accounting period dates for the Month, Kindly refer to the Finance Manager',
            ]);
        }
        $check = \DB::transaction(function () use ($request, $accountuingPeriod) {
            $expense = \App\Model\WaBills::find($request->id);
            $expense->supplier_id = $request->supplier ;
            $expense->terms_id = $request->wa_payment_terms;
            $expense->due_date = $request->due_date ;
            $expense->bill_date = $request->bill_date ;
            $expense->mailing_address = $request->mailing_address ;
            // $expense->bill_no = $request->bill_no ;
            $expense->memo = $request->memo ;
            $expense->is_processed = 1;
            $expense->restaurant_id = $request->main_branch;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            \App\Model\WaBillCategories::where('bill_id',$expense->id)->delete();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','BILL')->first();

            $dataA = [];
            $Subtotal = 0;
            $vatAma = 0;
            $total = 0;
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaBillCategories();
                $category->bill_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $category->branch_id = $request->branch[$key];
                $category->project_id  = $request->project_list[$key];
                $category->gltag_id  = $request->gltag_list[$key];
                $category->item_bill_no = $request->item_bill_no[$key];
                $vatAm = 0;
                if(isset($request->vat_list[$key])){
                    $category->tax_manager_id = $request->vat_list[$key];
                    $vat = \DB::table('tax_managers')->where('id',$category->tax_manager_id)->first();
                    $vatAm =  $vat->tax_value;
                }
                $category->description = $request->description[$key];
                if($request->tax_check == 'Inclusive'){
                    $category->total = $request->amount[$key];
                    $category->amount = $request->amount[$key] - (($request->amount[$key]*$vatAm)/100);
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
                $category->tax = $vatAm;
                $category->save();
                $Subtotal += $category->amount;
                $vatAma += ($category->total - $category->amount);
                $total += $category->total;
                $account = \DB::table('wa_charts_of_accounts')->where('id',$request->category_list[$key])->first();
                $dataA[] = (Object)[
                    'banking_expense_id'=>$category->id,
                    'banking_expense_type'=>'bill_category',
                    'transaction_type'=>$series_module->description,
                    'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                    'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                    'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                    'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                    'type'=>'Credit',
                    'item_bill_no'=>$category->item_bill_no,
                    'tb_reporting_branch'=>$category->branch_id,
                    'restaurant_id'=>$category->branch_id,
                    'department_id'=>$category->department_id,
                    'project_id'=>$category->project_id,
                    'gl_tag'=>$category->gl_tag_id,
                    'description'=>$category->description,
                    'account'=>$account ? $account->account_code : NULL,
                'balancing_gl_account'=>1,
                'amount'=>$request->amount[$key],
                ];
            }
            $expense->subTotal = $Subtotal;
            $expense->total = $total;
            $expense->balance = $total;
            $expense->save();
            $account1 = companyPrefFromRes($expense->restaurant_id);

            // $account = \App\Model\Restaurant::where('id',$request->branch)->with('getAssociateCompany')->first();
            $dataA[] = (Object)[
                'banking_expense_id'=>$expense->id,
                'banking_expense_type'=>'bill',
                'transaction_type'=>$series_module->description,
                'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                'type'=>'Debit',
                'description'=>'',
                'tb_reporting_branch'=>$expense->restaurant_id,
                'restaurant_id'=>$expense->restaurant_id,
                    'department_id'=>'',
                    'item_bill_no'=>NULL,
                    'project_id'=>'',
                    'gl_tag'=>'',
                    'account'=>$account1,
                'balancing_gl_account'=>NULL,
                'amount'=> $total,
                'vatamount'=>$vatAma,
            ];
            if($vatAma > 0){
                $series_module = \App\Model\WaNumerSeriesCode::where('module','INPUT_VAT')->first();
                $series_modules = \App\Model\WaNumerSeriesCode::where('module','BILL')->first();
                $account = \DB::table('wa_charts_of_accounts')->where('account_code',14003)->first();
                $dataA[] = (Object)[
                    'banking_expense_id'=>$expense->id,
                    'banking_expense_type'=>'bill',
                    'transaction_type'=>$series_modules->description,
                    'transaction_no'=>$series_modules->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                    'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                    'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                    'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                    'type'=>'Credit',
                    'restaurant_id'=>$expense->restaurant_id,
                    'tb_reporting_branch'=>$expense->restaurant_id,

                    'item_bill_no'=>NULL,
                    'department_id'=>'',
                    'gl_tag'=>'',
                    'project_id'=>'',
                    'balancing_gl_account'=>NULL,
                    'description'=>'',
                    'account'=>$account ? $account->account_code : NULL,
                    'amount'=> $vatAma,
                ];
            }
            $user = getLoggeduserProfile();
            foreach ($dataA as $key => $value) {
                $cr = new \App\Model\WaGlTran();
                $cr->period_number = $value->period_number;
                $cr->user_id = $user->id;
                $cr->banking_expense_id = $value->banking_expense_id;
                $cr->banking_expense_type = $value->banking_expense_type;
                $cr->tb_reporting_branch = $value->tb_reporting_branch;
                $cr->grn_type_number = $value->grn_type_number;
                $cr->trans_date = $request->bill_date;
                // $cr->restaurant_id = $request->branch;
                $cr->grn_last_used_number = $value->last_number_used;
                $cr->transaction_type = $value->transaction_type;
                $cr->transaction_no = $value->transaction_no;
                $cr->supplier_code = @$expense->supplier->supplier_code;
                $cr->supplier_name = @$expense->supplier->name;

                $cr->account = $value->account;
                $cr->restaurant_id = $value->restaurant_id;
                $cr->department_id = $value->department_id;
                $cr->item_bill_no = $value->item_bill_no;
                $cr->project_id = $value->project_id;
                $cr->gl_tag = $value->gl_tag;
                $cr->amount = '-'.$value->amount;
                $cr->narrative = $value->description;
                // $cr->reference = $expense->bill_no;
                $cr->balancing_gl_account = ($value->balancing_gl_account == 1 ? ($account1) : NULL);

                if($value->type == 'Credit')
                {
                    //Debit value changed by client
                    $newSupplierTrans = new \App\Model\WaSuppTran();
                    $newSupplierTrans->document_no = $value->transaction_no;
                    $newSupplierTrans->total_amount_inc_vat  = $value->amount;
                    $newSupplierTrans->trans_date  = $request->bill_date;
                    $newSupplierTrans->description  = $value->description;
                    $newSupplierTrans->due_date  = $request->due_date;
                    // $newSupplierTrans->suppreference  = $expense->bill_no;
                    $newSupplierTrans->item_bill_no = $value->item_bill_no;
                    $newSupplierTrans->bill_id  = $expense->id;
                    $newSupplierTrans->bill_type  = $value->banking_expense_type == 'bill_category' ? 'BillCategory' : 'Bill';
                    $newSupplierTrans->supplier_no  =  $expense->supplier->supplier_code;
                    $newSupplierTrans->grn_type_number  = $value->grn_type_number;
                    if(isset($value->vatamount) && $value->vatamount > 0){
                        $newSupplierTrans->vat_amount  = $value->vatamount;
                    }
                    $newSupplierTrans->account = $value->account;
                    $newSupplierTrans->save();
                    $cr->amount = $value->amount;
                }
                $cr->supplier_account_number = @$expense->supplier->supplier_code;
                $cr->supplier_code = @$expense->supplier->supplier_code;
                $cr->save();
            }

            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bill processed successfully',
                'location'=>route('bills.list')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    //bill payment.
    public function bill_payment($id)
    {
        if(!$this->modulePermissions('bill')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bills';
        $data['title'] = 'Bills';
        $data['pmodule'] = 'bills';
        $data['data'] = \App\Model\WaBills::with(['categories'])->where('is_processed',1)->findOrFail($id);
        return view('admin.bills.bill_payment')->with($data);
    }

    public function bill_payment_process($id,Request $request)
    {
        if(!$this->modulePermissions('bill')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }
        $inputArray = [];
        $inputArray['id'] = 'required|exists:wa_bills,id,is_processed,1';
        $inputArray['bank_account'] = 'required|exists:wa_charts_of_accounts,id';
        // $inputArray['supplier'] = 'required|exists:wa_suppliers,id';
        $inputArray['mailing_address'] = 'required|string|min:1|max:255';
        $inputArray['memo'] = 'required|string|min:1|max:255';
        $data = \App\Model\WaBills::with(['categories'])->where('is_processed',1)->findOrFail($id);
        $inputArray['payment_amount'] = 'numeric|required|min:1|max:'.$data->balance;
        $inputArray['ref_no']='required|string|min:1|max:255|unique:wa_bill_payments,ref_no';
        $inputArray['payment_date'] = 'required|date';
        $validator = Validator::make($request->all(),$inputArray);
        if ($validator->fails()) 
        {
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
        if(!$accountuingPeriod || !$request->payment_date || strtotime($request->payment_date) > strtotime($accountuingPeriod->end_date) || strtotime($request->payment_date) < strtotime($accountuingPeriod->start_date)){
            return response()->json([
                'result' => -1,
                'message' => 'The posting date is not within range of the Actice Accounting period dates for the Month, Kindly refer to the Finance Manager',
            ]);
        }
        $check = \DB::transaction(function () use ($request,$data, $accountuingPeriod){
            $account = companyPrefFromRes($data->restaurant_id);
            $user = getLoggeduserProfile();
            $bill = new \App\Model\WaBillPayment;
            $bill->bank_account_id = $request->bank_account;
            $bill->supplier_id = $data->supplier->id;
            $bill->wa_bill_id = $data->id;
            $bill->mailing_address = $request->mailing_address;
            $bill->memo = $request->memo;
            $bill->amount = $request->payment_amount;
            $bill->payment_date = $request->payment_date;
            $bill->ref_no = $request->ref_no;
            $balance = $data->balance - $request->payment_amount;
            $bill->opening_balance = $data->balance;
            $data->balance = $balance;
            $bill->save();
            $data->save();
            $dataA = [];
            $series_module = \App\Model\WaNumerSeriesCode::where('module','BILL')->first();
            
            $dataA[] = (Object)[
                'banking_expense_id'=>$bill->id,
                'banking_expense_type'=>'bill_payment',
                'transaction_type'=>$series_module->description,
                'transaction_no'=>$series_module->code.str_pad($bill->id,6, "0", STR_PAD_LEFT),
                'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                'type'=>'Debit',
                'balancing_gl_account'=>1,
                'description'=>'',
                'account'=>$account ? $account : NULL,
                'amount'=> $bill->amount,
            ];
            // $account = \DB::table('wa_charts_of_accounts')->where('id',$request->bank_account)->first();
            $accountt = \App\Model\WaBankAccount::with(['getGlDetail'])->where('id',$request->bank_account)->first();
            $dataA[] = (Object)[
                'banking_expense_id'=>$bill->id,
                'banking_expense_type'=>'bill_payment',
                'transaction_type'=>$series_module->description,
                'transaction_no'=>$series_module->code.str_pad($bill->id,6, "0", STR_PAD_LEFT),
                'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                'type'=>'Credit',
                'balancing_gl_account'=>NULL,
                'description'=>'',
                'account'=>$accountt ? @$accountt->getGlDetail->account_code : NULL,
                'amount'=> $bill->amount,
            ];
            $accountsss = \App\Model\WaChartsOfAccount::where('id',($accountt ? @$accountt->getGlDetail->parent_id : NULL))->first();

            foreach ($dataA as $key => $value) {                
                if($value->type == 'Debit')
                {
                    //Credit value changed by client
                    $newSupplierTrans = new \App\Model\WaSuppTran();
                    $newSupplierTrans->document_no = $value->transaction_no;
                    $newSupplierTrans->total_amount_inc_vat  = '-'.$value->amount;
                    if($data->balance >= 0 ){
                        $newSupplierTrans->allocated_amount  = $data->balance;
                    }

                    $newSupplierTrans->trans_date  = $request->payment_date;
                    $newSupplierTrans->due_date  = $data->due_date;
                    $newSupplierTrans->suppreference  = $bill->ref_no;
                    $newSupplierTrans->supplier_no  =  $data->supplier->supplier_code;
                    $newSupplierTrans->grn_type_number  = $value->grn_type_number;
                    $newSupplierTrans->bill_id  = $data->id;
                    $newSupplierTrans->balancing_gl_account = $accountt ? @$accountt->getGlDetail->account_code : NULL;
                    $newSupplierTrans->bill_type  = 'Bill Payment';
                    // if(isset($value->vatamount)){
                    //     $newSupplierTrans->vat_amount  = '-'.$value->vatamount;
                    // }
                    $newSupplierTrans->save();

                    $cr = new \App\Model\WaGlTran();
                    $cr->period_number = $value->period_number;
                    $cr->user_id = $user->id;
                    $cr->banking_expense_id = $value->banking_expense_id;
                    $cr->banking_expense_type = $value->banking_expense_type;
                    $cr->grn_type_number = $value->grn_type_number;
                    $cr->trans_date = $request->payment_date;
                    $cr->supplier_code = @$data->supplier->supplier_code;
                    $cr->supplier_name = @$data->supplier->name;
                    $cr->restaurant_id = $data->restaurant_id;
                    $cr->tb_reporting_branch = $accountt->restaurant_id;
                    $cr->grn_last_used_number = $value->last_number_used;
                    $cr->supplier_account_number = $newSupplierTrans->supplier_no;
                    $cr->balancing_gl_account = $newSupplierTrans->balancing_gl_account;
                    $cr->transaction_type = $value->transaction_type;
                    $cr->transaction_no = $value->transaction_no;
                    $cr->account = $value->account;
                    $cr->amount = $value->amount;
                    $cr->narrative = $value->description;
                    $cr->reference = $bill->ref_no;
                    $cr->save();
                }
                if($value->type == 'Credit')
                {
                    $btran = new \App\Model\WaBanktran;
                    $btran->type_number = $value->grn_type_number;
                    $btran->document_no = $value->transaction_no;
                    $btran->bank_gl_account_code = $value->account;
                    $btran->reference =  $bill->ref_no ;
                    $btran->trans_date = $request->payment_date;
                    $btran->wa_payment_method_id = 1;
                    $btran->amount = '-'.$value->amount;
                    $btran->wa_curreny_id = 1;
                    $btran->account = $value->account;
                    $btran->sub_account = @$accountsss->account_code ?? NULL;
                    $btran->save();

                    $cr = new \App\Model\WaGlTran();
                    $cr->period_number = $value->period_number;
                    $cr->user_id = $user->id;
                    $cr->banking_expense_id = $value->banking_expense_id;
                    $cr->banking_expense_type = $value->banking_expense_type;
                    $cr->grn_type_number = $value->grn_type_number;
                    $cr->trans_date = $request->payment_date;
                    $cr->restaurant_id = $data->restaurant_id;
                    $cr->tb_reporting_branch = $accountt->restaurant_id;
                    $cr->supplier_code = @$data->supplier->supplier_code;
                    $cr->supplier_name = @$data->supplier->name;
                    $cr->grn_last_used_number = $value->last_number_used;
                    $cr->transaction_type = $value->transaction_type;
                    $cr->transaction_no = $value->transaction_no;
                    $cr->account = $value->account;
                    $cr->amount = '-'.$value->amount;
                    $cr->narrative = $value->description;
                    $cr->reference = $bill->ref_no;
                    $cr->save();
                }
            }
            return true;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Bill Payment processed successfully',
                'location'=>route('bills.list')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function bill_payment_list($id,Request $request)
    {
        if(!$this->modulePermissions('bill')){
            return redirect()->back();
        }
        $data['model'] = 'genralLedger-bills';
        $data['title'] = 'Bills';
        $data['pmodule'] = 'bills';
        $data['data'] = \App\Model\WaBills::with(['categories'])->where('is_processed',1)->findOrFail($id);
        $data['permission'] =  $this->mypermissionsforAModule();
        if($request->ajax()){
            $sortable_columns = [
                'wa_bill_payments.id',
                'wa_suppliers.supplier_code',
                'wa_charts_of_accounts.account_name',
                'wa_bill_payments.ref_no',
                'wa_bill_payments.payment_date',
                'wa_bill_payments.amount',
                'wa_bill_payments.created_at',
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $request->id = $id;
            $response       = \App\Model\WaBillPayment::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['payment_date'] = getDateFormatted($re['payment_date']);
                $data[$key]['totalAmount'] = manageAmountFormat($re['totalAmount']);
                $data[$key]['created_at'] = getDateFormatted($re['created_at']);
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
        return view('admin.bills.bill_payment_list')->with($data);
    }

    public function report_download(Request $request)
    {
        if(!$this->modulePermissions('bill')){
            return redirect()->back();
        }
        if(!isset($request->processed) || $request->processed == 0){
            \Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $response       = \App\Model\WaBills::getData(10000000, 0 , '', NULL, NULL,$request);   
        $data['data'] = $response['response'];        
        $data['title'] = 'Bills-Report';
        $data['heading'] = 'Bills';
        // return view('admin.expenses.pdf', $data)->with($data);
        $pdf = \PDF::loadView('admin.bills.pdf', $data)->setPaper('a4','landscape');
        // return $pdf;
        return $pdf->stream();
        return $pdf->download('BIlls-Report-'.time().'.pdf');
    }
}
