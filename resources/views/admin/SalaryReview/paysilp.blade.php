<?php
use App\Model\Paye;
use App\Model\NHIF;
?>
@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
   <section class="content">

                   <div class="box box-primary" style="margin-top: 20px;">
      <div class="box-header with-border no-padding-h-b"><hr>
    <div class="awidget full-width">
        <div class="awidget-head">
            <h3>
                Employee Profile</h3>
        </div>
        <div class="awidget-body">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <a href="#">
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$processReportDataView[0]->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        
                        </a>
                </div>
                <div class="col-md-9 col-sm-9">
                    
                            <table class="table">
                        
                            <tbody>
                            <tr>
                                <td>
                                    <strong>Employee No.</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        {{$processReportDataView[0]->emp_number}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Employee Name</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        {{$processReportDataView[0]->first_name}}
                                        {{$processReportDataView[0]->last_name}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>PIN Number</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        {{$processReportDataView[0]->pin_number}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>NSSF Number</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        {{$processReportDataView[0]->nssf_no}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>NHIF Number</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        {{$processReportDataView[0]->nhif_no}}</b>
                                </td>
                            </tr>
                        
                            </tbody></table>
                        
                    
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
                     <div class="box box-primary" style="margin-top: 20px;">
      <div class="box-header with-border no-padding-h-b"><hr>
        <div class="awidget full-width">
        <div class="awidget-head">
            <h3>
                Employee Payroll Overview</h3>
        </div>
        <div class="awidget-body">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    
                            <table class="table">
                        
                            <tbody><tr>
                                <td>
                                    <strong>Allowances</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->AllowancesAmount)}}</b>
                                </td>
                                <td>
                                    <strong>Commissions</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                         {{manageAmountFormat($processReportDataView[0]->CommissionAmont)}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Non Cash Benefits</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->NonCashAmount)}}</b>
                                </td>
                                <td>
                                    <strong>Other Additions</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        0.00</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Liabilities</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        </b>
                                </td>
                                <td>
                                    <strong>Co-operatives</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        0.00</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Saccos</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->SaccoAmount)}}</b>
                                </td>
                                <td>
                                    <strong>Pension</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.{{manageAmountFormat(@array_sum($totalAmount))}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Other Deductions</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        0.00</b>
                                </td>
                                <td>
                                    <strong>Advances</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        0.00</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Loans</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->LoanTypeAmount)}}</b>
                                </td>
                                <td>
                                    <strong>Basic Pay</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->BasicMonthaly)}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Gross Pay</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->BasicMonthaly + $processReportDataView[0]->AllowancesAmount + $processReportDataView[0]->CommissionAmont + $processReportDataView[0]->PayrollAmount)}}</b>
                                </td>
                                <td>
                                    <strong>Taxable Pay</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <?php 



                                      $abc = @array_sum($totalAmount);
                                      $abcRelif = @array_sum($totalAmountRelief);

                                     $totalAmount2 = $processReportDataView[0]->BasicMonthaly + $processReportDataView[0]->AllowancesAmount + $processReportDataView[0]->CommissionAmont + $processReportDataView[0]->PayrollAmount;

                                     $taxableIncome = $totalAmount2  + $processReportDataView[0]->NonCashAmount - 
                                      $processReportDataView[0]->nssf_number - $abc;
                                      ?>

                                    <b>Kshs.
                                     {{manageAmountFormat($taxableIncome)}}</b>

                                     <?php   


// dd($totalAmount2);
                                     $payData = Paye::orderBy('id','DESC')->get();




    $salary = $taxableIncome;
    $tax = 0; 
    $abc2 = round($totalAmount2,0);
    $nhifmodel = NHIF::where([['from','<',$abc2],['to','>',$abc2]])->first();
    $amountReplace = $nhifmodel->rate;
    $abcRelpace = str_replace(',', '', $amountReplace);
    $nhifData = $abcRelpace;
 // dd($salary);
  foreach ($payData as $key => $valuePay) {
      $valuePay->to = str_replace(',', '', $valuePay->to);
      $valuePay->from = str_replace(',', '', $valuePay->from);
      $salary = str_replace(',', '', $salary);
      $valuePay->rate = str_replace('%', '', $valuePay->rate);
      if (($salary - $valuePay->from) > 0) { 
        if (($salary - $valuePay->to) > 0) {
          $taxAmount = $valuePay->to - $valuePay->from;
        }else{
          $taxAmount = $salary - $valuePay->from;
        }
        $slotTax = ((($taxAmount)) / 100) * $valuePay->rate;
        $tax  += $slotTax;
        $tax2 = $tax;
      }  

        $payeData = $tax2 - $processReportDataView[0]->relief - $abcRelif;

        $totalDeducation = $abc  + $processReportDataView[0]->SaccoAmount + $nhifData +  $processReportDataView[0]->nssf_number + $payeData;

       // dd($abc);
       // echo "string".$totalDeducation;
        $TotalAmountNet2 = $totalAmount2  - $totalDeducation - $processReportDataView[0]->CustomDeduction;





    }?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>PAYE</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($payeData)}}</b>
                                </td>
                                <td>
                                    <strong>NSSF</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->nssf_number)}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>NHIF</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($nhifData)}}</b>
                                </td>
                                <td>
                                    <strong>Relief</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($processReportDataView[0]->relief)}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Net Pay</strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>Kshs.
                                        {{manageAmountFormat($TotalAmountNet2)}}</b>
                                </td>
                                <td>
                                    <strong>Paid By </strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Bank </strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        Stanbic Bank</b>
                                </td>
                                <td>
                                    <strong>Branch </strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        Waiyaki Way</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Account Name </strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                        {{$processReportDataView[0]->account_name}}</b>
                                </td>
                                <td>
                                    <strong>Account No </strong>
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    <b>
                                         {{$processReportDataView[0]->account_number}}</b>
                                </td>
                            </tr>
                        
                            </tbody></table>
                        
                </div>
            </div>
        </div>
    </div>
    </div>
  </div>
</div>
        </section>
    @endsection
    @section('uniquepagescript')

@endsection
