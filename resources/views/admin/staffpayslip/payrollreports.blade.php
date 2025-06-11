<?php

use App\Model\Paye;
use App\Model\PayrollWaPayment;
use App\Model\PayrollAllowances;
use App\Model\PayrollCommission;
use App\Model\PayrollProcessViews;
use App\Model\PayrollRelief;
use App\Model\PayrollPension;
use App\Model\WaNonCashBenefits;
use App\Model\PayrollCustomParameters;
use App\Model\PayrollSacco;
use App\Model\NHIF;

?>
@extends('layouts.admin.admin')
@section('content')
<style>
.span-action {
    display: inline-block;
    margin: 0 3px;
}
</style>
    <!-- Main content -->
   <section class="content">

               <h4>Payroll Report</h4>
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
               
            </div>
            <div class="row">
                <div class="col-lg-5">
                  <button class="btn btn-info" type="submit">Save</button>
                  <a href="{{route('payrollreprt.Pdf')}}">
                  <button class="btn btn-info" type="button">Payroll Pdf</button></a>
                  <a href="{{route('payrollreprt.export')}}">
                  <button class="btn btn-info" type="button">Payroll Excel</button></a>
                   </button></a>
                </div>
              </div>
            </form>
            <hr>

@if(count($payrollAllowancesPayrollView) > 0)
            <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" id="TableID" style="display: block;overflow-x: auto;white-space: nowrap;">
    <thead>
      <tr>
        <th align="left" scope="col">Emp No</th>
        <th align="left" scope="col">Name</th>
        <th align="left" scope="col">ID No.</th>
        <th align="right" scope="col">BASIC PAY</th>
        @foreach($payrollAllowancesPayroll as $value)
        <th align="right" scope="col">{{$value->allowance}}</th>
        @endforeach
        @foreach($commissionData as $commissionDataVal)
        <th align="right" scope="col">{{$commissionDataVal->commission}}</th>
        @endforeach
        <th align="right" scope="col">Overtime 1.5</th>
        <th align="right" scope="col">Absenteeism</th>
        <th align="right" scope="col">Gross Pay</th>
        @foreach($waNonCashBenefitsData as $nonValue)
        <th align="right" scope="col">{{$nonValue->non_cash_benefit}}</th>
        @endforeach
        <th align="right" scope="col">Taxable Income</th>
        <th align="right" scope="col">Paye</th>
        <th align="right" scope="col">Nssf</th>
        <th align="right" scope="col">Nhif</th>
        @foreach($saccoDataGet as $saccoValue)
        <th align="right" scope="col">{{$saccoValue->sacco}}</th>
        @endforeach
        @foreach($customParametersDeduction as $customValue)
        <th align="right" scope="col">{{$customValue->parameter}}</th>
        @endforeach
        @foreach($pensionData as $pensionDataValue)
        <th align="right" scope="col">{{$pensionDataValue->pension}}</th>
        @endforeach
        <th align="right" scope="col">Total Deductions</th>
        <th align="right" scope="col">Monthly Relief</th>
        <th align="right" scope="col">Net Pay</th>
      </tr>
    </thead>
    <tbody>
      @foreach($payrollAllowancesPayrollView as $value2)
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
        @foreach($payrollAllowancesPayroll as $value)
        <?php
         $payrollAllowances = PayrollAllowances::where([['allowance_id',$value->id],['emp_id',$value2->id]])->first();
        ?>
        <td align="right"><span class="taxable">{{manageAmountFormat(@$payrollAllowances->amount)}}</span></td>
        @endforeach

       @foreach($commissionData as $commissionDataVal)
       <?php
        $commissionPayroll = PayrollCommission::where([['commission_id',$commissionDataVal->id],['emp_id',$value2->id]])->first();
        // dd($commissionDataVal);
        ?>
        <td align="right" scope="col">{{manageAmountFormat(@$commissionPayroll->amount)}}</td>
        @endforeach
        <td align="right" scope="col">0.00</td>
        <td align="right" scope="col">0.00</td>
        <td align="right" scope="col">{{manageAmountFormat($grossSalary)}}</td>
        @foreach($waNonCashBenefitsData as $nonValue)
        <?php      
          $waNonCashBenefitsAm = WaNonCashBenefits::where([['emp_id',$value2->id],['non_cash_benefits_id',$nonValue->id]])->first();
           ?>
        <td align="right" scope="col">{{manageAmountFormat(@$waNonCashBenefitsAm->amount)}}</td>
        @endforeach
        <td align="right" scope="col">{{manageAmountFormat($taxableAmount)}}</td>
        <td align="right" scope="col">{{manageAmountFormat($payeData)}}</td>
        <td align="right" scope="col">{{manageAmountFormat($value2->NssfAmount)}}</td>
        <td align="right" scope="col">{{manageAmountFormat($nhifData)}}</td>
        @foreach($saccoDataGet as $saccoValue)
        <?php
          $saccoDataAmount = PayrollSacco::where([['emp_id',$value2->id],['sacco_id',$saccoValue->id]])->first();
         ?>
          <td align="right" scope="col">{{manageAmountFormat(@$saccoDataAmount->amount)}}</td>
        @endforeach
        @foreach($customParametersDeduction as $customValue)
         <?php
          $deductionAmount = PayrollCustomParameters::where([['emp_id',$value2->id],['custom_parameters_id',
            $customValue->id]])->first();
         ?>
         <td align="right" scope="col">{{manageAmountFormat(@$deductionAmount->amount)}}</td>
        @endforeach
         @foreach($pensionData as $pensionDataValue)
         <?php $pensionAmount = PayrollPension::where([['emp_id',$value2->id],['pension_id',$pensionDataValue->id]])->first();?>
        <td align="right" scope="col">{{manageAmountFormat(@$pensionAmount->amount)}}</td>
        @endforeach
        <td align="right" scope="col">{{manageAmountFormat(@$totalDeduction)}}</td>
        <td align="right" scope="col">{{manageAmountFormat(@$value2->ReflifAmount + $value2->Relief )}}</td>
        <td align="right" scope="col">{{manageAmountFormat(@$grossSalary -$totalDeduction )}}</td>
      </tr>@endforeach
    </tbody>
  </table>@endif
 </div>
</div>
</section>
    @endsection
    @section('uniquepagescript')
    <script type="text/javascript">
            <?php 
          if (!empty(Request::get('month'))) {
             $m = Request::get('month');
          }else{
            $m =  $abc =  date('F');
          }
?>
      $('#year').on('change', function() {
        var year =  this.value;
        var month = "<?php echo $m?>";
          window.location.href = "{{route('payrollreprt.index')}}?year="+year+"&month="+month;

     });
    </script>
    <script type="text/javascript">
      <?php
      if (!empty(Request::get('year'))) {
         $abc = Request::get('year');
       }else{
        $abc =  $abc =  date('Y');
       }
       ?>
      $('#month').on('change', function() {
           var selectedVal2 =  this.value;
           var abc2 = "<?php echo $abc?>";
           // alert(abc2);
          window.location.href = "{{route('payrollreprt.index')}}?month="+selectedVal2+"&year="+abc2;

     });
    </script>

@endsection
