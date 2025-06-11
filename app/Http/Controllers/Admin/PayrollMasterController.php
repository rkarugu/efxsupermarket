<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\FdSeasonsAttribute;
use App\Model\Nationality;
use App\Model\TerminationTypes;
use App\Model\JobTitle;
use App\Model\PaymentFrequency;
use App\Model\Bank;
use App\Model\PaymentModes;
use Excel;
use App\Model\Employee;
use App\Model\SeparationTermnation;
use App\Model\PayrollWaPayment;
use App\Model\WaDepartment;
use App\Model\WaCurrencyManager;
use App\Model\LoanType;
use App\Model\Branch;
use App\Model\Commission;
use App\Model\Allowance;
use App\Model\PayrollLoanType;
use App\Model\PayrollCommission;
use App\Model\PayrollPension;
use App\Model\PayrollAllowances;
use App\Model\Pension;
use App\Model\Relief;
use App\Model\PayrollRelief;
use App\Model\Sacco;
use App\Model\PayrollSacco;
use App\Model\PayrollCustomParameters;
use App\Model\NonCashBenfit;
use App\Model\WaNonCashBenefits;
use App\Model\CustomParameter;
use Illuminate\Support\Facades\Validator;

class PayrollMasterController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Payroll Master';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.PayrollMaster.index',compact(
                    'title','lists','model','breadcum','pmodule','permission'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function Datatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'id', 'staff_number','first_name','date_of_birth','job_title','branch_id','date_of_birth','date_employed','last_name','Id_number'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Employee::select('wa_employee.*')->where('status','Active');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('staff_number', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('date_of_birth', 'LIKE', "%{$search}%");
                    
            });
            
        }
        $data_query_count = $data_query;
        $totalFiltered = $data_query_count->count();
        $data_query = $data_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($data_query)) {
            foreach ($data_query as $key => $row) { 
               $depData = WaDepartment::where('id',$row->department_id)->first();                 
               $job_Data = JobTitle::where('id',$row->job_title)->first();                 
               $branch_Data = Branch::where('id',$row->branch_id)->first();                 
                $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['staff_number'] = $row->staff_number;
                $nestedData['first_name'] = $row->first_name . ' '. $row->middle_name . ' '. $row->last_name;
                $nestedData['Id_number'] = $row->Id_number;
                $nestedData['nhif_no'] = $row->nhif_no;
                $nestedData['nssf_no'] = $row->nssf_no;
                $nestedData['action'] =  "<a href='" . route('payroll.manage',['id'=> $row->id])." '><button class='btn btn-primary'>Proceed</button></a>";
                $data[] = $nestedData;
            }
        
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
  }

  public function Payroll($payID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $empData = Employee::where('id',$payID)->first();
    $payment_frequency = PaymentFrequency::pluck('frequency','id');
    $branchData = Branch::pluck('branch','id');
    $bankData = Bank::pluck('bank','id');
    $payment_mode = PaymentModes::pluck('mode','id');
    $wa_CurrencyManager = WaCurrencyManager::pluck('ISO4217','id');
    $wa_Allowances = Allowance::pluck('allowance','id');
    $wa_LoanType = LoanType::pluck('loan_type','id');
    $commissionData = Commission::pluck('commission','id');
    $pensionData = Pension::pluck('pension','id');
    $reliefData = Relief::pluck('relief','id');
    $saccoData = Sacco::pluck('Sacco','id');
    $parameterNonCustomers= NonCashBenfit::pluck('non_cash_benefit','id');
    $customParameterData = CustomParameter::pluck('parameter','id');
    $payrollWaPaymentData = PayrollWaPayment::where('emp_id',$payID)->first();
    $payrollAllowancesActive = PayrollAllowances::where([['active','Yes'],['emp_id',$payID]])->get();
    $payrollAllowancesDeActive = PayrollAllowances::where([['active','No'],['emp_id',$payID]])->get();
    $payrollLoanTypeActive = PayrollLoanType::where([['active','Yes'],['emp_id',$payID]])->get();
    $payrollCommissionDActive = PayrollCommission::where([['active','No'],['emp_id',$payID]])->get();
    $payrollCommissionActive = PayrollCommission::where([['active','Yes'],['emp_id',$payID]])->get();
    $payrollLoanTypeDeActive = PayrollLoanType::where([['active','No'],['emp_id',$payID]])->get();
    $payrollPensionActive = PayrollPension::where([['active','Yes'],['emp_id',$payID]])->get();
    $payrollPensionDeActive = PayrollPension::where([['active','No'],['emp_id',$payID]])->get();
    $payrollReliefActive = PayrollRelief::where([['active','Yes'],['emp_id',$payID]])->get();
    $payrollReliefDeActive = PayrollRelief::where([['active','No'],['emp_id',$payID]])->get();
    $payrollSaccoActive = PayrollSacco::where([['active','Yes'],['emp_id',$payID]])->get();
    $payrollSaccoDeActive = PayrollSacco::where([['active','No'],['emp_id',$payID]])->get();
    $waNonCashBenefitsData = WaNonCashBenefits::where('emp_id',$payID)->get();
    $customParameterActive = PayrollCustomParameters::where([['active','Yes'],['emp_id',$payID]])->get();
    $customParameterDeActive = PayrollCustomParameters::where([['active','No'],['emp_id',$payID]])->get();
    return view('admin.PayrollMaster.manage',compact('empData','title','payment_frequency','bankData','branchData','payment_mode',
        'wa_CurrencyManager','wa_Allowances','wa_LoanType','payrollWaPaymentData','payrollAllowancesActive','payrollAllowancesDeActive','payrollLoanTypeActive','payrollLoanTypeDeActive','commissionData','payrollCommissionActive','payrollCommissionDActive','pensionData','payrollPensionActive','payrollPensionDeActive','reliefData','payrollReliefActive','payrollReliefDeActive','saccoData','payrollSaccoActive','payrollSaccoDeActive','customParameterData','customParameterActive','customParameterDeActive','parameterNonCustomers','waNonCashBenefitsData'));
  }


  public function PayrollMasterCreate(Request $request){
        try{
             $validator = Validator::make($request->all(), [
                'basic_pay' => 'required|max:255',
                'pay_frequency_id' => 'required|max:255',
                'branch_id' => 'required|max:255',
                'account_name' => 'required|max:255',
                'account_number' => 'required|max:20',
                'currency_id' => 'required|max:20',
                'nssf_number' => 'required|max:20',
                'bank_id' => 'required|max:20',
                'relief' => 'required|max:255',
                'voluntary_nssf' => 'required|max:255',
                'payment_mode_id' => 'required|max:255',
                'active' => 'required|max:255',

                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $payrollWaPaymentCreate = new PayrollWaPayment();
                $payrollWaPaymentCreate->emp_id = $request->emp_id;
                $payrollWaPaymentCreate->basic_pay = $request->basic_pay;
                $payrollWaPaymentCreate->pay_frequency_id = $request->pay_frequency_id;
                $payrollWaPaymentCreate->branch_id = $request->branch_id;
                $payrollWaPaymentCreate->account_name = $request->account_name;
                $payrollWaPaymentCreate->account_number = $request->account_number;
                $payrollWaPaymentCreate->currency_id = $request->currency_id;
                $payrollWaPaymentCreate->nssf_number = $request->nssf_number;
                $payrollWaPaymentCreate->voluntary_nssf = $request->voluntary_nssf;
                $payrollWaPaymentCreate->active = $request->active;
                $payrollWaPaymentCreate->payment_mode_id = $request->payment_mode_id;
                $payrollWaPaymentCreate->bank_id = $request->bank_id;
                $payrollWaPaymentCreate->relief = $request->relief;
                if (!empty($request->nhif)) {
                    $payrollWaPaymentCreate->nhif = 'On';
                }else{
                    $payrollWaPaymentCreate->nhif = 'Off';
                }
                if (!empty($request->paye)) {
                    $payrollWaPaymentCreate->paye = 'On';
                }else{
                    $payrollWaPaymentCreate->paye = 'Off';
                }
                $payrollWaPaymentCreate->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

     public function PayrollMasterUpdate(Request $request,$updateID){
        try{
             $validator = Validator::make($request->all(), [
                'basic_pay' => 'required|max:255',
                'pay_frequency_id' => 'required|max:255',
                'branch_id' => 'required|max:255',
                'account_name' => 'required|max:255',
                'account_number' => 'required|max:20',
                'currency_id' => 'required|max:20',
                'nssf_number' => 'required|max:20',
                'bank_id' => 'required|max:20',
                'relief' => 'required|max:255',
                'voluntary_nssf' => 'required|max:255',
                'payment_mode_id' => 'required|max:255',
                'active' => 'required|max:255',

                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $payrollWaPaymentUpdate = PayrollWaPayment::where('id',$updateID)->first();
                $payrollWaPaymentUpdate->emp_id = $request->emp_id;
                $payrollWaPaymentUpdate->basic_pay = $request->basic_pay;
                $payrollWaPaymentUpdate->pay_frequency_id = $request->pay_frequency_id;
                $payrollWaPaymentUpdate->branch_id = $request->branch_id;
                $payrollWaPaymentUpdate->account_name = $request->account_name;
                $payrollWaPaymentUpdate->account_number = $request->account_number;
                $payrollWaPaymentUpdate->currency_id = $request->currency_id;
                $payrollWaPaymentUpdate->nssf_number = $request->nssf_number;
                $payrollWaPaymentUpdate->voluntary_nssf = $request->voluntary_nssf;
                $payrollWaPaymentUpdate->active = $request->active;
                $payrollWaPaymentUpdate->payment_mode_id = $request->payment_mode_id;
                $payrollWaPaymentUpdate->bank_id = $request->bank_id;
                $payrollWaPaymentUpdate->relief = $request->relief;
                if (!empty($request->nhif)) {
                    $payrollWaPaymentUpdate->nhif = 'On';
                }else{
                    $payrollWaPaymentUpdate->nhif = 'Off';
                }
                if (!empty($request->paye)) {
                    $payrollWaPaymentUpdate->paye = 'On';
                }else{
                    $payrollWaPaymentUpdate->paye = 'Off';
                }
                $payrollWaPaymentUpdate->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

public function AllowancesCreate(Request $request){
    try{
             $validator = Validator::make($request->all(), [
                'allowance_id' => 'required|max:255',
                'year' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'active' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'month' => 'required|max:255',
                'narration' => 'required|max:255',

                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $payrollAllowancesCreate = new PayrollAllowances;
                $payrollAllowancesCreate->emp_id = $request->emp_id;
                $payrollAllowancesCreate->allowance_id = $request->allowance_id;
                $payrollAllowancesCreate->year = $request->year;
                $payrollAllowancesCreate->ref_number = $request->ref_number;
                $payrollAllowancesCreate->active = $request->active;
                $payrollAllowancesCreate->amount = $request->amount;
                $payrollAllowancesCreate->month = $request->month;
                $payrollAllowancesCreate->narration = $request->narration;
                $payrollAllowancesCreate->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

    public function LoansCreate(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'loan_type_id' => 'required|max:255',
                'year' => 'required|max:255',
                'monthly_deduction' => 'required|max:255',
                'active' => 'required|max:255',
                'principal_deducted' => 'required|max:255',
                'month' => 'required|max:255',
                ]);
            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $payrollLoanTypeAdd = new PayrollLoanType;
                $payrollLoanTypeAdd->emp_id = $request->emp_id;
                $payrollLoanTypeAdd->loan_type_id = $request->loan_type_id;
                $payrollLoanTypeAdd->year = $request->year;
                $payrollLoanTypeAdd->monthly_deduction = $request->monthly_deduction;
                $payrollLoanTypeAdd->principal_deducted = $request->principal_deducted;
                $payrollLoanTypeAdd->active = $request->active;
                $payrollLoanTypeAdd->month = $request->month;
                $payrollLoanTypeAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

     public function CommissionCreate(Request $request){
        // dd($request->all());
      try{
             $validator = Validator::make($request->all(), [
                'commission_id' => 'required|max:255',
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'narration' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $payrollCommissionAdd = new PayrollCommission;
                $payrollCommissionAdd->emp_id = $request->emp_id;
                $payrollCommissionAdd->commission_id = $request->commission_id;
                $payrollCommissionAdd->year = $request->year;
                $payrollCommissionAdd->month = $request->month;
                $payrollCommissionAdd->ref_number = $request->ref_number;
                $payrollCommissionAdd->amount = $request->amount;
                $payrollCommissionAdd->narration = $request->narration;
                $payrollCommissionAdd->active = $request->active;
                $payrollCommissionAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     } 


     public function PensionCreate(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'pension_id' => 'required|max:255',
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'narration' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $payrollPensionAdd = new PayrollPension;
                $payrollPensionAdd->emp_id = $request->emp_id;
                $payrollPensionAdd->pension_id = $request->pension_id;
                $payrollPensionAdd->year = $request->year;
                $payrollPensionAdd->month = $request->month;
                $payrollPensionAdd->ref_number = $request->ref_number;
                $payrollPensionAdd->amount = $request->amount;
                $payrollPensionAdd->narration = $request->narration;
                $payrollPensionAdd->active = $request->active;
                $payrollPensionAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

     public function PayrollReliefCreate(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'relief_id' => 'required|max:255',
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'narration' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $payrollPensionAdd = new PayrollRelief;
                $payrollPensionAdd->emp_id = $request->emp_id;
                $payrollPensionAdd->relief_id = $request->relief_id;
                $payrollPensionAdd->year = $request->year;
                $payrollPensionAdd->month = $request->month;
                $payrollPensionAdd->ref_number = $request->ref_number;
                $payrollPensionAdd->amount = $request->amount;
                $payrollPensionAdd->narration = $request->narration;
                $payrollPensionAdd->active = $request->active;
                $payrollPensionAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

     public function PayrollSaccoCreate(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'sacco_id' => 'required|max:255',
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'narration' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $payrollSaccoAdd = new PayrollSacco;
                $payrollSaccoAdd->emp_id = $request->emp_id;
                $payrollSaccoAdd->sacco_id = $request->sacco_id;
                $payrollSaccoAdd->year = $request->year;
                $payrollSaccoAdd->month = $request->month;
                $payrollSaccoAdd->ref_number = $request->ref_number;
                $payrollSaccoAdd->amount = $request->amount;
                $payrollSaccoAdd->narration = $request->narration;
                $payrollSaccoAdd->active = $request->active;
                $payrollSaccoAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

     public function PayrollCustomParametersCreate(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'custom_parameters_id' => 'required|max:255',
                'year' => 'required|max:255',
                'month' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'narration' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $customparametersAdd = new PayrollCustomParameters;
                $customparametersAdd->emp_id = $request->emp_id;
                $customparametersAdd->custom_parameters_id = $request->custom_parameters_id;
                $customparametersAdd->year = $request->year;
                $customparametersAdd->month = $request->month;
                $customparametersAdd->ref_number = $request->ref_number;
                $customparametersAdd->amount = $request->amount;
                $customparametersAdd->narration = $request->narration;
                $customparametersAdd->active = $request->active;
                $customparametersAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }


     public function NonCashBenfitStore(Request $request){
          try{
             $validator = Validator::make($request->all(), [
                'non_cash_benefits_id' => 'required|max:255',
                'year' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'amount' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'month' => 'required|max:255',
                'narration' => 'required|max:255',

                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $waNonCashBenefitsAdd = new WaNonCashBenefits;
                $waNonCashBenefitsAdd->emp_id = $request->emp_id;
                $waNonCashBenefitsAdd->non_cash_benefits_id = $request->non_cash_benefits_id;
                $waNonCashBenefitsAdd->year = $request->year;
                $waNonCashBenefitsAdd->ref_number = $request->ref_number;
                $waNonCashBenefitsAdd->amount = $request->amount;
                $waNonCashBenefitsAdd->month = $request->month;
                $waNonCashBenefitsAdd->narration = $request->narration;
                $waNonCashBenefitsAdd->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

}