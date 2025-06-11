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
                    <div class="box box-primary" style="margin-top: 20px;">
                       <div class="box-header with-border no-padding-h-b">
                        <div class="col-lg-12 col-md-12">
                            <div class="tab">

  <a href="?Types=LeaveTypes"><button class="tablinks {{ Request::get('Types') == 'LeaveTypes' ? 'active' : ''}}" onclick="openCity(event, 'London')">Add Per Employee</button></a>
  <a href="?Types=Holidays"><button class="tablinks {{ Request::get('Types') == 'Holidays' ? 'active' : ''}}" onclick="openCity(event, 'Holidays')">Add Per Department</button></a>

</div>

<div id="London" class="tabcontent" style="{{ Request::get('Types') == 'LeaveTypes' || Request::get('Types') == ''  ? 'display: block;' : ''}}">
    <div class="row" style="margin-top: 15px;">
        <form class="validate form-horizontal"  role="form" method="POST" @if(!empty($updateData)) action="{{route('Entitlements.update',['id'=>$updateData->id])}}" @else() action="{{route('Entitlements.CreateStore')}}" @endif enctype = "multipart/form-data">
      {{ csrf_field() }}

        <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Employee</label>
          <div class="col-lg-9">
              {!! Form::select('employee_id', $empData, @$updateData->employee_id, ['maxlength'=>'255','placeholder' => 'Select Employee', 'required'=>true, 'class'=>'form-control EmpDataGet']) !!}  
          </div>
        </div> 
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Entitlement</label>
            <div class="col-lg-9">
            {!! Form::text('entitlement',@$updateData->entitlement, ['maxlength'=>'255','placeholder' => 'Entitlement', 'required'=>true, 'class'=>'form-control FirstID','readonly'=>true]) !!}  
            </div>
            </div>
             <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Opening Balance</label>
            <div class="col-lg-9">
            {!! Form::text('opening_balance',@$updateData->opening_balance, ['maxlength'=>'255','placeholder' => 'Opening Balance', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
        </div>
        <?php
         $dateFormant  = date('Y');
        ?>
        <div class="col-lg-6">
             <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Leave Period</label>
                    <div class="col-lg-9">
                        {!! Form::select('leave_period',array($dateFormant => 'Jan  1 '.$dateFormant.' -Dec 31 '.$dateFormant.'  '),@$updateData->leave_period, ['maxlength'=>'255','placeholder' => 'Leave Period', 'required'=>true, 'class'=>'form-control']) !!}  

                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Leave Type</label>
                    <div class="col-lg-9">
                      {!! Form::select('leave_type_id', $leaveTypeData, @$updateData->leave_type_id, ['maxlength'=>'255','placeholder' => 'Select Leave Type','disabled'=>true, 'required'=>true, 'class'=>'form-control leaveID']) !!}  
                    </div>
                </div>
                  <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Default Entitlement</label>
            <div class="col-lg-9">
            {!! Form::text('default_entitlement',@$updateData->default_entitlement, ['maxlength'=>'255','placeholder' => 'Default Entitlement', 'required'=>true, 'class'=>'form-control FirstID','readonly'=>true]) !!}  
            </div>
            </div>
            </div>
        <div class="col-lg-12"><hr>
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
             <th align="left" scope="col">Emp No</th>
             <th align="left" scope="col">Leave Type</th>
             <th align="left" scope="col">Entitlement</th>
             <th align="left" scope="col">Default Entitlement</th>
             <th align="left" scope="col">Opening Balance</th>
             <th align="left" scope="col">EarnedLeave Days</th>
             <th align="left" scope="col">Balance</th>
             <th scope="col" style="width:70px;">&nbsp;</th>
             <th scope="col">&nbsp;</th>
        </tr>
          @foreach($entitlementsDataa as $key => $val)
          <tr>
            <td>{{$key+1}}</td>
            <td>{{$val->leave_period}}</td>
            <td>{{$val->EmpDataGet ? $val->EmpDataGet->first_name : 'NA'}} {{$val->EmpDataGet ? $val->EmpDataGet->middle_name : 'NA'}} {{$val->EmpDataGet ? $val->EmpDataGet->last_name : 'NA'}}</td>
            <td>{{$val->LeaveDataGet ? $val->LeaveDataGet->leave_type : 'NA'}}</td>
            <td>{{$val->entitlement}}</td>
            <td>{{$val->default_entitlement}}</td>
            <td>{{$val->opening_balance}}</td>
            <td>{{$val->default_entitlement}}</td>
            <td>{{$val->default_entitlement - $val->opening_balance}}</td>
            <td style="width:70px;"><a href="?Types=LeaveTypes&Edit={{$val->id}}"><input type="button" value="Edit" onclick="javascript:__doPostBack('ctl00$MainContent$TabContainer1$TabPanel1$gridEntitlements','Select$0')" class="btn btn-success btn-xs"></a></td>
            <td><a href="{{route('Entitlements.Delete',['id'=>$val->id])}}"><input type="button" value="Delete" onclick="javascript:__doPostBack('ctl00$MainContent$TabContainer1$TabPanel1$gridEntitlements','DeleteWE$0')" class="btn btn-warning btn-xs"></a></td>
          </tr>
          @endforeach
        </tbody>
            </table>
            </div>                                      
          </div>
        </div>
     </div>
     <div id="Holidays" class="tabcontent" style="{{ Request::get('Types') == 'Holidays' ? 'display: block;' : ''}}">
    <div class="row" style="margin-top: 15px;">
        <form class="validate form-horizontal" 
        action="{{route('Entitlements.DepartmentsCreate')}}" role="form" method="POST"  enctype = "multipart/form-data">
      {{ csrf_field() }}

        <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Department</label>
          <div class="col-lg-9">
              {!! Form::select('department_id', $departmentData, null, ['maxlength'=>'255','placeholder' => 'Select Department', 'required'=>true, 'class'=>'form-control']) !!}  
          </div>
        </div> 
         
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Leave Period</label>
            <div class="col-lg-9">
            {!! Form::select('leave_period',array($dateFormant => 'Jan  1 '.$dateFormant.' -Dec 31 '.$dateFormant.'  '),@$updateData->leave_period, ['maxlength'=>'255','placeholder' => 'Leave Period', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            

        </div>
        <div class="col-lg-6">
             <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Leave Type</label>
                    <div class="col-lg-9">
                      {!! Form::select('leave_type_id', $leaveTypeData, null, ['maxlength'=>'255','placeholder' => 'Select Leave Type', 'required'=>true,'class'=>'form-control ']) !!}  
                    </div>
                </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Entitlement</label>
            <div class="col-lg-9">
            {!! Form::text('entitlement', null, ['maxlength'=>'255','placeholder' => 'Entitlement', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            </div>
        <div class="col-lg-12"><hr>
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
                        <th align="left" scope="col">Emp No</th>
                        <th align="left" scope="col">Emp Name</th>
                        <th align="left" scope="col">Leave Type</th>
                        <th align="left" scope="col">Entitlement</th>
                        <th scope="col" style="width:70px;">Default Entitlement</th>
                        <th scope="col" style="width:70px;">EarnedLeave Days</th>
                        <th scope="col">&nbsp;</th>
                     </tr>
                     @foreach($wa_entitlementsDepartmentData as $key => $val2)
                     <tr>
                       <td>{{$key+1}}</td>
                       <td>{{$val2->leave_period}}</td>
                       <td>{{$val2->EmpDataGet2 ? $val2->EmpDataGet2->staff_number : 'NA'}}</td>
                       <td>{{$val2->EmpDataGet2 ? $val2->EmpDataGet2->first_name : 'NA'}} {{$val2->EmpDataGet2 ? $val2->EmpDataGet2->middle_name : 'NA'}} {{$val2->EmpDataGet2 ? $val2->EmpDataGet2->last_name : 'NA'}}</td>
                       <td>{{$val2->LeaveDataGet2 ? $val2->LeaveDataGet2->leave_type : 'NA'}}</td>
                       <td>{{$val2->entitlement}}</td>
                       <td>{{$val2->entitlement}}</td>
                       <td>{{$val2->entitlement}}</td>
                         <td><a href="{{route('Holidays.Delete',['id'=>$val2->id])}}"><input type="button" value="Delete" onclick="javascript:__doPostBack('ctl00$MainContent$TabContainer1$TabPanel1$gridEntitlements','DeleteWE$0')" class="btn btn-warning btn-xs"></a></td>

                    </tr>@endforeach
                </tbody>
            </table>
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
       $('.leaveID').prop('disabled', false);



   $(".EmpDataGet").on("change", function() {
       $('.leaveID').prop('disabled', false);
      var Emp = $(this).val();
       $.ajax({
           type: "get",
           url: "{{route('YearCalcution.Get')}}",
           data: {Emp}, // serializes the form's elements.
           success: function(data2){
            $('.assignLeaveDataBasic').val(data2);
           }
         });
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
</script>  

@endsection
