<?php

namespace App\Http\Controllers\Admin;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WaExpenseController extends Controller
{
    protected $model = 'expenses';
    protected $title = 'Expense';
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
    public function departments(Request $request)
    {
        $data = \DB::table('wa_departments')->select(['id as id','department_name as text']);
        if($request->q)
        {
            $data = $data->where('department_name','LIKE',"%$request->q%");
        }
        $data = $data->get();
        $newData = [];
        if($request->payroll){
            $newData[] = ['id'=>'-1','text'=>'Show All'];
        }
        foreach ($data as $key => $value) {
            $newData[] = ['id'=>$value->id,'text'=>$value->text];
        }
        return response()->json($newData);
    }
    public function list(Request $request)
    {
        $data['pmodule'] = 'expenses';
        if(!$this->modulePermissions('expense')){
            return redirect()->back();
        }
        $data['permission'] =  $this->mypermissionsforAModule();        
        if($request->ajax()){
            $sortable_columns = [
                'wa_expenses.id',
                // 'wa_suppliers.supplier_code',
                'wa_charts_of_accounts.account_name',
                'payment_methods.title',
                'wa_expenses.total',
                'wa_expenses.ref_no',
                'wa_expenses.payment_date',
            ];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaExpenses::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '';
                if($data[$key]['is_processed'] == 0){
                    $data[$key]['links'] .= '<a href="'.route('expense.edit',['id'=>$re['id']]).'"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>';
                }else
                {
                    $data[$key]['links'] .= '<a href="'.route('expense.show',['id'=>$re['id']]).'"><i class="fa fa-eye" aria-hidden="true"></i></a>';
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
        $data['model'] = 'expenses';
        $data['title'] = 'Expenses';
        
        return view('admin.expenses.list')->with($data);
    }
    public function new()
    {
         if(!$this->modulePermissions('expense')){
            return redirect()->back();
        }

        $data['model'] = 'expenses';
        $data['title'] = 'Expenses';
        $data['pmodule'] = 'expenses';
        return view('admin.expenses.new')->with($data);
    }

    public function payee_list(Request $request)
    {
        if($request->type_new){
            $data = \DB::table('wa_charts_of_accounts')->select(['id as id','account_name as text']);
            if($request->q)
            {
                $data = $data->where('account_name','LIKE',"%$request->q%");
            }
            $data = $data->where('wa_account_group_id',16)->get();
            return $data;
        }
        if (isset($request->type)) {
            if($request->type == 'Customer'){
                $data = \DB::table('wa_customers')->select(['id as id','customer_code as text']);
                if($request->q)
                {
                    $data = $data->where('customer_code','LIKE',"%$request->q%");
                }
                $data = $data->get();
                return $data;
            }
            
            if($request->type == 'Supplier'){
                # Supplier List
                $data = \DB::table('wa_suppliers')->select(['id as id',\DB::RAW('CONCAT(name," (",supplier_code,")") as text')]);
                if($request->q)
                {
                    $data = $data->where('supplier_code','LIKE',"%$request->q%");
                }
                $data = $data->get();
                return $data;
            }
            return [];
        }else
        {
            $data = \DB::table('wa_suppliers')->select(['id as id',\DB::RAW('CONCAT(name," (",supplier_code,")") as text')]);
            if($request->q)
            {
                $data = $data->orWhere('name','LIKE',"%$request->q%");
                $data = $data->orWhere('supplier_code','LIKE',"%$request->q%");
            }
            $data = $data->get();
            return $data;
        }
    }
    public function paymentAccount(Request $request)
    {
        # Payment Accounts
        // $data = \DB::table('wa_charts_of_accounts')->select(['wa_charts_of_accounts.id as id',\DB::RAW('CONCAT(wa_charts_of_accounts.account_name," (",wa_charts_of_accounts.account_code,")") as text')])
        // ->join('wa_account_groups','wa_charts_of_accounts.wa_account_group_id','=','wa_account_groups.id')
        // ->where('wa_account_groups.group_name','CASH AND BANK');
        // if($request->q)
        // {
        //     $data = $data->orWhere('account_name','LIKE',"%$request->q%");
        //     $data = $data->orWhere('account_code','LIKE',"%$request->q%");
        // }
        // $data = $data->groupBy('wa_charts_of_accounts.id')->get();
        // return $data;
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
    public function payment_method(Request $request)
    {
        $data = \DB::table('payment_methods')->select(['id as id','title as text']);
        if($request->q)
        {
            $data = $data->where('title','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }
    public function category_list(Request $request)
    {
        # Payment Accounts
        $data = \DB::table('wa_charts_of_accounts')->select(['id as id',\DB::RAW('concat(account_name," (",account_code,")") as text')]);
        if($request->q)
        {
            $data = $data->orWhere('account_name','LIKE',"%$request->q%");
            $data = $data->orWhere('account_code','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }
    public function vat_list(Request $request)
    {
        $data = \DB::table('tax_managers')->select(['id as id',\DB::RAW('concat(title," (",tax_value,")") as text')]);
        if($request->q)
        {
            $data = $data->orWhere('title','LIKE',"%$request->q%");
            $data = $data->orWhere('tax_value','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }

    public function branches(Request $request)
    {
        $data = \DB::table('restaurants')->select(['id as id','name as text']);
        if($request->q)
        {
            $data = $data->where('name','LIKE',"%$request->q%");
        }
        $data = $data->get();
        return $data;
    }


    public function vat_find(Request $request)
    {
        $data = \DB::table('tax_managers')->where('id',$request->id)->first();
        return response()->json($data);
    }
    public function validations(Request $request)
    {
        $inputArray = [               
            //'payee' => 'required|exists:wa_suppliers,id',
            'payment_account' => 'required|exists:wa_charts_of_accounts,id',
            'payment_method' => 'required|exists:payment_methods,id',
            // 'branch' => 'required|exists:restaurants,id',
            'tax_check'=>'required|in:'.implode(',',array_keys(tax_amount_type())),
            'category_list' => 'required|array',
            'description' => 'nullable|array',
            // 'vat_list' => 'nullable|array',
            'amount' => 'nullable|array',
            'description.*' => 'nullable',
            'category_list.*' => "required",
            'branch.*' => "required|exists:restaurants,id",
            'tb_reporting_branch'=>"required|exists:restaurants,id",
            'project.*' => "required|exists:projects,id",
            'amount.*' => 'nullable',
            'vat_list.*' => 'nullable',
            'payment_date'=>'required|date',
            'memo'=>'nullable|string',
            'ref_no'=>'required|string',
        ];
        if(isset($request->category_list)){
            foreach($request->category_list as $key => $val)
            {
                if($val != ''){
                    $inputArray['description.'.$key] = 'required|string|min:1';
                    $inputArray['amount.'.$key] = 'required|numeric|min:1';
                    $inputArray['branch.'.$key] = 'required';
                    $inputArray['project.'.$key] = 'required';
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
         if(!$this->modulePermissions('expense')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }

        $validator = Validator::make($request->all(),$this->validations($request),['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'branch.*'=>'branch',
            'project.*'=>'project',
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
            $expense = new \App\Model\WaExpenses();
            //$expense->payee_id = $request->payee ;
            $expense->payment_account_id = $request->payment_account ;
            $expense->payment_date = $request->payment_date ;
            $expense->payment_method_id = $request->payment_method ;
            $expense->ref_no = $request->ref_no ;
            $expense->memo = $request->memo ;
            $expense->restaurant_id = NULL;//$request->branch;
            $expense->tb_reporting_branch = $request->tb_reporting_branch;
            $expense->control_amount = $request->control_amount ;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            $subTotal = 0;
            $total = 0;
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaExpenseCategories();
                $category->expense_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $category->branch_id = $request->branch[$key];
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
                    $category->total = $request->amount[$key]; //100 vat=16
                    $category->amount = ($request->amount[$key] - (($request->amount[$key]*$vatAm)/100));
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
            $expense->save();
            return $expense;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Expense added successfully',
                'location'=>route('expense.edit',['id'=>$check->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
        
    }

    public function edit($id)
    {
         if(!$this->modulePermissions('expense')){
            return redirect()->back();
        }

        $data['model'] = 'expenses';
        $data['title'] = 'Expenses';
        $data['pmodule'] = 'expenses';
        $data['data'] = \App\Model\WaExpenses::with(['categories'])->where('is_processed',0)->findOrFail($id);
        return view('admin.expenses.edit')->with($data);
    }
    public function show($id)
    {
         if(!$this->modulePermissions('expense')){
            return redirect()->back();
        }

        $data['model'] = 'expenses';
        $data['title'] = 'Expenses';
        $data['pmodule'] = 'expenses';
        $data['data'] = \App\Model\WaExpenses::with(['categories'])->where('is_processed',1)->findOrFail($id);
        return view('admin.expenses.show')->with($data);
    }
    public function update(Request $request)
    {
         if(!$this->modulePermissions('expense')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }

        $inputArray = $this->validations($request);
        $inputArray['id'] = 'required|exists:wa_expenses,id,is_processed,0';
        $validator = Validator::make($request->all(),$inputArray,['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
            'amount.*'=>'amount',
            'category_list.*'=>'category list',
            'branch.*'=>'branch',
            'project_list.*'=>'project_list',
            'gltag_list.*'=> 'gltag_list',
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
            $expense = \App\Model\WaExpenses::find($request->id);
            //$expense->payee_id = $request->payee ;
            $expense->payment_account_id = $request->payment_account ;
            $expense->payment_date = $request->payment_date ;
            $expense->payment_method_id = $request->payment_method ;
            $expense->ref_no = $request->ref_no ;
            $expense->control_amount = $request->control_amount ;
            $expense->tb_reporting_branch = $request->tb_reporting_branch;
            $expense->memo = $request->memo ;
            $expense->restaurant_id = NULL;//$request->branch;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            \App\Model\WaExpenseCategories::where('expense_id',$expense->id)->delete();
            // $expense->categories->delete();
            $subTotal = 0;
            $total = 0;
            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaExpenseCategories();
                $category->expense_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                $category->branch_id = $request->branch[$key];
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
                    $category->total = $request->amount[$key]; //100 vat=16
                    $category->amount = ($request->amount[$key] - (($request->amount[$key]*$vatAm)/100));
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
            $expense->save();
            return true;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Expense updated successfully',
                'location'=>route('expense.edit',['id'=>$request->id])
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
        
    }

    public function processExpense(Request $request)
    {
         if(!$this->modulePermissions('expense')){
            return response()->json([
                'result' => -1,
                'message' => 'You dont have permissions',
            ]);
        }

        $inputArray = $this->validations($request);
        $inputArray['id'] = 'required|exists:wa_expenses,id,is_processed,0';
        $validator = Validator::make($request->all(),$inputArray,['vat_list.*'=>'The vat list field is required.','category_lists.required'=>'Categories are required'],[
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
        $accountuingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
        if(!$accountuingPeriod || !$request->payment_date || strtotime($request->payment_date) > strtotime($accountuingPeriod->end_date) || strtotime($request->payment_date) < strtotime($accountuingPeriod->start_date)){
            return response()->json([
                'result' => -1,
                'message' => 'The posting date is not within range of the Actice Accounting period dates for the Month, Kindly refer to the Finance Manager',
            ]);
        }
        $check = \DB::transaction(function () use ($request,$accountuingPeriod) {
            $expense = \App\Model\WaExpenses::find($request->id);
            //$expense->payee_id = $request->payee ;
            $expense->payment_account_id = $request->payment_account ;
            $expense->payment_date = $request->payment_date ;
            $expense->payment_method_id = $request->payment_method ;
            $expense->ref_no = $request->ref_no ;
            $expense->memo = $request->memo ;
            $expense->control_amount = $request->control_amount ;
            $expense->tb_reporting_branch = $request->tb_reporting_branch;
            $expense->is_processed = 1;
            $expense->restaurant_id = NULL;//$request->branch;
            $expense->tax_amount_type = tax_amount_type()[$request->tax_check];
            $expense->save();
            \App\Model\WaExpenseCategories::where('expense_id',$expense->id)->delete();
            // $expense->categories->delete();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','EXPENSE')->first();
            
            $dataA = [];
            $Subtotal = 0;
            $vatAma = 0;
            $total = 0;
            $accountSS = [];

            foreach ($request->category_list as $key => $value) {
                $category = new \App\Model\WaExpenseCategories();
                $category->expense_id = $expense->id;
                $category->category_id = $request->category_list[$key];
                 $category->branch_id = $request->branch[$key];
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
                    $category->total = $request->amount[$key]; //100 vat=16
                    $category->amount = ($request->amount[$key] - (($request->amount[$key]*$vatAm)/100));
                }else{
                    $category->amount = $request->amount[$key];
                    $category->total = $request->amount[$key] + (($request->amount[$key]*$vatAm)/100);
                }
                $category->tax = $vatAm;
                $Subtotal += $category->amount;
                $vatAma += ($category->total - $category->amount);
                $total += $category->total;

                $category->save();
                $account = \DB::table('wa_charts_of_accounts')->where('id',$request->category_list[$key])->first();
                $dataA[] = (Object)[
                    'banking_expense_id'=>$category->id,
                    'banking_expense_type'=>'expense_category',
                    'transaction_type'=>$series_module->description,
                    'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                    'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                    'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                    'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                    'type'=>'Credit',
                    'tb_reporting_branch' => $request->tb_reporting_branch,
                    'restaurant_id'=>$category->branch_id,
                    'department_id'=>$category->department_id,
                    'project_id'=>$category->project_id,
                    'description'=>$category->description,
                    'account'=>$account ? $account->account_code : NULL,
                    'account_name'=>$account->parent_id,
                    'amount'=>$category->total,
                    'balancing_gl_account'=>1,
                ];
                $accountSS[] = $account->parent_id;
            }       
            $expense->subTotal = $Subtotal;
            $expense->total = $total;
            $expense->save();     
            // $account = \DB::table('wa_charts_of_accounts')->where('id',$request->payment_account)->first();
            $accountt = \App\Model\WaBankAccount::with(['getGlDetail'])->where('id',$request->payment_account)->first();
            $dataA[] = (Object)[
                'banking_expense_id'=>$expense->id,
                'banking_expense_type'=>'expense',
                'transaction_type'=>$series_module->description,
                'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                'type'=>'Debit',
                'tb_reporting_branch' => @$accountt->restaurant_id,
                'description'=>'',
                'restaurant_id'=>'',
                    'department_id'=>'',
                    'project_id'=>'',
                'account'=>$accountt ? @$accountt->getGlDetail->account_code : NULL,
                'amount'=> $total,
                'balancing_gl_account'=>NULL,
            ];
            if($vatAma > 0){
                $series_module = \App\Model\WaNumerSeriesCode::where('module','INPUT_VAT')->first();
                $account = \DB::table('wa_charts_of_accounts')->where('id',4)->first();
                $dataA[] = (Object)[
                    'banking_expense_id'=>$expense->id,
                    'banking_expense_type'=>'expense',
                    'transaction_type'=>$series_module->description,
                    'transaction_no'=>$series_module->code.str_pad($expense->id,6, "0", STR_PAD_LEFT),
                    'period_number'=>$accountuingPeriod?$accountuingPeriod->period_no:null,
                    'grn_type_number'=> $series_module ? $series_module->type_number : NULL,
                    'last_number_used'=> $series_module ? $series_module->last_number_used : NULL,
                    'type'=>'Debit',
                    'description'=>'',
                    'restaurant_id'=>'',
                    'tb_reporting_branch' => @$accountt->restaurant_id,
                    'department_id'=>'','account_name'=>NULL,
                    'project_id'=>'',
                    'account'=>$account ? $account->account_code : NULL,
                'balancing_gl_account'=>NULL,
                'amount'=> $vatAma,
                ];
            }
            $accountsss = \App\Model\WaChartsOfAccount::whereIn('id',$accountSS)->get();
            $user = getLoggeduserProfile();
            foreach ($dataA as $key => $value) {
                $cr = new \App\Model\WaGlTran();
                $cr->period_number = $value->period_number;
                $cr->user_id = $user->id;
                $cr->banking_expense_id = $value->banking_expense_id;
                $cr->banking_expense_type = $value->banking_expense_type;
                $cr->tb_reporting_branch = $value->tb_reporting_branch;
                $cr->grn_type_number = $value->grn_type_number;
                $cr->trans_date = $request->payment_date;
                $cr->restaurant_id = $value->restaurant_id;
                $cr->department_id = $value->department_id;
                $cr->project_id = $value->project_id;
                $cr->grn_last_used_number = $value->last_number_used;
                $cr->transaction_type = $value->transaction_type;
                $cr->transaction_no = $value->transaction_no;
                $cr->account = $value->account;
                $cr->amount = -$value->amount;
                $cr->narrative = $value->description;
                $cr->reference = $expense->ref_no;
                $cr->balancing_gl_account = ($value->balancing_gl_account == 1 ? ($accountt ? @$accountt->getGlDetail->account_code : NULL) : NULL);

                if($value->type == 'Credit')
                {
                    $btran = new \App\Model\WaBanktran;
                    $btran->type_number = $value->grn_type_number;
                    $btran->document_no = $value->transaction_no;
                    $btran->bank_gl_account_code = $accountt ? @$accountt->getGlDetail->account_code : NULL;
                    $btran->reference =  $expense->ref_no ;
                    $btran->trans_date = $request->payment_date;
                    $btran->wa_payment_method_id = $request->payment_account;
                    $btran->amount = '-'.$value->amount;
                    $btran->account = $value->account;
                    $acGL = $accountsss->where('id',$value->account_name)->first();
                    $btran->sub_account = @$acGL->account_code ?? NULL;
                    $btran->wa_curreny_id = 1;
                    $btran->save();
                    $cr->amount = $value->amount;
                }
                $cr->save();
            }
            return true;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Expense processed successfully',
                'location'=>route('expense.list')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function report_download(Request $request)
    {
         if(!$this->modulePermissions('expense')){
            return redirect()->back();
        }

        if(!isset($request->processed) || $request->processed == 0){
            \Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $response       = \App\Model\WaExpenses::getData(10000000, 0 , '', NULL, NULL,$request);   
        $data['data'] = $response['response'];        
        $data['title'] = 'Expense-Report';
        $data['heading'] = 'Expenses';
        // return view('admin.expenses.pdf', $data)->with($data);
        $pdf = \PDF::loadView('admin.expenses.pdf', $data)->setPaper('a4','landscape');
        // return $pdf;
        // return $pdf->stream();
        return $pdf->download('Expense-Report-'.time().'.pdf');
    }

    public function delete($id)
    {
        if(!$this->modulePermissions('expense_delete')){
            return redirect()->back();
        }
        $id = base64_decode($id);
        $expense = \App\Model\WaExpenses::where('id',$id)->first();
        if($expense){
            if($expense->is_processed != 0){
                \Session::flash('warning', 'Restricted: you cannot delete processed expense');
                return redirect()->back();
            }
            \App\Model\WaExpenseCategories::where('expense_id',$id)->delete();
            $expense->delete();
            \Session::flash('success', 'Expense deleted successfully');
            return redirect()->back();
        }
        \Session::flash('warning', 'Restricted: Expense not found');
        return redirect()->back();
    }
}
