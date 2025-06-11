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
use App\Model\Absent;
use App\Model\PayrollCustomParameters;
use App\Model\CustomParameter;
use App\Model\PayrollAllowances;
use App\Model\WaSalaryReview;
use App\Model\Paye;
use App\Model\PayrollSacco;
use App\Model\PayrollRelief;
use App\Model\PayrollPension;
use App\Model\NHIF;
use App\Model\WaNonCashBenefits;
use Illuminate\Support\Facades\Validator;
use PDF;

class SalaryReviewController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Salary Review';
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
            return view('admin.SalaryReview.index',compact(
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
                $nestedData['action'] =  "<a href='" . route('SalaryReview.Create',['id'=> $row->id])." '><button class='btn btn-primary'>Review</button></a>";
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

  public function SalaryReviewCreate(Request $request,$payID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $empData = Employee::where('id',$payID)->first();
    $wa_salaryReviewData = WaSalaryReview::where('emp_id',$payID)->get();
    $data2 = PayrollWaPayment::where('emp_id',$payID)->first();
    // dd($data2);
    return view('admin.SalaryReview.manage',compact('empData','title','absentData','absentEdit','wa_salaryReviewData','data2'));
  }



     public function SalaryStore(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'new_basic_pay' => 'required|max:255',
                'effective_date' => 'required|max:255',
                'comment' => 'required|max:255',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $payrollWaPayment = PayrollWaPayment::where('emp_id',$request->emp_id)->first();
                $waSalaryReviewAdd = new WaSalaryReview;
                $calcution = $payrollWaPayment->basic_pay  + $request->new_basic_pay;
                $waSalaryReviewAdd->emp_id = $request->emp_id;
                $waSalaryReviewAdd->new_basic_pay = $calcution;
                $waSalaryReviewAdd->old_pay = $payrollWaPayment->basic_pay;
                $waSalaryReviewAdd->effective_date = $request->effective_date;
                $waSalaryReviewAdd->comment = $request->comment;
                $waSalaryReviewAdd->save();
                if ($waSalaryReviewAdd->save()) {
                    $payrollWaPayment->basic_pay = $calcution;
                    $payrollWaPayment->save();

                } 
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

   public function ProcessPayroll(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Process Payroll';
        $model = $this->model;

        $abc =  date('F');
     if (isset($request->month)) {
         $monthGet = $request->month;
     }else{
        $monthGet = $abc;
     }

     if (isset($request->year)) {
         $yearGet = $request->year;
     }else{
        $yearGet = Date('Y');
     }

    
   if (!empty($request->Search)){
        $processReportData = DB::select("select wa_employee.*,wa_payroll_payment.nssf_number as NssfAmount,wa_payroll_payment.relief AS Relief, wa_payroll_payment.basic_pay AS MonthlyPay,
       (CASE WHEN ad.allowance IS NOT NULL THEN ad.allowance ELSE 0 END) as allowance,
       (CASE WHEN plt.deduct IS NOT NULL THEN plt.deduct ELSE 0 END) as deduct,
       (CASE WHEN Pen.Pension IS NOT NULL THEN Pen.Pension ELSE 0 END) as Pension,
       (CASE WHEN Non.NonCash IS NOT NULL THEN Non.NonCash ELSE 0 END) as NonCash,
       (CASE WHEN Rl.ReflifAmount IS NOT NULL THEN Rl.ReflifAmount ELSE 0 END) as ReflifAmount,
       (CASE WHEN CD.CustomDeduction IS NOT NULL THEN CD.CustomDeduction ELSE 0 END) as CustomDeduction,
       (CASE WHEN Saccco.SacccoAmount IS NOT NULL THEN Saccco.SacccoAmount ELSE 0 END) as SacccoAmount,
       (CASE WHEN plt2.CustomerParams IS NOT NULL THEN plt2.CustomerParams ELSE 0 END) as CustomerParams FROM wa_employee

         LEFT JOIN ( select sum(amount) allowance,emp_id from wa_payroll_allowances where month = '".$monthGet."' AND year = '".$yearGet."' group by emp_id) ad ON wa_employee.id = ad.emp_id 

          LEFT JOIN (SELECT SUM(SumAmount) CustomerParams,emp_id FROM payroll_process_views WHERE month = '".$monthGet."'  AND year = '".$yearGet."' GROUP BY emp_id ) plt2 ON wa_employee.id = plt2.emp_id 
        
          LEFT JOIN(SELECT SUM(amount) CustomDeduction,emp_id FROM payroll_custom_deduction  WHERE month='".$monthGet."' AND year = '".$yearGet."' 
          GROUP by emp_id ) CD on wa_employee.id = CD.emp_id

          LEFT JOIN ( SELECT SUM(wa_payroll_commission.amount)  deduct,emp_id
            FROM wa_payroll_commission WHERE month = '".$monthGet."' AND year = '".$yearGet."'
            GROUP BY emp_id ) plt ON wa_employee.id = plt.emp_id

           LEFT JOIN (SELECT SUM(amount) ReflifAmount,emp_id FROM wa_payroll_relief WHERE month = '".$monthGet."' AND year = '".$yearGet."' GROUP BY emp_id) Rl on wa_employee.id = Rl.emp_id

           LEFT JOIN (SELECT SUM(amount) NonCash,emp_id FROM wa_non_cash_benefits WHERE month = '".$monthGet."' AND year = '".$yearGet."' GROUP BY emp_id) Non On wa_employee.id = Non.emp_id

           LEFT JOIN (SELECT SUM(amount) Pension,emp_id FROM wa_payroll_pension WHERE month='".$monthGet."' AND  year = '".$yearGet."' GROUP BY emp_id) Pen ON wa_employee.id = Pen.emp_id

           LEFT JOIN (SELECT SUM(amount) SacccoAmount,emp_id FROM wa_payroll_sacco WHERE month = '".$monthGet."' AND year = '".$yearGet."' GROUP BY emp_id) Saccco ON wa_employee.id = Saccco.emp_id

           LEFT JOIN wa_payroll_payment on wa_employee.id = wa_payroll_payment.emp_id 
            where wa_employee.first_name = '".$request->Search."'
              ORDER BY wa_employee.id");

     # code...
   }else{

    
        $processReportData = DB::select("select wa_employee.*,wa_payroll_payment.nssf_number as NssfAmount,wa_payroll_payment.relief AS Relief, wa_payroll_payment.basic_pay AS MonthlyPay,
       (CASE WHEN ad.allowance IS NOT NULL THEN ad.allowance ELSE 0 END) as allowance,
       (CASE WHEN plt.deduct IS NOT NULL THEN plt.deduct ELSE 0 END) as deduct,
       (CASE WHEN Pen.Pension IS NOT NULL THEN Pen.Pension ELSE 0 END) as Pension,
       (CASE WHEN Non.NonCash IS NOT NULL THEN Non.NonCash ELSE 0 END) as NonCash,
       (CASE WHEN Rl.ReflifAmount IS NOT NULL THEN Rl.ReflifAmount ELSE 0 END) as ReflifAmount,
       (CASE WHEN CD.CustomDeduction IS NOT NULL THEN CD.CustomDeduction ELSE 0 END) as CustomDeduction,
       (CASE WHEN Saccco.SacccoAmount IS NOT NULL THEN Saccco.SacccoAmount ELSE 0 END) as SacccoAmount,
       (CASE WHEN plt2.CustomerParams IS NOT NULL THEN plt2.CustomerParams ELSE 0 END) as CustomerParams FROM wa_employee

         LEFT JOIN ( select sum(amount) allowance,emp_id from wa_payroll_allowances where month = '".$monthGet."' AND year = '".$yearGet."' group by emp_id) ad ON wa_employee.id = ad.emp_id 

          LEFT JOIN (SELECT SUM(SumAmount) CustomerParams,emp_id FROM payroll_process_views WHERE month = '".$monthGet."'  AND year = '".$yearGet."' GROUP BY emp_id ) plt2 ON wa_employee.id = plt2.emp_id 
        
          LEFT JOIN(SELECT SUM(amount) CustomDeduction,emp_id FROM payroll_custom_deduction  WHERE month='".$monthGet."' AND year = '".$yearGet."' 
          GROUP by emp_id ) CD on wa_employee.id = CD.emp_id

          LEFT JOIN ( SELECT SUM(wa_payroll_commission.amount)  deduct,emp_id
            FROM wa_payroll_commission WHERE month = '".$monthGet."' AND year = '".$yearGet."'
            GROUP BY emp_id ) plt ON wa_employee.id = plt.emp_id

           LEFT JOIN (SELECT SUM(amount) ReflifAmount,emp_id FROM wa_payroll_relief WHERE month = '".$monthGet."' AND year = '".$yearGet."' GROUP BY emp_id) Rl on wa_employee.id = Rl.emp_id

           LEFT JOIN (SELECT SUM(amount) NonCash,emp_id FROM wa_non_cash_benefits WHERE month = '".$monthGet."' AND year = '".$yearGet."' GROUP BY emp_id) Non On wa_employee.id = Non.emp_id

           LEFT JOIN (SELECT SUM(amount) Pension,emp_id FROM wa_payroll_pension WHERE month='".$monthGet."' AND  year = '".$yearGet."' GROUP BY emp_id) Pen ON wa_employee.id = Pen.emp_id

           LEFT JOIN (SELECT SUM(amount) SacccoAmount,emp_id FROM wa_payroll_sacco WHERE month = '".$monthGet."' AND year = '".$yearGet."' GROUP BY emp_id) Saccco ON wa_employee.id = Saccco.emp_id

           LEFT JOIN wa_payroll_payment on wa_employee.id = wa_payroll_payment.emp_id 
            where allowance > 0 or deduct > 0 or Pension > 0 or  NonCash > 0   or  SacccoAmount > 0  or   CustomerParams > 0
            or CustomDeduction > 0 
              ORDER BY wa_employee.id");
   }
        // dd($processReportData);
         // dd($processReportData);
       return view('admin.SalaryReview.ProcessPayroll',compact('title','basic_payCalcution','processReportData'));
    }



    public function ProcessPayrollPdf(Request $request,$empid){
        $empDataSalarySilp = Employee::where('id',$empid)->first();
        $payrollWaPayment = PayrollWaPayment::where('emp_id',$empid)->first();
        $payrollAllowancesData = PayrollAllowances::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $payrollCommission = PayrollCommission::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $payrollRelifPay = PayrollRelief::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->first();
        $saccoData = PayrollSacco::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $loanAmount = PayrollLoanType::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $pensionAmount = PayrollPension::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $releifData = PayrollRelief::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $nhifData = NHIF::whereBetween('to',array('0.00',$payrollWaPayment->basic_pay))->first();
        $customParametersDeduction = CustomParameter::where('parameter_type','Deduction')->get();
        $customParametersEarning = CustomParameter::where('parameter_type','Earning')->get();
        $waNonCashBenefitsData = WaNonCashBenefits::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        
        foreach ($customParametersDeduction as $key => $value) {
                $deductionVal [] = $value->id;
        }
        foreach($customParametersEarning as $key => $value2){
             $earningValue [] = $value2->id;
        }
        // dd($deductionVal);
        $payrollCustomParametersDedction = PayrollCustomParameters::whereIn('custom_parameters_id',$deductionVal)->where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        // dd($payrollCustomParameters);
        $payrollearningValue = PayrollCustomParameters::whereIn('custom_parameters_id',$earningValue)->where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
                // dd($payrollearningValue);

        $pdf = PDF::loadView('admin.SalaryReview.PayRollsProcessPdf',compact('empDataSalarySilp','payrollWaPayment','payrollAllowancesData','payrollCommission','payrollCustomParameters','payrollRelifPay','saccoData','nhifData','loanAmount','pensionAmount','releifData','payrollCustomParametersDedction','customParametersEarning','payrollearningValue','waNonCashBenefitsData'))->setPaper(array(0, 0, 595, 1141),
          'portrait');
      return $pdf->download('SALARY SILP.pdf');
    }

    public function PayrolllPayslip(Request $request,$viewId){
      // dd($request->all());
        $abc =  date('F');
     if (isset($request->month)) {
         $request->month = $request->month;
     }else{
        $request->month = $abc;
     }

     if (isset($request->year)) {
         $request->year = $request->year;
     }else{
        $request->year = Date('Y');
     }

      $title = 'Process Payroll';
        $processReportDataView = DB::select("select *,wa_payroll_payment.basic_pay as BasicMonthaly,
       (CASE WHEN Al.AllowancesAmount IS NOT NULL THEN Al.AllowancesAmount ELSE 0 END) as AllowancesAmount,
       (CASE WHEN NonCash.NonCashAmount IS NOT NULL THEN NonCash.NonCashAmount ELSE 0 END) as NonCashAmount,
        (CASE WHEN CD.CustomDeduction IS NOT NULL THEN CD.CustomDeduction ELSE 0 END) as CustomDeduction,
       (CASE WHEN Commission.CommissionAmont IS NOT NULL THEN Commission.CommissionAmont ELSE 0 END) as CommissionAmont,
       (CASE WHEN Loan.LoanTypeAmount IS NOT NULL THEN Loan.LoanTypeAmount ELSE 0 END) as LoanTypeAmount,
       (CASE WHEN Sacco.SaccoAmount IS NOT NULL THEN Sacco.SaccoAmount ELSE 0 END) as SaccoAmount,
       (CASE WHEN PayAmount.PayrollAmount IS NOT NULL THEN PayAmount.PayrollAmount ELSE 0 END) as PayAmount FROM wa_employee LEFT JOIN ( select sum(amount) AllowancesAmount,emp_id from wa_payroll_allowances where month = '".$request->month."'  AND year = '".$request->year."'  group by emp_id) Al ON wa_employee.id = Al.emp_id       
        LEFT JOIN (select SUM(amount) NonCashAmount,emp_id FROM wa_non_cash_benefits WHERE month = '".$request->month."' AND year = '".$request->year."'  GROUP BY emp_id) NonCash ON wa_employee.id = NonCash.emp_id 

          LEFT JOIN(SELECT SUM(amount) CustomDeduction,emp_id FROM payroll_custom_deduction  WHERE month='".$request->month."' AND year = '".$request->year."' 
          GROUP by emp_id ) CD on wa_employee.id = CD.emp_id

       LEFT JOIN(SELECT sum(amount) CommissionAmont,emp_id FROM wa_payroll_commission WHERE month='".$request->month."' AND year ='".$request->year."' GROUP BY emp_id) Commission ON wa_employee.id = Commission.emp_id
       LEFT JOIN (SELECT SUM(monthly_deduction) LoanTypeAmount,emp_id FROM wa_payroll_loan_type WHERE month ='".$request->month."'  AND  year='".$request->year."'  GROUP BY emp_id ) Loan ON wa_employee.id = Loan.emp_id
       LEFT JOIN (SELECT SUM(amount) SaccoAmount,emp_id FROM wa_payroll_sacco WHERE month='".$request->month."'  And year ='".$request->year."'  GROUP BY emp_id) Sacco on wa_employee.id = Sacco.emp_id 
       LEFT JOIN(SELECT SUM(amount) PayrollAmount,emp_id FROM payroll_process_views WHERE month='".$request->month."'  And year='".$request->year."' 
           GROUP by emp_id) PayAmount ON wa_employee.id = PayAmount.emp_id LEFT JOIN wa_payroll_payment on wa_employee.id = wa_payroll_payment.emp_id WHERE wa_employee.id = '".$viewId."'  ORDER BY wa_employee.id");


// dd($processReportDataView);
       $pensionAmount = PayrollPension::where([['emp_id',$viewId],['month',$request->month],['year',$request->year]])->get();
       $wa_payroll_reliefData = PayrollRelief::where([['emp_id',$viewId],['month',$request->month],['year',$request->year]])->get();

        foreach ($pensionAmount as $key => $value) {
            $totalAmount [] = $value->amount;
        }
        foreach ($wa_payroll_reliefData as $key => $value2) {
            $totalAmountRelief [] = $value2->amount;
        }
          //= dd($processReportDataView);
      return view('admin.SalaryReview.paysilp',compact('title','processReportDataView','totalAmount','totalAmountRelief'));
    }

}