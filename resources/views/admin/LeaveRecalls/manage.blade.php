@extends('layouts.admin.admin')
@section('content')
<section class="content">
<div class="box box-primary">
       @include('message')<br>

     <br>
     <div class="box-header with-border no-padding-h-b">  <h4>Employee Profile</h4>
                            <hr>    
                            <div class="row">
                        <div class="col-md-3 col-sm-3">
                            <a href="#">
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$leaveAssignDataView->EmpData->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        </a>
                        
                        </div>
                       <div class="col-md-9 col-sm-9">
                          <table class="table">
                            <tbody>
                                <tr>
                                <td><strong>Staff No</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignDataView->EmpData ? $leaveAssignDataView->EmpData->staff_number : 'NA'}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Employee Name</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignDataView->EmpData ? $leaveAssignDataView->EmpData->first_name : 'NA'}} {{$leaveAssignDataView->EmpData ? $leaveAssignDataView->EmpData->middle_name : 'NA'}} {{$leaveAssignDataView->EmpData ? $leaveAssignDataView->EmpData->last_name : 'NA'}}</b></td>
                            </tr>
                            <tr>
                               <td><strong>Leave Type</strong></td>
                               <td>:</td>
                               <td><b>{{$leaveAssignDataView->LeaveDataGet2 ? $leaveAssignDataView->LeaveDataGet2->leave_type : 'NA'}} </b></td>
                            </tr>
                            <tr>
                                <td><strong>From</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignDataView->from}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>To</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignDataView->to}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Days Taken</strong></td>
                                <td>:</td>
                                <td><b>{{$leaveAssignDataView->day_taken}}</b></td>
                            </tr>
                            </tbody>
                        </table>
                       </div>
                        </div>
                    </div>
                </div>
                 <div class="box box-primary" style="margin-top: 20px;">
                       <div class="box-header with-border no-padding-h-b">
                         <h4>Leave Recalls</h4><hr>
        <form class="validate form-horizontal" action="{{route('Leave.Recalls')}}"
         role="form" method="POST"  enctype = "multipart/form-data">
                                   {{ csrf_field() }}
                        <div class="col-lg-12 col-md-12">
                        <div class="col-lg-6">
                          <?php  
                          $uData = \Session::get('userdata');
                          ?>
                          <input type="hidden" name="assign_leave_id" value="{{$leaveAssignDataView->id}}">
                          <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label">Recalled By</label>
                              <div class="col-lg-9">
                             {!! Form::text('recalled_by', $uData->name,['maxlength'=>'255','placeholder' => 'Recalled By', 'required'=>true, 'class'=>'form-control']) !!}  
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label">Date Recalled</label>
                            <div class="col-lg-9">
                           {!! Form::text('date_recalled', null, ['maxlength'=>'255','placeholder' => 'Date Recalled', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
                        </div>
                      </div>
                        </div>
                        <div class="col-lg-6">
                             <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Reason</label>
                              <div class="col-lg-9">
                            {!! Form::text('reason', null, ['maxlength'=>'255','placeholder' => 'Reason', 'required'=>true, 'class'=>'form-control']) !!}  
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-12">
                           <div class="pull-right">
                          <div class="form-group">
                                      <input type="submit" id="contactForm2" name="Save" value="Save" class="btn btn-success btn-sm">

                                  </div>
                              </div>
                          </div>
                      </div>
                    </form>
  </div>
                         <div class="box-header with-border no-padding-h-b">                            <div class="col-md-12 ">
                                <center>
                                    <span id="MainContent_lblapproved" class="label label-success" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Leave Recalls
</span></center>
                                <div>
    <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridapproved" style="border-collapse:collapse;">
        <tbody><tr>
            <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col">Employee</th>
            <th align="left" scope="col">Leave Type</th>
            <th align="left" scope="col">From</th>
            <th align="left" scope="col">To</th>
            <th align="left" scope="col">Total Days</th>
            <th align="left" scope="col">Date Recalled</th>
            <th align="left" scope="col">Recalled By</th>
        </tr>
        @foreach($leaveRecalls as $value)
        <tr>
          <td>{{$value->EmpData ? $value->EmpData->staff_number : 'NA'}}</td>
          <td>{{$value->EmpData ? $value->EmpData->first_name : 'NA'}} {{$value->EmpData ? $value->EmpData->middle_name : 'NA'}} {{$value->EmpData ? $value->EmpData->last_name : 'NA'}}</td>
          <td>{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : 'NA'}}</td>
          <td>{{$value->AssignLeave ? $value->AssignLeave->from : 'NA'}}</td>
          <td>{{$value->AssignLeave ? $value->AssignLeave->to : 'NA'}}</td>
          <td>{{$value->AssignLeave ? $value->AssignLeave->day_taken : 'NA'}}</td>
          <td>{{$value->date_recalled}}</td>
          <td>{{$value->ReCallsLeave ? $value->ReCallsLeave->name : 'NA'}}</td>
        </tr>@endforeach
            </tbody>
   </table>
   </div>
  </div>
  
 </div>
</div>
  <div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title">Confirm Recalls</h4>
        </div>
      <form action="http://demo2server.com/production/admin/emp/HrApproval/ApprovalHr" method="post" id="contactForm">     
       <input type="hidden" name="_token" value="UaAAfMu77BHC1U7oHnzb6GKTJT0jkjcaJIYRXKjG">
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
              Total Days Approved:&nbsp;&nbsp;<span id="MainContent_TotalDays" style="color:CadetBlue;font-weight:bold;"></span>
          </p>
          <input type="hidden" name="approved_start_date" id="MainContent_StartDate_old">
          <input type="hidden" name="approved_end_date" id="MainContent_EndDate_old">
          <input type="hidden" name="acting_staff" id="acting_staffData2">
          <input type="hidden" name="days_applied" id="days_appliedData2">
          <input type="hidden" name="date" id="DateData2">
          <input type="hidden" name="comments" id="CommentsData2">
          <input type="hidden" name="emp_id" value="5">
          <input type="hidden" name="purpose" id="PurposeData2">
          <input type="hidden" name="approved_by" id="approved_byData2">
          <input type="hidden" name="leave_type_id" value="5">
          <input type="hidden" name="day_taken" id="day_takenData2">
          <input type="hidden" name="leave_Assign_id" value="9">
          <hr>
          <p style="font-weight: bold; color: #800000">
              Do you want to proceed?
          </p>
      </div>
        <div class="modal-footer">
          <input type="submit" name="Status" value="Decline" class="btn btn-success">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
            </form>
      </div>
    </div>
  </div>
</section> @endsection
    @section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
   $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });

   $(".leaveID").on("change", function() {
      var LeaveID = $(this).val();
       $.ajax({
           type: "get",
           url: "{{route('LeaveType.Get')}}",
           data: {LeaveID}, // serializes the form's elements.
           success: function(data){
            $('.FirstID').val(data.default_entitlement);
           }
         });
  });
    $(function () {
    $(".mlselect").select2();
    });
</script>   
@endsection