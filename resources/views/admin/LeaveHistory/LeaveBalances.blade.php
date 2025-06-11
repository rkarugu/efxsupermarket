<?php

use App\Model\Entitlements;
use App\Model\AssignLeave;

?>
@extends('layouts.admin.admin')
@section('content')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>

        <?php
         $dateFormant  = date('Y');
        ?>
    <!-- Main content -->
    <section class="content">
       @include('message')<br>
    <div class="box box-primary" style="margin-top: 20px;">
      <div class="box-header with-border no-padding-h-b">
               <h4>REPORTS LEAVES BALANCES   REPORT</h4><hr>
               <form method="get">
                <div class="row">
                  <div class="col-lg-3">
                    <div class="form-group">
                    <label>Leave Type</label>
                    <select class="form-control" name="leave_type_id">
                      <option value="">--Select---</option>
                      @foreach($leaveTypeData2 as $val)
                      <option value="{{$val->id}}"{{ trim(Request::get('leave_type_id')) == $val ? 'selected="selected"' : ''}}>{{$val->leave_type}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                    <label>Year</label>
                    <select class="form-control" name="year">
                      <option value="">--Select---</option>
                      <option value="2020">2020</option>
                      <option value="2021">2021</option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-12">
                  <button class="btn btn-danger" type="submit">Filter</button>
                 <a href="{{route('LeaveBlancePdf.Pdf')}}?leave_type_id={{Request::get('leave_type_id')}}&year={{Request::get('year')}}"><button title="Export In PDF" type="button" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf" aria-hidden="true"></i></button></a>
               </div>
              </div>
            </form>
               <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridapproved" style="border-collapse:collapse; margin-top: 30px;">
           <tbody>
            <tr>
            <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col">Employee</th>
            <th align="left" scope="col">Leave Type</th>
            <th align="left" scope="col">Opening Bal.</th>
            <th align="left" scope="col">Accrued</th>
            <th align="left" scope="col">Days Taken</th>
            <th align="left" scope="col">Balance</th>
        </tr>
       @foreach($leaveDataAssignBalace as $value)

       <?php
          $assignLeave = AssignLeave::where('emp_id',$value->employee_id)->groupBy('emp_id')->selectRaw('*,sum(day_taken) as total')->first();

       ?>

        <tr>
          <td>{{$value->EmpDataGet ? $value->EmpDataGet->staff_number : 'NA'}}</td>
          <td>{{$value->EmpDataGet ? $value->EmpDataGet->first_name : 'NA'}} {{$value->EmpDataGet ? $value->EmpDataGet->middle_name : 'NA'}} {{$value->EmpDataGet ? $value->EmpDataGet->last_name : 'NA'}}</td>
          <td>{{$value->LeaveDataGet ? $value->LeaveDataGet->leave_type : 'NA'}}</td>
          <td>{{$value->opening_balance}}</td>
          <td>{{$value->default_entitlement}}</td>
          <td>{{@$assignLeave->total}}</td>
          <td>{{$value->opening_balance +  $value->default_entitlement - @$assignLeave->total }}</td>
        </tr>
        @endforeach
            </tbody>
</table>
      
 </div>
</div>
</section>
    @endsection
    @section('uniquepagescript')
 <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>

<script type="text/javascript">
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
  
</script>  
@endsection
