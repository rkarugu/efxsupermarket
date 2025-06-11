<?php

use App\Model\Paye;
use App\Model\PayrollWaPayment;
use App\Model\PayrollAllowances;
use App\Model\PayrollCommission;
use App\Model\PayrollProcessViews;
use App\Model\PayrollRelief;
use App\Model\PayrollPension;
use App\Model\WaNonCashBenefits;
use App\Model\PayrollSacco;
use App\Model\PayrollCustomParameters;
use App\Model\NHIF;

?>
@if(count($payrollAllowancesPayrollViewPdf) > 0)
            <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" id="TableID" style="overflow: scroll;white-space: nowrap;width: 700px;">
    <thead> 
      <tr style="overflow: scroll;white-space: nowrap;width: 700px;">
        <th align="left" scope="col">Emp No</th>
        <th align="left" scope="col">Name</th>
        <th align="left" scope="col">ID No.</th>
        <th align="right" scope="col">BASIC PAY</th>
        @foreach($payrollAllowancesPayrollPdf as $value)
        <th align="right" scope="col">{{$value->allowance}}</th>
        @endforeach
        @foreach($commissionDataPdf as $commissionDataVal)
        <th align="right" scope="col">{{$commissionDataVal->commission}}</th>
        @endforeach
        <th align="right" scope="col">Overtime 1.5</th>
        <th align="right" scope="col">Absenteeism</th>
        <th align="right" scope="col">Gross Pay</th>
        @foreach($waNonCashBenefitsDataPdf as $nonValue)
        <th align="right" scope="col">{{$nonValue->non_cash_benefit}}</th>
        @endforeach
        <th align="right" scope="col">Taxable Income</th>
        <th align="right" scope="col">Paye</th>
        <th align="right" scope="col">Nssf</th>
        <th align="right" scope="col">Nhif</th>
        @foreach($saccoDataGetPdf as $saccoValue)
        <th align="right" scope="col">{{$saccoValue->sacco}}</th>
        @endforeach
        @foreach($customParametersDeductionPdf as $customValue)
        <th align="right" scope="col">{{$customValue->parameter}}</th>
        @endforeach
        @foreach($pensionDataPdf as $pensionDataValue)
        <th align="right" scope="col">{{$pensionDataValue->pension}}</th>
        @endforeach
        <th align="right" scope="col">Total Deductions</th>
        <th align="right" scope="col">Monthly Relief</th>
        <th align="right" scope="col">Net Pay</th>
      </tr>
    </thead>
    <tbody>
      @foreach($payrollAllowancesPayrollViewPdf as $value2)
      <?php
               $payData = Paye::orderBy('id','DESC')->get();

        $grossSalary = $value2->MonthlyPay +  $value2->allowance +  $value2->deduct + $value2->CustomerParams;

        $taxableAmount = $grossSalary  + $value2->NonCash - $value2->NssfAmount - $value2->Pension;


    $salary = $taxableAmount;
    $tax = 0; 
    $abc = round($grossSalary,0);
    $nhifmodel = NHIF::where([['from','<',$abc],['to','>',$abc]])->first();
    $amountReplace = $nhifmodel->rate;
    $abcRelpace = str_replace(',', '', $amountReplace);
    $nhifData = $abcRelpace;

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
    }
    
    $payeData = $tax2 - $value2->Relief - $value2->ReflifAmount;
    $totalDeduction = $payeData + $value2->NssfAmount +  $nhifData + $value2->SacccoAmount + $value2->CustomDeduction + $value2->Pension;

        ?>

    <tr>
        <td>{{$value2->emp_number}}</td>
        <td class="Name">{{$value2->first_name}} {{$value2->middle_name}} {{$value2->last_name}}</td>
        <td>{{$value2->Id_number}}</td>
        <td align="right"><span>{{manageAmountFormat($value2->MonthlyPay)}}</span></td>
        @foreach($payrollAllowancesPayrollPdf as $value)
        <?php
         $payrollAllowances = PayrollAllowances::where([['allowance_id',$value->id],['emp_id',$value2->id]])->first();
        ?>
        <td align="right"><span class="taxable">{{manageAmountFormat(@$payrollAllowances->amount)}}</span></td>
        @endforeach

       @foreach($commissionDataPdf as $commissionDataVal)
       <?php
        $commissionPayroll = PayrollCommission::where([['commission_id',$commissionDataVal->id],['emp_id',$value2->id]])->first();
        // dd($commissionDataVal);
        ?>
        <td align="right" scope="col">{{manageAmountFormat(@$commissionPayroll->amount)}}</td>
        @endforeach
        <td align="right" scope="col">0.00</td>
        <td align="right" scope="col">0.00</td>
        <td align="right" scope="col">{{manageAmountFormat($grossSalary)}}</td>
        @foreach($waNonCashBenefitsDataPdf as $nonValue)
        <?php      
          $waNonCashBenefitsAm = WaNonCashBenefits::where([['emp_id',$value2->id],['non_cash_benefits_id',$nonValue->id]])->first();
           ?>
        <td align="right" scope="col">{{manageAmountFormat(@$waNonCashBenefitsAm->amount)}}</td>
        @endforeach
        <td align="right" scope="col">{{manageAmountFormat($taxableAmount)}}</td>
        <td align="right" scope="col">{{manageAmountFormat($payeData)}}</td>
        <td align="right" scope="col">{{manageAmountFormat($value2->NssfAmount)}}</td>
        <td align="right" scope="col">{{manageAmountFormat($nhifData)}}</td>
        @foreach($saccoDataGetPdf as $saccoValue)
        <?php
          $saccoDataAmount = PayrollSacco::where([['emp_id',$value2->id],['sacco_id',$saccoValue->id]])->first();
         ?>
          <td align="right" scope="col">{{manageAmountFormat(@$saccoDataAmount->amount)}}</td>
        @endforeach
        @foreach($customParametersDeductionPdf as $customValue)
         <?php
          $deductionAmount = PayrollCustomParameters::where([['emp_id',$value2->id],['custom_parameters_id',
            $customValue->id]])->first();
         ?>
         <td align="right" scope="col">{{manageAmountFormat(@$deductionAmount->amount)}}</td>
        @endforeach
         @foreach($pensionDataPdf as $pensionDataValue)
         <?php $pensionAmount = PayrollPension::where([['emp_id',$value2->id],['pension_id',$pensionDataValue->id]])->first();?>
        <td align="right" scope="col">{{manageAmountFormat(@$pensionAmount->amount)}}</td>
        @endforeach
        <td align="right" scope="col">{{manageAmountFormat(@$totalDeduction)}}</td>
        <td align="right" scope="col">{{manageAmountFormat(@$value2->ReflifAmount + $value2->Relief )}}</td>
        <td align="right" scope="col">{{manageAmountFormat(@$grossSalary -$totalDeduction )}}</td>
      </tr>@endforeach
    </tbody>
  </table>@endif