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
<div class="box box-primary">
                       <div class="box-header with-border no-padding-h-b">  <h4>Employee Profile</h4>
                          @include('message')<br>
                            <hr>    
                            <div class="row">
                        <div class="col-md-3 col-sm-3">
                            <a href="#">
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$data->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </a><a href="{{  asset('public/uploads/AssignLeaveImage/'.$data->attach_document) }}" target="_blank">Download CV</a>
                        
                        </div>
                       <div class="col-md-9 col-sm-9">
                          <table class="table">
                            <tbody>
                                <tr>
                                <td><strong>Staff No</strong></td>
                                <td>:</td>
                                <td><b>{{$data->staff_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Employee Name</strong></td>
                                <td>:</td>
                                <td><b>{{$data->FirstName}} {{$data->MiddleName}} {{$data->LastName}}</b></td>
                            </tr>
                            <tr>
                               <td><strong>Leave Type</strong></td>
                               <td>:</td>
                               <td><b>{{$data->LeaveType}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>From</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignEmp->from}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>To</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignEmp->to}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Days Taken</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignEmp->day_taken}}</b></td>
                            </tr>
                            </tbody>
                        </table>
                       </div>
                        </div>
                    </div>
                </div>
                 <div class="box box-primary" style="margin-top: 20px;">
                       <div class="box-header with-border no-padding-h-b">
                         <h4>Leave Approval</h4><hr>
                         <form  method="post">
                          {{ csrf_field() }}
                        <div class="col-lg-12 col-md-12">
                        <div class="col-lg-6">
                          <input type="hidden" name="emp_id" value="{{$data->emp_id}}">
                          <input type="hidden" name="leave_type_id" 
                          value="{{$data->leave_id}}">
                           <input type="hidden" name="haflday" class="halfday" value="{{$leaveAssignEmp->half_day}}">

                          <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Approved Start Date</label>
                              <div class="col-lg-9">
                             {!! Form::text('approved_start_date', $leaveAssignEmp->from, ['maxlength'=>'255','placeholder' => 'Approved Start Date', 'required'=>true, 'class'=>'form-control datepicker StratDate']) !!}  
                          </div>
                        </div>
                       
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Approved End Date</label>
                            <div class="col-lg-9">
                             {!! Form::text('approved_end_date', $leaveAssignEmp->to, ['maxlength'=>'255','placeholder' => 'Approved End Date', 'required'=>true, 'class'=>'form-control datepicker EndDate']) !!}  
                        </div>
                      </div>
                        </div>
                        <?php
                        $abcd = Date('Y-m-d');
                        $uData = \Session::get('userdata');
                        ?>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Date</label>
                             <div class="col-lg-9">
                           {!! Form::text('date', $abcd, ['maxlength'=>'255','placeholder' => 'Date', 'required'=>true, 'class'=>'form-control datepicker date2']) !!}   
                        </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Comments</label>
                             <div class="col-lg-9">
                           {!! Form::text('comments', null, ['maxlength'=>'255','placeholder' => 'Comments', 'required'=>true, 'class'=>'form-control comments']) !!}   
                        </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Purpose</label>
                             <div class="col-lg-9">
                           {!! Form::text('purpose', null, ['maxlength'=>'255','placeholder' => 'Purpose', 'required'=>true, 'class'=>'form-control purpose']) !!}   
                        </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Approved By</label>
                              <div class="col-lg-9">
                             {!! Form::text('approved_by', @$uData->name, ['maxlength'=>'255','placeholder' => 'Approved By', 'required'=>true,'readonly'=>true, 'class'=>'form-control approved_by']) !!}  
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Days Applied</label>
                              <div class="col-lg-9">
                             {!! Form::text('days_applied', $leaveAssignEmp->day_taken, ['maxlength'=>'255','placeholder' => 'Days Applied', 'required'=>true, 'readonly'=>true,'class'=>'form-control days_applied','readonly'=>true]) !!}  
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Acting Staff</label>
                              <div class="col-lg-9">
                             {!! Form::text('acting_staff', $leaveAssignEmp->acting_staff, ['maxlength'=>'255','placeholder' => 'Acting Staff', 'required'=>true,'readonly'=>true, 'class'=>'form-control acting_staff','readonly'=>true]) !!}  
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-12" style="margin-top: 20px;">
                             <div class="pull-right">
                            <div class="form-group">
                                         <input type="submit" id="contactForm2" name="Status" value="Approve" class="btn btn-success btn-sm">
                                        <input type="submit" id="contactForm3" name="Status" value="Decline" class="btn btn-warning btn-sm">
                                        <a href="{{route('ManagerApproval.index')}}"><input type="button" name="" value="Back"  class="btn btn-primary btn-sm"></a>
                                    </div>
                                </div>
                        </div>
                      </div>
                    </form>
  </div>
                         <div class="box-header with-border no-padding-h-b">                            <div class="col-md-12 ">
                                <center>
                                    <span id="MainContent_lblapproved" class="label label-success" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Approved Leave Applications</span></center>
                                <div>
    <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridapproved" style="border-collapse:collapse;">
        <tbody><tr>
            <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col">Employee</th>
            <th align="left" scope="col">Leave Type</th>
            <th align="left" scope="col">From</th>
            <th align="left" scope="col">To</th>
            <th align="left" scope="col">Date Approved</th>
            <th align="left" scope="col">Total Days</th>
            <th align="left" scope="col">Approved Days</th>
            <th align="left" scope="col">Approved By</th>
        </tr>
        @foreach($approveData as $val)
        <tr>
            <td align="left">{{$val->EmpData ? $val->EmpData->staff_number : 'NA'}}</td>
            <td align="left">{{$val->EmpData ? $val->EmpData->first_name : 'NA'}}  {{$val->EmpData ? $val->EmpData->middle_name : 'NA'}} {{$val->EmpData ? $val->EmpData->last_name : 'NA'}}</td>
            <td>{{$val->LeaveDataGet2 ? $val->LeaveDataGet2->leave_type : 'NA'}}</td>
            <td>{{$val->from}}</td>
            <td>{{$val->to}}</td>
            <td>{{$val->date_approved}}</td>
            <td>{{$val->day_taken}}</td>
            <td>{{$val->day_taken}}</td>
            <td>{{$val->UData ? $val->UData->name : 'NA'}}</td>
        </tr>@endforeach
    </tbody>
   </table>
   </div>
  </div>
  <div class="col-md-12" style="margin-top: 30px;">
  <center>
      <span id="MainContent_Label1" class="label label-warning" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Rejected Leave Applications</span></center>
    <div>
    <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridunapproved" style="border-collapse:collapse;">
        <tbody>
          <tr>
            <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col">Employee</th>
            <th align="left" scope="col">Leave Type</th>
            <th align="left" scope="col">Date Rejected</th>
            <th align="left" scope="col">Total Days</th>
            <th align="left" scope="col">Rejected By</th>
        </tr>
        @foreach($declineData as $val3)
        <tr>
            <td align="left">{{$val3->EmpData ? $val3->EmpData->staff_number : 'NA'}}</td>
            <td align="left">{{$val3->EmpData ? $val3->EmpData->first_name : 'NA'}}  {{$val3->EmpData ? $val3->EmpData->middle_name : 'NA'}} {{$val3->EmpData ? $val3->EmpData->last_name : 'NA'}}</td>
            <td>{{$val3->LeaveData ? $val3->LeaveData->leave_type : 'NA'}}</td>
            <td>{{$val3->date_rejected}}</td>
            <td>{{$val3->day_taken}}</td>
            <td>{{$val3->DCUserData ? $val3->DCUserData->name : 'NA'}}</td>
        </tr>@endforeach
    </tbody></table>
</div>
 </div>
 </div>
</div>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirm Approval</h4>
        </div>
      <form action="{{route('ManagerApproval.Leave')}}" method="post" id="contactForm">     
       {{ csrf_field() }}
       <div class="modal-body">
          <p>
              You are about to Approve Leave with following details
          </p>
          <p>
              Leave Start Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="MainContent_StartDate" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <p>
              Leave End Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="MainContent_EndDate" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <p>
              Total Days Approved:&nbsp;&nbsp;<span id="MainContent_TotalDays" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <input type="hidden" name="approved_start_date" id="MainContent_StartDate2">
          <input type="hidden" name="approved_end_date" id="MainContent_EndDate2">
          <input type="hidden" name="acting_staff" id="acting_staffData">
          <input type="hidden" name="days_applied" id="days_appliedData">
          <input type="hidden" name="date" id="DateData">
          <input type="hidden" name="comments" id="CommentsData">
          <input type="hidden" name="emp_id" value="{{$data->id}}">
          <input type="hidden" name="purpose" id="PurposeData">
          <input type="hidden" name="approved_by" id="approved_byData">
          <input type="hidden" name="leave_type_id"value="{{$data->leave_id}}">
          <input type="hidden" name="leave_Assign_id"value="{{$leaveAssignEmp->id}}">
          <input type="hidden" name="haflday" class="halfday" value="{{$leaveAssignEmp->half_day}}">
          <input type="hidden" name="day_taken" id="day_takenData">

          <hr>
          <p style="font-weight: bold; color: #800000">
              Do you want to proceed?
          </p>
      </div>
        <div class="modal-footer">
          <input type="submit" name="ManageStatus" value="Approve" class="btn btn-success">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
            </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirm Decline</h4>
        </div>
      <form action="{{route('ManagerApproval.Leave')}}" method="post" id="contactForm">     
       {{ csrf_field() }}
       <div class="modal-body">
          <p>
              You are about to Approve Leave with following details
          </p>
          <p>
              Leave Start Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="MainContent_StartDate23" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <p>
              Leave End Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="MainContent_EndDate23" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <p>
              Total Days Approved:&nbsp;&nbsp;<span id="MainContent_TotalDays2" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <input type="hidden" name="approved_start_date" id="MainContent_StartDate_old">
          <input type="hidden" name="approved_end_date" id="MainContent_EndDate_old">
          <input type="hidden" name="acting_staff" id="acting_staffData2">
          <input type="hidden" name="days_applied" id="days_appliedData2">
          <input type="hidden" name="date" id="DateData2">
          <input type="hidden" name="comments" id="CommentsData2">
          <input type="hidden" name="emp_id" value="{{$data->emp_id}}">
          <input type="hidden" name="purpose" id="PurposeData2">
          <input type="hidden" name="approved_by" id="approved_byData2">
          <input type="hidden" name="leave_type_id"value="{{$data->leave_id}}">
          <input type="hidden" name="leave_Assign_id"value="{{$leaveAssignEmp->id}}">
          <input type="hidden" name="haflday" class="halfday" value="{{$leaveAssignEmp->half_day}}">

          <input type="hidden" name="day_taken" id="day_takenData2">

          <hr>
          <p style="font-weight: bold; color: #800000">
              Do you want to proceed?
          </p>
      </div>
        <div class="modal-footer">
          <input type="submit" name="ManageStatus" value="Decline" class="btn btn-success">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
            </form>
      </div>
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
   <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript">
   $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });

    $(document).ready(function() {
      $('#contactForm2').click(function(e){
        e.preventDefault();
         var StratDate = $('.StratDate').val();
         var EndDate = $('.EndDate').val();
         var HalfDaty = $('.halfday').val();
          $.ajax({
           type: "get",
           url: "{{route('HrApproval.calcuction')}}",
           data: {EndDate,StratDate,HalfDaty}, // serializes the form's elements.
           success: function(data){
        $('#MainContent_TotalDays').html(data);
        $('#day_takenData').val(data);
        $('#MainContent_StartDate').html($('.StratDate').val());
        $('#MainContent_EndDate').html($('.EndDate').val()); 
        $('#MainContent_StartDate2').val($('.StratDate').val());
        $('#MainContent_EndDate2').val($('.EndDate').val());
        $('#acting_staffData').val($('.acting_staff').val());
        $('#days_appliedData').val($('.days_applied').val());
        $('#DateData').val($('.date2').val());
        $('#CommentsData').val($('.comments').val());
        $('#PurposeData').val($('.purpose').val());
        $('#approved_byData').val($('.approved_by').val());
        $('#myModal').modal('show');
      }
      });
    });
      $('#contactForm3').click(function(e){
        e.preventDefault();
         var StratDate = $('.StratDate').val();
         var EndDate = $('.EndDate').val();
         var HalfDaty = $('.halfday').val();

          $.ajax({
           type: "get",
           url: "{{route('HrApproval.calcuction')}}",
           data: {EndDate,StratDate,HalfDaty}, // serializes the form's elements.
           success: function(data){
        $('#MainContent_TotalDays2').html(data);
        $('#day_takenData2').val(data);
        $('#MainContent_StartDate23').html($('.StratDate').val());
        $('#MainContent_EndDate23').html($('.EndDate').val()); 
        $('#MainContent_StartDate_old').val($('.StratDate').val());
        $('#MainContent_EndDate_old').val($('.EndDate').val());
        $('#acting_staffData2').val($('.acting_staff').val());
        $('#days_appliedData2').val($('.days_applied').val());
        $('#DateData2').val($('.date2').val());
        $('#CommentsData2').val($('.comments').val());
        $('#PurposeData2').val($('.purpose').val());
        $('#approved_byData2').val($('.approved_by').val());
        $('#myModal2').modal('show')
      }
    });
        });
});
</script>  
</script>   
@endsection