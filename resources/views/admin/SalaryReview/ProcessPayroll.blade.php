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

               <h4>Payroll Processing....</h4>
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
                <input type="text" name="Search" value="{{Request::get('Search')}}" placeholder="Search" class="form-control">
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5" style="float: right;">
                  <button class="btn btn-info" type="submit">Payroll Process</button>
                  @if(count($processReportData) == 1)
                   @foreach($processReportData as $valuebtn)
                   <a href="{{route('ProcessPayroll.pdf',['id'=>$valuebtn->id])}}?month={{Request::get('month')}}&year={{Request::get('year')}}&Search=">
                  <button class="btn btn-danger" type="button">Payroll PaySilp</button></a>
                  @endforeach
                  @else()
                     <a>
                  <button class="btn btn-danger" type="button">Payroll PaySilp</button></a>
                  @endif

                  <button class="btn btn-secondary" type="button">Payroll Tax Forms</button>
                </div>
              </div>
            </form>
            <hr>
            <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" id="MainContent_gridpayroll" style="border-collapse:collapse;">
    <thead>
      <tr style="color:White;background-color:#3AC0F2;">
        <th align="left" scope="col">Emp No</th>
        <th align="left" scope="col">Name</th>
        <th align="left" scope="col">ID No.</th>
        <th align="right" scope="col">BASIC PAY</th>
        <th align="right" scope="col">GROSS</th>
        <th align="right" scope="col">TAXABLE INCOME</th>
        <th align="right" scope="col">NSSF</th>
        <th align="right" scope="col">NHIF</th>
        <th align="right" scope="col">PAYE</th>
        <th align="right" scope="col">Net Pay</th>
        <th align="right" scope="col">#</th>
      </tr>
    </thead>
    <tbody>
   
   @foreach($processReportData as $val)
    
    <?php
         $payData = Paye::orderBy('id','DESC')->get();
         $basicPayAmount = $val->MonthlyPay +  $val->allowance +  $val->deduct + $val->CustomerParams;
         $taxableAmount = $basicPayAmount  + $val->NonCash - $val->NssfAmount - $val->Pension;




  
    $salary = $taxableAmount;
    $tax = 0; 
    $abc = round($basicPayAmount,0);
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
    
    $payeData = $tax2 - $val->Relief - $val->ReflifAmount ;

    $totalDeducation = $val->Pension  + $val->SacccoAmount + $nhifData +  $val->NssfAmount + $payeData;

    $TotalAmountNet2 = $basicPayAmount  - $totalDeducation - $val->CustomDeduction;
   
   // echo "string".$totalDeducation;
    ?>
    
    <tr>
        <td>{{$val->staff_number}}</td>
        <td class="Name">{{$val->first_name}} {{$val->middle_name}} {{$val->last_name}}</td>
        <td>{{$val->Id_number}}</td>
        <td align="right"><span>{{manageAmountFormat($val->MonthlyPay)}}</span></td>
        <td align="right"><span>{{manageAmountFormat($basicPayAmount)}}</span></td>
        <td align="right"><span class="taxable">{{manageAmountFormat($taxableAmount)}}</span></td>
        <td align="right"><span>{{manageAmountFormat($val->NssfAmount)}}</span></td>
        <td align="right"><span>{{manageAmountFormat($nhifData)}}</span></td>
        <td align="right"><span>{{manageAmountFormat($payeData)}}  </span></td>
        <td align="right"><span>{{manageAmountFormat($TotalAmountNet2)}}</span></td>
        <td align="right"><span><a href="{{route('ProcessPayroll.Payslip',['id'=>$val->id])}}?month={{Request::get('month')}}&year={{Request::get('year')}}&Search="><button style="padding: 3px;" class="btn btn-primary">View</button></a></span></td>
      </tr>@endforeach
    </tbody>
  </table>
      
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
          window.location.href = "{{route('ProcessPayroll.index')}}?year="+year+"&month="+month;

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
          window.location.href = "{{route('ProcessPayroll.index')}}?month="+selectedVal2+"&year="+abc2;

     });
    </script>

@endsection
