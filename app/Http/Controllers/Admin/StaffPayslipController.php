<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Model\PayrollWaPayment;
use App\Model\PayrollAllowances;
use App\Model\PayrollCommission;
use App\Model\PayrollRelief;
use App\Model\PayrollSacco;
use App\Model\PayrollLoanType;
use App\Model\Allowance;
use App\Model\PayrollPension;
use App\Model\NHIF;
use App\Model\Pension;
use App\Model\CustomParameter;
use App\Model\Commission;
use App\Model\NonCashBenfit;
use App\Model\PayrollCustomParameters;
use App\Model\WaNonCashBenefits;
use App\Model\Sacco;
use PDF;
use DB;
use Excel;


class StaffPayslipController extends Controller {

    protected $title;

    public function __construct() {
        $this->title = 'Staff Payslip';
    }

    public function index(Request $request) {
      $allemp = Employee::where('status','Active')->get();
      // dd($request->all());
      if (isset($request->Search)) {
        $empData = Employee::where('id',$request->Search)->first();
        }
       $empid  = null;
       if (!empty($empData)) {
           $empid= $empData->id;
       }
       // dd($empid);

        $title = $this->title;
          $empDataSalarySilp = Employee::where('id',$empid)->first();
        $payrollWaPayment = PayrollWaPayment::where('emp_id',$empid)->first();
		$deductionVal = [];
		$earningValue = [];
        $payrollAllowancesData = PayrollAllowances::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $payrollCommission = PayrollCommission::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $saccoData = PayrollSacco::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $loanAmount = PayrollLoanType::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $pensionAmount = PayrollPension::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
        $releifData = PayrollRelief::where([['emp_id',$empid],['month',$request->month],['year',$request->year]])->get();
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

        // dd($empDataSalarySilp);

       return view('admin.staffpayslip.index',compact('empDataSalarySilp','payrollWaPayment','payrollAllowancesData','payrollCommission','payrollCustomParameters','saccoData','loanAmount','pensionAmount','releifData','payrollCustomParametersDedction','customParametersEarning','payrollearningValue','waNonCashBenefitsData','title','allemp'));        
  }

  public function PayrollProcessReport(Request $request){
         $title = $this->title;

    $payrollAllowancesPayroll= Allowance::get();
    $commissionData = Commission::get();
    $saccoDataGet = Sacco::get();
    $waNonCashBenefitsData = NonCashBenfit::get();
    $customParametersDeduction = CustomParameter::where('parameter_type','Deduction')->get();
    $pensionData = Pension::get();
    // dd($customParametersDeduction);

    // dd($waNonCashBenefitsData);
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
    $payrollAllowancesPayrollView = DB::select("select wa_employee.*,wa_payroll_payment.nssf_number as NssfAmount,wa_payroll_payment.relief AS Relief, wa_payroll_payment.basic_pay AS MonthlyPay,
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
      // dd($payrollAllowancesPayrollView);
     return view('admin.staffpayslip.payrollreports',compact('title','payrollAllowancesPayroll','payrollAllowancesPayrollView','commissionData','waNonCashBenefitsData','saccoDataGet','customParametersDeduction','pensionData'));
  }

  public function PayrollProcessPdf(Request $request){
    $payrollAllowancesPayrollPdf= Allowance::get();
    $commissionDataPdf = Commission::get();
    $saccoDataGetPdf = Sacco::get();
    $waNonCashBenefitsDataPdf = NonCashBenfit::get();
    $customParametersDeductionPdf = CustomParameter::where('parameter_type','Deduction')->get();
    $pensionDataPdf = Pension::get();
    // dd($customParametersDeduction);

    // dd($waNonCashBenefitsData);
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
    $payrollAllowancesPayrollViewPdf = DB::select("select wa_employee.*,wa_payroll_payment.nssf_number as NssfAmount,wa_payroll_payment.relief AS Relief, wa_payroll_payment.basic_pay AS MonthlyPay,
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
    $pdf = PDF::loadView('admin.staffpayslip.PayrollReportPdf',compact('title','payrollAllowancesPayrollPdf','payrollAllowancesPayrollViewPdf','commissionDataPdf','waNonCashBenefitsDataPdf','saccoDataGetPdf','customParametersDeductionPdf','pensionDataPdf'))->setPaper('a4', 'landscape');
      return $pdf->download('Payroll Report.pdf');
  }

   public function PayrollProcessExport(Request $request){      

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
    $datas = DB::select("select wa_employee.*,wa_payroll_payment.nssf_number as NssfAmount,wa_payroll_payment.relief AS Relief, wa_payroll_payment.basic_pay AS MonthlyPay,
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
      // dd($allowanceDataColoum);
       return Excel::create('wa_employee', function($excel) use($datas) {
            return  $excel->sheet('Sheet 1', function($sheet) use($datas) {
            $allowanceData = Allowance::get();
            $commissionData = Commission::get();
            $nonCashBenfit = NonCashBenfit::get();
            $saccoDataExel = Sacco::get();
            $customParametersDeductionExexel = CustomParameter::where('parameter_type','Deduction')->get();
            $pensionData = Pension::get();



            // dd($payrollAllowancesPayrollExcel);

         foreach ($allowanceData as $key => $value) {
           $allowanceDataColoum  [] = $value->allowance;
           $allowanceID [] = $value->id;
         } 


         // $payrollAllowances = PayrollAllowances::whereIn('allowance_id',$allowanceID)->where('emp_id',$value2->id)->get();
         // dd($payrollAllowances);


         foreach ($commissionData as $key => $val) {
          $commissionDataColoum [] = $val->commission;
         }

         foreach ($nonCashBenfit as $key => $val3) {
          $nonCashBenfitColum  [] = $val3->non_cash_benefit;
         }
         foreach ($saccoDataExel as $key => $saccoDataExelValue) {
           $saccoColoum [] = $saccoDataExelValue->sacco;
         }
         foreach ($customParametersDeductionExexel as $key => $customParametersDeductionValue) {
          $customParametersDeductionCoulom [] = $customParametersDeductionValue->parameter;
         }
         foreach ($pensionData as $key => $pensionDataValue) {
          $pensionDataColoum [] = $pensionDataValue->pension;
         }

         $arrayCustomCoulam = ['Overtime 1.5','Absenteeism 1.5', 'Gross Pay'];
         $taxableCoulom = ['Taxable Income','Paye','Nssf','Nhif'];
         $addmoreColoum = ['Total Deductions','Monthly Relief','Net Pay'];
         $datasheet2  = ['#','Emp No','Name','Basic Pay'];
         $datasheet  =   array();
         $datasheet[0]  =  array_merge($datasheet2,$allowanceDataColoum,$commissionDataColoum,$arrayCustomCoulam,$nonCashBenfitColum,$taxableCoulom,$saccoColoum,$customParametersDeductionCoulom,$pensionDataColoum,$addmoreColoum);
            $i=1;
          foreach($datas as $key => $datanew){
                $datasheet[$i] = array(
                @$key + 1,
                $datanew->emp_number,
                $datanew->first_name.' '.$datanew->middle_name.' '.$datanew->last_name,
                $datanew->MonthlyPay,
            );
              $i++;
            }
            // dd($datasheet);
           $sheet->fromArray($datasheet);

            });

        })->download('xlsx')->view('exports.invoices');                                                                                             

   }

}