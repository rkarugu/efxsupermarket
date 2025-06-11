<?php

use App\Model\Paye;
use App\Model\NHIF;


?>
@extends('layouts.admin.admin')
@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
.dataTables_wrapper table th, td {
     border-right:0px  !important;
}
</style>
    <!-- Main content -->
   <section class="content">
    <h4>Staff Payslips</h4  >
                   <div class="box box-primary" style="margin-top: 20px;">
      <div class="box-header with-border no-padding-h-b"><hr>
                  <form method="get">
                <div class="row">
                  <div class="col-lg-4">
                    <div class="form-group">
                    <label>Month</label>
                    @if(Request::get('month'))
                    <select class="form-control" name="month" id="month" >
                        <option value=''>--Select Month--</option>
                        <option  value='Janaury'{{Request::get('month') == 'Janaury' ? 'selected="selected"' : ''}}>Janaury</option>
                        <option value='February'{{Request::get('month') == 'February' ? 'selected="selected"' : ''}}>February</option>
                        <option value='March'{{Request::get('month') == 'March' ? 'selected="selected"' : ''}}>March</option>
                        <option value='April'{{Request::get('month') == 'April' ? 'selected="selected"' : ''}}>April</option>
                        <option value='May'{{Request::get('month') == 'May' ? 'selected="selected"' : ''}}>May</option>
                        <option value='June'{{Request::get('month') == 'June' ? 'selected="selected"' : ''}}>June</option>
                        <option value='July'{{Request::get('month') == 'July' ? 'selected="selected"' : ''}}>July</option>
                        <option value='August'{{Request::get('month') == 'August' ? 'selected="selected"' : ''}}>August</option>
                        <option value='September'{{Request::get('month') == 'September' ? 'selected="selected"' : ''}}>September</option>
                        <option value='October'{{Request::get('month') == 'October' ? 'selected="selected"' : ''}}>October</option>
                        <option value='November'{{Request::get('month') == 'November' ? 'selected="selected"' : ''}}>November</option>
                        <option value='December'{{Request::get('month') == 'December' ? 'selected="selected"' : ''}}>December</option>
                      </select>
                      @else()
                        <select class="form-control" name="month" id="month" >
                        <option value=''>--Select Month--</option>
                        <option  value='Janaury'{{Date('F') == 'Janaury' ? 'selected="selected"' : ''}}>Janaury</option>
                        <option value='February'{{Date('F') == 'February' ? 'selected="selected"' : ''}}>February</option>
                        <option value='March'{{Date('F') == 'March' ? 'selected="selected"' : ''}}>March</option>
                        <option value='April'{{Date('F') == 'April' ? 'selected="selected"' : ''}}>April</option>
                        <option value='May'{{Date('F') == 'May' ? 'selected="selected"' : ''}}>May</option>
                        <option value='June'{{Date('F') == 'June' ? 'selected="selected"' : ''}}>June</option>
                        <option value='July'{{Date('F') == 'July' ? 'selected="selected"' : ''}}>July</option>
                        <option value='August'{{Date('F') == 'August' ? 'selected="selected"' : ''}}>August</option>
                        <option value='September'{{Date('F') == 'September' ? 'selected="selected"' : ''}}>September</option>
                        <option value='October'{{Date('F') == 'October' ? 'selected="selected"' : ''}}>October</option>
                        <option value='November'{{Date('F') == 'November' ? 'selected="selected"' : ''}}>November</option>
                        <option value='December'{{Date('F') == 'December' ? 'selected="selected"' : ''}}>December</option>
                      </select>
                      @endif
                  </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                    <label>Year</label>
                   {!! Form::selectYear('year', '2021',2000, 2050,['class'=>'form-control','id'=>'year']) !!}

                  </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                    <label>Search</label>
                     <select class="form-control" name="Search" >
                        <option value=''>--Select--</option>
                        @foreach($allemp as $value)
                        <option  value='{{$value->id}}'{{$value->id == Request::get('Search') ? 'selected="selected"' : ''}}>{{$value->first_name}} {{$value->middle_name}} {{$value->last_name}}</option>@endforeach
                      </select>
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5">
                  <button class="btn btn-info" type="submit">Preview Payslips</button>
                  <button class="btn btn-info" type="button" id="print-btn">Print Payslip</button>
                     @if(count($payrollAllowancesData) > 0 || count($payrollCommission) > 0 || count($saccoData) > 0 || count($loanAmount) > 0  || count($pensionAmount) > 0 || count($releifData) > 0 || count($waNonCashBenefitsData) > 0 )
                     <a href="{{route('ProcessPayroll.pdf',['id'=>$empDataSalarySilp->id])}}?month={{Request::get('month')}}&year={{Request::get('year')}}&Search=">
                  <button class="btn btn-danger" type="button">Export to Pdf</button></a>
                  @else() 
                  <button class="btn btn-danger" type="button">Export to Pdf</button>
                  @endif()
                </div>
              </div>
            </form>
            <hr>
     </div>
   </div>
   @if(count($payrollAllowancesData) > 0 || count($payrollCommission) > 0 || count($saccoData) > 0 || count($loanAmount) > 0  || count($pensionAmount) > 0 || count($releifData) > 0 || count($waNonCashBenefitsData) > 0 )
   <div class="row" id="TableID">
   <div class="col-lg-7">
    <div class="box-header with-border no-padding-h-b box box-primary" id="card-id" style="padding:24px;border:5px solid">
 <table style="border-collapse: collapse;width:100%;border:1px solid gray;margin: auto;">
         <tr>
            <td style="padding: 10px;border-bottom: 1px solid gray"><b></b></td>
            <td style="padding: 10px;border-bottom: 1px solid gray">
               <table style="width: 50%">
                  <tr>
                     <td style="text-align: center;width: 100%;">
                      <img style="width: 100px;height: 100px;" src="http://demo2server.com/production/uploads/restaurants/thumb/16068906231524237664.jpg"></b></td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td style="padding: 5px;font-size: 14px;"><b><u>NAME</u></b></td>
            <td style="padding: 5px;border-bottom: 1px dotted gray">
               {{@$empDataSalarySilp->first_name}} {{@$empDataSalarySilp->last_name}}
            </td>
         </tr>
         <tr>
            <td style="padding: 5px;font-size: 14px;"><b><u>STAFF NO</u></b></td>
            <td style="">
               <table style="width: 80%;margin: auto;">
                  <tr>
                     <td style="padding: 5px;border-bottom: 1px dotted gray">{{$empDataSalarySilp->staff_number}}</td>
                     <td style="padding: 5px;text-align: center;font-size: 14px;"><b><u>PIN NUMBER</u></b></td>
                     <td style="padding: 5px;border-bottom: 1px dotted gray">{{$empDataSalarySilp->pin_number}}</td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td style="padding: 5px;font-size: 14px;"><b><u>JOB TITLE</u></b></td>
            <td style="padding: 5px;border-bottom: 1px dotted gray">
               {{$empDataSalarySilp->JobTitleData ? $empDataSalarySilp->JobTitleData->job_title : 'NA'}}
            </td>
         </tr>
         <tr>
            <td style="padding: 5px;font-size: 14px;"><b><u>JOB GROUP</u></b></td>
            <td style="padding: 5px;border-bottom: 1px dotted gray">
               {{$empDataSalarySilp->JobData ? $empDataSalarySilp->JobData->job_group : 'NA'}}
            </td>
         </tr>

         <tr>
            <td style="padding: 5px;font-size: 14px;"><b><u>BANK NAME</u></b></td>
            <td style="padding: 5px;border-bottom: 1px dotted gray">
               {{$empDataSalarySilp->BankData ? $empDataSalarySilp->BankData->bank : 'NA'}}
            </td>
         </tr>
         <tr>
            <td style="padding: 5px;font-size: 14px;"><b><u>BANK A/C</u></b></td>
            <td style="padding: 5px;border-bottom: 1px dotted gray">
               {{$empDataSalarySilp->account_no}}
            </td>
         </tr>
      </table>
      <table style="border-collapse: collapse;width: 100%;border-right:1px solid gray;border-left:1px solid gray;border-bottom:1px solid gray;margin: auto;">
         <tr>
            <td style="padding: 5px;font-size: 14px" colspan="2"><b><u>Basic Pay</u></b></td>
         </tr>
         <tr>
            <td style="padding: 5px;">Basic Pay</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat(@$payrollWaPayment->basic_pay)}}
            </td>
         </tr>

         <?php
           foreach($pensionAmount as $pensionAmountVal){
             $pensionSum [] = $pensionAmountVal->amount;
             $arraySumPension = @array_sum($pensionSum);
          }

           foreach($releifData as $releifDataVal) 
           $relifSum [] = $releifDataVal->amount;
           $arraySumrelifSum = @array_sum($relifSum);
         ?>
        
         @foreach($payrollAllowancesData as $value)
         @php
          $grossSalary [] = $value->amount;
          @endphp
         <tr>
            <td style="padding: 5px;">{{$value->AllowanceData ? $value->AllowanceData->allowance : 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($value->amount)}}
            </td>
         </tr>
         @endforeach
         @foreach($payrollCommission as $val)
         @php 
          $payrollCommissionSumAmount [] = $val->amount;
         @endphp
         <tr>
            <td style="padding:5px;">{{$val->CommissionType ? $val->CommissionType->commission : 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">{{manageAmountFormat($val->amount)}}</td>
         </tr>
         @endforeach
        @if(count($payrollearningValue) > 0)
         @foreach($payrollearningValue as $val3)
         @php 
          $payrollCustomParametersSumAmount [] = $val3->amount;
         @endphp
         <tr>
            <td style="padding:5px;">{{$val3->CustomParameterData ? $val3->CustomParameterData->parameter : ''}}</td>
            <td style="padding: 5px;text-align: right;">{{ $val3->amount ?  manageAmountFormat($val3->amount) : ''}}</td>
         </tr>
         @endforeach
         @endif
         <?php 
            $payrollAllowancesData =  @array_sum($grossSalary); 
            $payrollCommissionSum  =  @array_sum($payrollCommissionSumAmount);
            $payrollCustomParametersSum = @array_sum(@$payrollCustomParametersSumAmount);
            ?>
         <tr>
            <td style="padding: 5px;">Gross Salary</td>
            <td style="padding: 5px;text-align: right;">

              <?php  $totalAM =  @$payrollWaPayment->basic_pay + @$payrollAllowancesData + @$payrollCommissionSum + @$payrollCustomParametersSum?>


               {{manageAmountFormat($payrollWaPayment->basic_pay + $payrollAllowancesData + $payrollCommissionSum + $payrollCustomParametersSum)}}
            </td>
         </tr>
         @if(count($waNonCashBenefitsData) > 0)
          <tr>
            <td style="padding: 5px;font-size: 14px" colspan="2"><b><u>Non Cash benefit</u></b></td>
         </tr>
            @foreach($waNonCashBenefitsData as $nonValue)
            <?php
             $nonAmount [] = $nonValue->amount;
             $nonAmountSum = array_sum($nonAmount);
            ?>
         <tr>
            <td style="padding: 5px;">
             {{$nonValue->NonCashBenfitData ? $nonValue->NonCashBenfitData->non_cash_benefit : 'NA'}}
              </td>
            <td style="padding: 5px;text-align: right;">{{ $nonValue->amount ?  manageAmountFormat($nonValue->amount) : ''}}</td>
         </tr>@endforeach @endif
          <tr>
            <td style="padding: 5px;font-size: 14px" colspan="2"><b><u>Taxable Income</u></b></td>
            <tr>
            <td style="padding: 5px;">Taxable Income
              </td>
              <?php
                 $taxableIncome = $totalAM  + @$nonAmountSum -$payrollWaPayment->nssf_number - @$arraySumPension;
              ?>
            <td style="padding: 5px;text-align: right;">{{ manageAmountFormat(@$taxableIncome) }}</td>
         </tr>
         </tr>


  <?php




 $payData = Paye::orderBy('id','DESC')->get();

 $salary = @$taxableIncome;
 $tax = 0; 
 $abc = round($totalAM,0);
 $nhifmodel = NHIF::where([['from','<',$abc],['to','>',$abc]])->first();
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
    }

   $totalAmountSum = $tax2 - $payrollWaPayment->relief -  $arraySumrelifSum;
    // dd($tax2);
   ?>
          <tr>
            <td style="padding: 5px;font-size: 14px" colspan="2"><b><u>Payroll Deductions</u></b></td>
         </tr>
           <tr>
            <td style="padding: 5px;">Paye</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($totalAmountSum)}}
            </td>
         </tr>
          @foreach($loanAmount as $value2)
          @php $loanAmountSum [] = $value2->monthly_deduction;
          $arraysumLoan = array_sum($loanAmountSum);
           @endphp
         <tr>
            <td style="padding: 5px;">{{$value2->LoanTypeData ? $value2->LoanTypeData->loan_type : 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">
               {{$value2->monthly_deduction}}
            </td>
         </tr>@endforeach
         @foreach($pensionAmount as $pensionAmountVal)
         <tr>
            <td style="padding: 5px;">{{$pensionAmountVal->PayrollPensionData ? $pensionAmountVal->PayrollPensionData->pension : 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($pensionAmountVal->amount)}}
            </td>
         </tr>
         @endforeach
         <tr>
            <td style="padding: 5px;">Nhif</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($nhifData)}}
            </td>
         </tr>
          <tr>
            <td style="padding: 5px;">Nssf</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($payrollWaPayment->nssf_number)}}
            </td>
         </tr>
         @foreach($saccoData as $saccoDataVal)
          
          @php 
           $saccoDataAm [] = $saccoDataVal->amount;  
           $sccooSum = array_sum($saccoDataAm); 
          @endphp

         <tr>
            <td style="padding: 5px;">{{$saccoDataVal->SaccoData ? $saccoDataVal->SaccoData->sacco : 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($saccoDataVal->amount)}}
            </td>
         </tr>@endforeach
        @if(count($payrollCustomParametersDedction) > 0)
          @foreach($payrollCustomParametersDedction as $val3)
         @php 
          $payrollCustomParametersSumAmount2 [] = $val3->amount;
          $payrollCustomSum = @array_sum($payrollCustomParametersSumAmount2);
         @endphp
         <tr>
            <td style="padding:5px;">{{$val3->CustomParameterData ? $val3->CustomParameterData->parameter : 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">{{manageAmountFormat($val3->amount)."\n"}}</td>
         </tr>
         @endforeach
         @endif
          <tr>
            <td style="padding: 5px;font-size: 14px" colspan="2"><b><u>TOTAL DEDUCTIONS</u></b></td>
         </tr>
         <tr>
            <td style="padding: 5px;">Total Deductions</td>
            <td style="padding: 5px;text-align: right;">

               {{manageAmountFormat($totalAmountSum + @$arraySumPension + $payrollWaPayment->nssf_number + $nhifData + @$sccooSum + @$payrollCustomSum)}}
               
               <?php  $total = $totalAmountSum + @$arraySumPension + $payrollWaPayment->nssf_number + $nhifData + @$sccooSum + @$payrollCustomSum;?>

            </td>
         </tr>
         <tr>
            <td style="padding: 5px;font-size:14px" colspan="2"><b><u>PAYROL RELIEF</u></b></td>
         </tr>
         <tr>
            <td style="padding: 5px;">Monthly Relief</td>
            <td style="padding: 5px;text-align: right;">
              {{manageAmountFormat($payrollWaPayment->relief)}} 
            </td>
         </tr>
         @foreach($releifData as $releifDataVal)
         
         <tr>
            <td style="padding: 5px;">{{$releifDataVal->ReliefData ? $releifDataVal->ReliefData->relief: 'NA'}}</td>
            <td style="padding: 5px;text-align: right;">
               {{manageAmountFormat($releifDataVal->amount)}}
            </td>
         </tr>@endforeach
         <tr>
            <td style="padding: 5px;">Net Pay</td>
            <td style="padding: 5px;text-align: right;">{{ manageAmountFormat($totalAM -  $total)}}
         </td>
         </tr>
             <tr>
            <td style="padding: 10px;border-top: 1px solid gray" colspan="2">SIGNATURE</td>
         </tr>
      </table>
      <table style="width: 100%;margin: auto;">
             <tr>
            <td style="padding: 10px;"><b>{{Date('d-m-Y')}}</b></td>
             <td style="padding: 10px;text-align: center;"><b>Printed By: Dennis</b></td>

              <td style="padding: 10px;text-align: right;"><b>{{Date('h:i:a')}}</b></td>
         </tr>
      </table>
  </div>
  </div>@endif
   </div>
</div>

</section>
    @endsection
    @section('uniquepagescript')
    `
    <script type="text/javascript">
     

function printData()
{
   var divToPrint=document.getElementById("TableID");
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
}

   $("#print-btn").click(function () {
              printData();


      });

    </script>
@endsection
