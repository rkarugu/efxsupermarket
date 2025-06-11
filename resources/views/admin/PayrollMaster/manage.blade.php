@extends('layouts.admin.admin')
@section('content')
<style>
.dataTables_wrapper table th, td {
    border-right: 0px solid #337ab7 !important;
}	
/* Style the tab */
.tab {
  overflow: hidden;
/*  border: 1px solid #ccc;
*/  background-color: #c1ccd1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 7px 16px;
  transition: 0.3s;
  font-size: 14px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: white;
  border: 1px solid gainsboro;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
.ctable {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

.ctable,td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
    .onoffswitch {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px; 
}

 .onoffswitch1 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch1-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch1-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch1-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch1-inner:before, .onoffswitch1-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch1-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch1-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch1-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-inner {
    margin-left: 0;
}
.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-switch {
    right: 0px; 
}

/* Style the close button */
</style>

    <!-- Main content -->
    <section class="content">
                                  @include('message')<br>

        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                       <div class="box-header with-border no-padding-h-b">  <h4>Employee Profile</h4>
                        	<hr>	
                        	<div class="row">
                        <div class="col-md-3 col-sm-3">
                            <a href="#">
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$empData->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </a>
                        
                        </div>
                       <div class="col-md-9 col-sm-9">
                          <table class="table">
                            <tbody>
                            	<tr>
                                <td><strong>Staff No</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->staff_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Employee Name</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->first_name}} {{$empData->last_name}}</b></td>
                            </tr>
                            <tr>
                               <td><strong>PIN Number</strong></td>
                               <td>:</td>
                               <td><b>{{$empData->pin_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>ID Number</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->Id_number}}</b></td>
                            </tr>
                            <tr>
                            	<td><strong>NSSF Number</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->nssf_no}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>NHIF Number</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->nhif_no}}</b></td>
                            </tr>
                            </tbody>
                        </table>
                       </div>
                        </div>
                    </div>
                </div>
                    <div class="box box-primary" style="margin-top: 20px;">
                       <div class="box-header with-border no-padding-h-b">
                       <h4>Employee Detail</h4>
                        	<hr>
                        <div class="col-lg-12 col-md-12">
                        	<div class="tab">

  <button class="tablinks {{ Request::get('Types') == 'Payments' ? 'active' : ''}}" onclick="openCity(event, 'London')">Payments</button>
  <a href="?Types=Allowances"><button class="tablinks {{ Request::get('Types') == 'Allowances' ? 'active' : ''}}" onclick="openCity(event, 'Allowances')">Allowances</button></a>
  <a href="?Types=Loans"><button class="tablinks {{ Request::get('Types') == 'Loans' ? 'active' : ''}}" onclick="openCity(event, 'Loans')">Loans</button></a>
  <a href="?Types=Commission"><button class="tablinks {{ Request::get('Types') == 'Commission' ? 'active' : ''}}" onclick="openCity(event, 'Loans')">Commission</button></a>
  <a href="?Types=Pension"><button class="tablinks {{ Request::get('Types') == 'Pension' ? 'active' : ''}}" onclick="openCity(event, 'Loans')">Pension</button></a>
  <a href="?Types=Relief"><button class="tablinks {{ Request::get('Types') == 'Relief' ? 'active' : ''}}" onclick="openCity(event, 'Loans')">Relief</button></a>
  <a href="?Types=Sacco"><button class="tablinks {{ Request::get('Types') == 'Sacco' ? 'active' : ''}}" onclick="openCity(event, 'Sacco')">Sacco</button></a>
  <a href="?Types=Custom Parameters"><button class="tablinks {{ Request::get('Types') == 'Custom Parameters' ? 'active' : ''}}" onclick="openCity(event, 'Custom Parameters')">Custom Parameters</button></a>
  <a href="?Types=Non-Cash Benefits"><button class="tablinks {{ Request::get('Types') == 'Non-Cash Benefits' ? 'active' : ''}}" onclick="openCity(event, 'Non-Cash Benefits')">Non-Cash Benefits</button></a>
</div>

<div id="London" class="tabcontent" style="{{ Request::get('Types') == '' ? 'display: block;' : ''}}">
	<div class="row" style="margin-top: 15px;">
		<form class="validate form-horizontal"  role="form" method="POST" @if(!empty($payrollWaPaymentData))  action="{{route('PayrollMaster.EmpPaymentUpdate',['id'=>$payrollWaPaymentData->id])}}" @else() action="{{route('PayrollMaster.EmpPaymentStore')}}"@endif enctype = "multipart/form-data">
      {{ csrf_field() }}

		<div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Basic Pay</label>
          <div class="col-lg-9">
              {!! Form::text('basic_pay', @$payrollWaPaymentData->basic_pay, ['maxlength'=>'255','placeholder' => 'Basic Pay', 'required'=>true, 'class'=>'form-control']) !!}  
          </div>
        </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Pay Frequency</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                      {!!Form::select('pay_frequency_id',
                      $payment_frequency, @$payrollWaPaymentData->pay_frequency_id, ['placeholder'=>'Select Pay Frequency', 'class' => 'form-control','required'=>true,'title'=>'Please Pay Frequency'  ])!!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Branch</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('branch_id',
                      $branchData, @$payrollWaPaymentData->branch_id, ['placeholder'=>'Select Branch', 'class' => 'form-control','required'=>true,'title'=>'Please Branch'  ])!!} 
                    </div>
                </div>  
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Account Name</label>
            <div class="col-lg-9">
            {!! Form::text('account_name', @$payrollWaPaymentData->account_name, ['maxlength'=>'255','placeholder' => 'Account Name', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Account No</label>
            <div class="col-lg-9">
            {!! Form::text('account_number', @$payrollWaPaymentData->account_number, ['maxlength'=>'255','placeholder' => 'Account No', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>  
             <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Currency</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('currency_id',
                      $wa_CurrencyManager, @$payrollWaPaymentData->currency_id, ['placeholder'=>'Select Currency', 'class' => 'form-control','required'=>true,'title'=>'Please Currency'  ])!!} 
                    </div>
                </div>  
                   <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">NHIF</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch">
                    <input type="checkbox" name="nhif" class="onoffswitch-checkbox" value="On" id="myonoffswitch" tabindex="0" {{@$payrollWaPaymentData->nhif == 'On' ? 'checked' : ''}}>
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                      </div>
                </div>
                </div>
		</div>
		<div class="col-lg-6">
			 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">NSSF</label>
                    <div class="col-lg-9">
                        {!! Form::text('nssf_number',  @$payrollWaPaymentData->nssf_number, ['maxlength'=>'255','placeholder' => 'NSSF', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Bank</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                     {!!Form::select('bank_id',
                      $bankData, @$payrollWaPaymentData->bank_id, ['placeholder'=>'Select Pay Bank', 'class' => 'form-control','required'=>true,'title'=>'Please Bank'  ])!!} 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Relief</label>
                    <div class="col-lg-9">
                        {!! Form::text('relief', @$payrollWaPaymentData->relief, ['maxlength'=>'255','placeholder' => 'Relief', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Voluntary NSSF</label>
                    <div class="col-lg-9">
                        {!! Form::text('voluntary_nssf', @$payrollWaPaymentData->voluntary_nssf, ['maxlength'=>'255','placeholder' => 'Voluntary NSSF', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Payment Mode</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                     {!!Form::select('payment_mode_id',
                      $payment_mode, @$payrollWaPaymentData->payment_mode_id, ['placeholder'=>'Select Payment Mode', 'class' => 'form-control','required'=>true,'title'=>'Please Payment Mode'  ])!!} 
                    </div>
                </div>
                <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">PAYE</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch1">
                    <input type="checkbox" name="paye" class="onoffswitch1-checkbox" value="On" id="myonoffswitch1" tabindex="0" {{@$payrollWaPaymentData->paye == 'On' ? 'checked' : ''}}>
                    <label class="onoffswitch1-label" for="myonoffswitch1">
                        <span class="onoffswitch1-inner"></span>
                        <span class="onoffswitch1-switch"></span>
                    </label>
                      </div>
                </div>
                </div>
                   <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
                    <input type="radio" name="active" value="Yes" {{@$payrollWaPaymentData->active == 'Yes' ? 'checked="checked"' : ''}}> Yes 
                    <input type="radio" name="active" value="No" {{@$payrollWaPaymentData->active == 'No' ? 'checked="checked"' : ''}}> No<br>
                  </div>
            </div>
		<div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
      </div>
	</form>
	</div>
</div>
<div id="Allowances" class="tabcontent" style="{{ Request::get('Types') == 'Allowances' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" action="{{route('PayrollMaster.AllowancesCreate')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}

    <div class="col-lg-6">
        <div class="form-group">
          <input type="hidden" name="emp_id" value="{{$empData->id}}">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Allowance</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('allowance_id',
            $wa_Allowances, null, ['placeholder'=>'Select Allowance', 'class' => 'form-control','required'=>true,'title'=>'Please Allowance'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
          <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
               <div class="col-lg-9">
                    <input type="radio" name="active" value="Yes"> Yes 
                    <input type="radio" name="active" value="No" > No<br>
                  </div>
                </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Allowance</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($payrollAllowancesActive as $acVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$acVal->AllowanceData ? $acVal->AllowanceData->allowance : 'NA'}}</td>
            <td>{{$acVal->year}}</td>
            <td>{{$acVal->month}}</td>
            <td>{{$acVal->ref_number}}</td>
            <td>{{$acVal->narration}}</td>
            <td>{{$acVal->amount}}</td>
            <td><input  type="checkbox" {{ $acVal->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Allowance</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
<!--             <th align="left" scope="col" style="width:70px;">&nbsp;</th>
 -->          </tr>
 @foreach($payrollAllowancesDeActive as $acValD)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$acValD->AllowanceData ? $acValD->AllowanceData->allowance : 'NA'}}</td>
            <td>{{$acValD->year}}</td>
            <td>{{$acValD->month}}</td>
            <td>{{$acValD->ref_number}}</td>
            <td>{{$acValD->narration}}</td>
            <td>{{$acValD->amount}}</td>
            <td><input  type="checkbox" {{ $acValD->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Loans" class="tabcontent" style="{{ Request::get('Types') == 'Loans' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
      <form class="validate form-horizontal"  role="form" method="POST" action="{{route('PayrollMaster.LoanCreate')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
          <input type="hidden" name="emp_id" value="{{$empData->id}}">

    <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Loan Type</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('loan_type_id',
            $wa_LoanType, null, ['placeholder'=>'Select Loan Type', 'class' => 'form-control','required'=>true,'title'=>'Please Loan Type'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Monthly Deduction</label>
            <div class="col-lg-9">
                {!! Form::text('monthly_deduction', null, ['maxlength'=>'255','placeholder' => 'Monthly Deduction', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
         <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
               <div class="col-lg-9">
                    <input type="radio" name="active" value="Yes"> Yes 
                    <input type="radio" name="active" value="No" > No<br>
                  </div>
                </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Principal Deducted</label>
            <div class="col-lg-9">
                {!! Form::text('principal_deducted', null, ['maxlength'=>'255','placeholder' => 'Principal Deducted', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Loan Type</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">Principal Deducted</th>
            <th align="left" scope="col">Monthly Deduction</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($payrollLoanTypeActive as $av2)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$av2->LoanTypeData ? $av2->LoanTypeData->loan_type : 'NA'}}</td>
            <td>{{$av2->year}}</td>
            <td>{{$av2->month}}</td>
            <td>{{$av2->principal_deducted}}</td>
            <td>{{$av2->monthly_deduction}}</td>
            <td><input  type="checkbox" {{ $av2->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
       <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Loan Type</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">Principal Deducted</th>
            <th align="left" scope="col">Monthly Deduction</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($payrollLoanTypeDeActive as $av3)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$av3->LoanTypeData ? $av3->LoanTypeData->loan_type : 'NA'}}</td>
            <td>{{$av3->year}}</td>
            <td>{{$av3->month}}</td>
            <td>{{$av3->principal_deducted}}</td>
            <td>{{$av3->monthly_deduction}}</td>
            <td><input  type="checkbox" {{ $av3->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Commission" class="tabcontent" style="{{ Request::get('Types') == 'Commission' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" action="{{route('PayrollMaster.CommissionCreate')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
    <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Commission</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('commission_id',
            $commissionData, null, ['placeholder'=>'Select Commission', 'class' => 'form-control','required'=>true,'title'=>'Please Commission'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
        <div class="form-group">
              <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
         <div class="col-lg-9">
              <input type="radio" name="active" value="Yes"> Yes 
              <input type="radio" name="active" value="No" > No<br>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
      <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Commission</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
            <th align="left" scope="col" style="width:70px;">&nbsp;</th>
          </tr>
          @foreach($payrollCommissionActive as $avl4)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$avl4->CommissionType ? $avl4->CommissionType->commission : ''}}</td>
            <td>{{$avl4->year}}</td>
            <td>{{$avl4->month}}</td>
            <td>{{$avl4->ref_number}}</td>
            <td>{{$avl4->narration}}</td>
            <td>{{$avl4->amount}}</td>
            <td><input  type="checkbox" {{ $avl4->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
            <td><input type="button" value="Edit" class="btn btn-success btn-xs"></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Commission</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
          </tr>
         @foreach($payrollCommissionDActive as $avl5)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$avl5->CommissionType ? $avl5->CommissionType->commission : ''}}</td>
            <td>{{$avl5->year}}</td>
            <td>{{$avl5->month}}</td>
            <td>{{$avl5->ref_number}}</td>
            <td>{{$avl5->narration}}</td>
            <td>{{$avl5->amount}}</td>
            <td><input  type="checkbox" {{ $avl5->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Pension" class="tabcontent" style="{{ Request::get('Types') == 'Pension' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
     <form class="validate form-horizontal"  role="form" method="POST" action="{{route('Pension.create')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
      <input type="hidden" name="emp_id" value="{{$empData->id}}">

    <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Pension</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('pension_id',
            $pensionData, null, ['placeholder'=>'Select Pension', 'class' => 'form-control','required'=>true,'title'=>'Please Pension'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
        <div class="form-group">
              <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
         <div class="col-lg-9">
              <input type="radio" name="active" value="Yes"> Yes 
              <input type="radio" name="active" value="No" > No<br>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Pension</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($payrollPensionActive as $pensionVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$pensionVal->PayrollPensionData ? $pensionVal->PayrollPensionData->pension : 'NA'}}</td>
            <td>{{$pensionVal->year}}</td>
            <td>{{$pensionVal->month}}</td>
            <td>{{$pensionVal->ref_number}}</td>
            <td>{{$pensionVal->narration}}</td>
            <td>{{$pensionVal->amount}}</td>
            <td><input  type="checkbox" {{ $pensionVal->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Pension</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
         @foreach($payrollPensionDeActive as $pensionVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$pensionVal->PayrollPensionData ? $pensionVal->PayrollPensionData->pension : 'NA'}}</td>
            <td>{{$pensionVal->year}}</td>
            <td>{{$pensionVal->month}}</td>
            <td>{{$pensionVal->ref_number}}</td>
            <td>{{$pensionVal->narration}}</td>
            <td>{{$pensionVal->amount}}</td>
            <td><input  type="checkbox" {{ $pensionVal->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Relief" class="tabcontent" style="{{ Request::get('Types') == 'Relief' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
     <form class="validate form-horizontal"  role="form" method="POST" action="{{route('Relief.create')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
       <input type="hidden" name="emp_id" value="{{$empData->id}}" >
    <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Relief</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('relief_id',
            $reliefData, null, ['placeholder'=>'Select Relief', 'class' => 'form-control','required'=>true,'title'=>'Please Relief'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
         <div class="form-group">
              <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
         <div class="col-lg-9">
              <input type="radio" name="active" value="Yes"> Yes 
              <input type="radio" name="active" value="No" > No<br>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="SEPTEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Relief</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($payrollReliefActive as $payrollReliefVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$payrollReliefVal->ReliefData ? $payrollReliefVal->ReliefData->relief : 'Na' }}</td>
            <td>{{$payrollReliefVal->year}}</td>
            <td>{{$payrollReliefVal->month}}</td>
            <td>{{$payrollReliefVal->ref_number}}</td>
            <td>{{$payrollReliefVal->narration}}</td>
            <td>{{$payrollReliefVal->amount}}</td>
            <td><input  type="checkbox" {{ $payrollReliefVal->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Relief</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
<!--             <th align="left" scope="col" style="width:70px;">&nbsp;</th>
 -->          </tr>
         @foreach($payrollReliefDeActive as $Deval)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$payrollReliefVal->ReliefData ? $payrollReliefVal->ReliefData->relief : 'Na' }}</td>
            <td>{{$Deval->year}}</td>
            <td>{{$Deval->month}}</td>
            <td>{{$Deval->ref_number}}</td>
            <td>{{$Deval->narration}}</td>
            <td>{{$Deval->amount}}</td>
            <td><input  type="checkbox" {{ $Deval->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Sacco" class="tabcontent" style="{{ Request::get('Types') == 'Sacco' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
     <form class="validate form-horizontal"  role="form" method="POST" action="{{route('Sacco.create')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
      <input type="hidden" name="emp_id" value="{{$empData->id}}">

    <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Sacco</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('sacco_id',
            $saccoData, null, ['placeholder'=>'Select Sacco', 'class' => 'form-control','required'=>true,'title'=>'Please Sacco'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
          <div class="form-group">
              <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
         <div class="col-lg-9">
              <input type="radio" name="active" value="Yes"> Yes 
              <input type="radio" name="active" value="No" > No<br>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Allowance</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($payrollSaccoActive as $payrollSaccoActiveVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$payrollSaccoActiveVal->SaccoData ? $payrollSaccoActiveVal->SaccoData->sacco : 'NA'}}</td>
            <td>{{$payrollSaccoActiveVal->year}}</td>
            <td>{{$payrollSaccoActiveVal->month}}</td>
            <td>{{$payrollSaccoActiveVal->ref_number}}</td>
            <td>{{$payrollSaccoActiveVal->narration}}</td>
            <td>{{$payrollSaccoActiveVal->amount}}</td>
            <td><input  type="checkbox" {{ $payrollSaccoActiveVal->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Allowance</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
<!--             <th align="left" scope="col" style="width:70px;">&nbsp;</th>
 -->          </tr>
 @foreach($payrollSaccoDeActive as $avlDeative)
           <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$avlDeative->SaccoData ? $avlDeative->SaccoData->sacco : 'NA'}}</td>
            <td>{{$avlDeative->year}}</td>
            <td>{{$avlDeative->month}}</td>
            <td>{{$avlDeative->ref_number}}</td>
            <td>{{$avlDeative->narration}}</td>
            <td>{{$avlDeative->amount}}</td>
            <td><input  type="checkbox" {{ $avlDeative->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Custom Parameters" class="tabcontent" style="{{ Request::get('Types') == 'Custom Parameters' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
     <form class="validate form-horizontal"  role="form" method="POST" action="{{route('CustomParameters.create')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}

    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Custom Parameters</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('custom_parameters_id',
            $customParameterData, null, ['placeholder'=>'Select Custom Parameters', 'class' => 'form-control','required'=>true,'title'=>'Please Custom Parameters'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
           <div class="form-group">
              <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
         <div class="col-lg-9">
              <input type="radio" name="active" value="Yes"> Yes 
              <input type="radio" name="active" value="No" > No<br>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
         <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Custom Parameter</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
          </tr>
          @foreach($customParameterActive as $customParameterVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$customParameterVal->CustomParameterData ? $customParameterVal->CustomParameterData->parameter : 'Na'}}</td>
            <td>{{$customParameterVal->year}}</td>
            <td>{{$customParameterVal->month}}</td>
            <td>{{$customParameterVal->ref_number}}</td>
            <td>{{$customParameterVal->narration}}</td>
            <td>{{$customParameterVal->amount}}</td>
            <td><input  type="checkbox" {{ $customParameterVal->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
      <hr>
      <center>
       <span class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">In active</span></center>
        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Custom Parameter</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">Active</th>
<!--             <th align="left" scope="col" style="width:70px;">&nbsp;</th>
 -->          </tr>
 @foreach($customParameterDeActive as $customDeActive)
            <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$customDeActive->CustomParameterData ? $customDeActive->CustomParameterData->parameter : 'Na'}}</td>
            <td>{{$customDeActive->year}}</td>
            <td>{{$customDeActive->month}}</td>
            <td>{{$customDeActive->ref_number}}</td>
            <td>{{$customDeActive->narration}}</td>
            <td>{{$customDeActive->amount}}</td>
            <td><input  type="checkbox" {{ $customDeActive->active == 'Yes' ? 'checked="checked"' : '' }} disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
<div id="Non-Cash Benefits" class="tabcontent" style="{{ Request::get('Types') == 'Non-Cash Benefits' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
     <form class="validate form-horizontal"  role="form" method="POST" action="{{route('Non-Cash-Benfit.store')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
      <input type="hidden" name="emp_id" value="{{$empData->id}}">

    <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Non-Cash Benefits</label>
          <div class="col-lg-9" style="margin-top: 5px;">
           {!!Form::select('non_cash_benefits_id',
            $parameterNonCustomers, null, ['placeholder'=>'Select Non-Cash Benefits', 'class' => 'form-control','required'=>true,'title'=>'Please Non-Cash Benefits'  ])!!} 
          </div>
        </div>
        <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
          <div class="col-lg-9">
          <select name="year" id="" class="form-control input-sm">
        <option value="2013">2013</option>
        <option value="2014">2014</option>
        <option value="2015">2015</option>
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option selected="selected" value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
        <option value="2034">2034</option>
        <option value="2035">2035</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref No.</label>
            <div class="col-lg-9">
                {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
        </div>
        <div class="col-lg-6">
        <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount</label>
            <div class="col-lg-9">
                {!! Form::text('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
      <div class="form-group">
        <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
          <div class="col-lg-9">
        <select name="month" class="form-control input-sm">
        <option value="JANUARY">JANUARY</option>
        <option value="FEBRUARY">FEBRUARY</option>
        <option value="MARCH">MARCH</option>
        <option selected="selected" value="APRIL">APRIL</option>
        <option value="MAY">MAY</option>
        <option value="JUNE">JUNE</option>
        <option value="JULY">JULY</option>
        <option value="AUGUST">AUGUST</option>
        <option value="SEPTEMBER">SEPTEMBER</option>
        <option value="OCTOBER">OCTOBER</option>
        <option value="NOVEMBER">NOVEMBER</option>
        <option value="DECEMBER">DECEMBER</option>
      </select>
      </div>
    </div>
     <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
            <div class="col-lg-9">
                {!! Form::text('narration', null, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
        </div>
  </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
       </div>
  </form>
  </div><hr>
  <center>
    <span  class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span></center>
  <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1" style="border-collapse:collapse;">
          <tbody>
            <tr>
            <th align="left" scope="col">EmpNo</th>
            <th align="left" scope="col">Allowance</th>
            <th align="left" scope="col">Year</th>
            <th align="left" scope="col">Month</th>
            <th align="left" scope="col">RefNo</th>
            <th align="left" scope="col">Narration</th>
            <th align="left" scope="col">Amount</th>
            <th align="left" scope="col">C</th>
            <th align="left" scope="col">L</th>
          </tr>
          @foreach($waNonCashBenefitsData as $waNonCashBenefitsDataVal)
          <tr>
            <td>{{$empData->staff_number}}</td>
            <td>{{$waNonCashBenefitsDataVal->NonCashBenfitData ? $waNonCashBenefitsDataVal->NonCashBenfitData->non_cash_benefit : 'NA'}}</td>
            <td>{{$waNonCashBenefitsDataVal->year}}</td>
            <td>{{$waNonCashBenefitsDataVal->month}}</td>
            <td>{{$waNonCashBenefitsDataVal->ref_number}}</td>
            <td>&{{$waNonCashBenefitsDataVal->narration}}</td>
            <td>{{$waNonCashBenefitsDataVal->amount}}</td>
            <td><span class="aspNetDisabled"><input  type="checkbox" disabled="disabled"></span></td>
            <td><span class="aspNetDisabled"><input  type="checkbox" name="" disabled="disabled"></span></td>
          </tr>@endforeach
        </tbody>
      </table>
</div>
</div>
</div>

    </section>
    @endsection
    @section('uniquepagescript')
 <script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript">
   $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
</script>	
@endsection
