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
               <h4>Assign Leave</h4><hr>
      <div class="col-lg-12 col-md-12">                
    <div class="row" style="margin-top: 15px;"> 
        <form class="validate form-horizontal"  @if(!empty($updataData)) action="{{route('AssignLeave.Update',['id'=>$updataData->id])}}" @else() 
        action="{{route('Assign.Store')}}" @endif role="form" method="POST"  enctype = "multipart/form-data">
      {{ csrf_field() }}
      <div class="col-md-12 col-lg-12">
      <div class="col-lg-1">
          <label for="inputEmail3" class="control-label"   style="margin-top: 5px;">Employee</label>

      </div>
      <div class="col-lg-5">
        <div class="form-group" style="margin-left: 23px;width: 330px !important;"> 
              {!! Form::select('emp_id', $empData, @$updataData->emp_id, ['maxlength'=>'255','placeholder' => 'Select Employee', 'required'=>true, 'class'=>'form-control EmpID']) !!}  
          </div>
      </div>
    </div>
          <div class="col-lg-6">
             <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Leave Period</label>
                    <div class="col-lg-9">
                        {!! Form::select('leave_period',array($dateFormant => 'Jan  1 '.$dateFormant.' -Dec 31 '.$dateFormant.'  '),@$updataData->leave_period, ['maxlength'=>'255','placeholder' => 'Leave Period', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>

            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">From</label>
            <div class="col-lg-9">
            {!! Form::text('from', @$updataData->from, ['maxlength'=>'255','placeholder' => 'From', 'required'=>true,'disabled'=>true, 'class'=>'form-control date2']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Half Day?</label>
            <div class="col-lg-9">
              {!! Form::select('half_day',array('Yes' => 'Yes','No' => 'No'),@$updataData->half_day, ['maxlength'=>'255','placeholder' => 'Half Day', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Acting Staff</label>
            <div class="col-lg-9">
            {!! Form::text('acting_staff', @$updataData->acting_staff, ['maxlength'=>'255','placeholder' => 'Acting Staff', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Attach Document</label>
            <div class="col-lg-9">
            {!! Form::file('attach_document', null,null, ['maxlength'=>'255','placeholder' => 'Attach Document', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
            </div>
            </div>
        </div>
        <div class="col-lg-6">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Leave Type</label>
                    <div class="col-lg-9">
                     {!! Form::select('leave_id', $leave_data, @$updataData->leave_id, ['maxlength'=>'255','placeholder' => 'Select Leave Type', 'required'=>true,'disabled'=>true,'class'=>'form-control select leaveID']) !!}  
                    </div>
                </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">To</label>
            <div class="col-lg-9">
            {!! Form::text('to', @$updataData->to, ['maxlength'=>'255','placeholder' => 'To', 'required'=>true, 'disabled'=>true,'class'=>'form-control date2']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Leave Balance</label>
            <div class="col-lg-9">
            {!! Form::text('leave_balance', @$updataData->leave_balance, ['maxlength'=>'255','placeholder' => 'Leave Balance', 'required'=>true,'readonly'=>true, 'class'=>'form-control leave_balance']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Purpose</label>
            <div class="col-lg-9">
            {!! Form::text('purpose', @$updataData->purpose, ['maxlength'=>'255','placeholder' => 'Purpose', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            </div>
        <div class="col-lg-12">
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
      </div>
    </form>
    <hr>
    <div class="col-md-12" style="margin-top: 15px;">
            <div class="table-responsive">
                <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="rows" border="1" id="MainContent_TabContainer1_TabPanel2_gridLeavetype" style="width:100%;border-collapse:collapse;">
                    <tbody>
                        <tr style="color:White;background-color:#3AC0F2;">
                        <th align="left" scope="col">#</th>
                        <th align="left" scope="col">Year</th>
                        <th align="left" scope="col">Staff No</th>
                        <th align="left" scope="col">Employee</th>
                        <th align="left" scope="col">Leave Type </th>
                        <th scope="col" style="width:70px;">From</th>
                        <th scope="col" style="width:70px;">To</th>
                        <th scope="col" style="width:70px;">Date Approved</th>
                        <th scope="col" style="width:70px;">Total Days  </th>
                        <th scope="col" style="width:70px;">Approved By</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                     </tr>
                     @foreach($assignLeaveData as $key => $val2)
                     <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$val2->leave_period}}</td>
                        <td>{{$val2->EmpDataGet2 ? $val2->EmpDataGet2->staff_number : 'NA'}}</td>
                        <td>{{$val2->EmpDataGet2 ? $val2->EmpDataGet2->first_name : 'NA'}} {{$val2->EmpDataGet2 ? $val2->EmpDataGet2->middel_name : 'NA'}} {{$val2->EmpDataGet2 ? $val2->EmpDataGet2->last_name : 'NA'}}</td>
                        <td>{{$val2->LeaveDataGet2 ? $val2->LeaveDataGet2->leave_type : 'NA'}}</td>
                        <td>{{$val2->from}}</td>
                        <td>{{$val2->to}}</td>
                        <td>{{$val2->date_approved}}</td>
                        <td>{{$val2->day_taken}}</td>
                        <td>{{$val2->UData ? $val2->UData->name : 'NA'}}</td>
<!--                         <td><a href="?Edit={{$val2->id}}"><input type="button" value="Edit" class="btn btn-success btn-xs"></a></td>
 -->                        <td><a href="{{route('AssignLeave.Delete',['id'=>$val2->id])}}"><input type="button" value="Delete" class="btn btn-warning btn-xs"></a></td>
                    </tr>@endforeach
                </tbody>
            </table>
           </div>
          </div>
        </div>
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript">
  var date = new Date();
  date.setDate(date.getDate());
 
 

   $(".EmpID").on("change", function() {
      var EmpID = $(this).val();
       $.ajax({
           type: "get",
           url: "{{route('AssignLeaveGet.Get')}}",
           data: {EmpID}, // serializes the form's elements.
           success: function(data){
             console.log('data =============',data);
            $('.leave_balance').val(data.TotalDaya);
            $('.date2').prop('disabled', false);
            $('.leaveID').prop('disabled', false);
            $('.date2').datepicker({ 
               minDate: data.end_date, // Range start
            });
           }
         });
  });
  
</script>  
@endsection
