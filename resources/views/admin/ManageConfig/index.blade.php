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

  <a href="?Types=LeaveTypes"><button class="tablinks {{ Request::get('Types') == 'LeaveTypes' ? 'active' : ''}}" onclick="openCity(event, 'London')">Leave Types</button></a>
  <a href="?Types=Holidays"><button class="tablinks {{ Request::get('Types') == 'Holidays' ? 'active' : ''}}" onclick="openCity(event, 'Holidays')">Holidays</button></a>

</div>

<div id="London" class="tabcontent" style="{{ Request::get('Types') == 'LeaveTypes' || Request::get('Types') == ''  ? 'display: block;' : ''}}">
    <div class="row" style="margin-top: 15px;">
        <form class="validate form-horizontal"  role="form" method="POST"  @if(!empty($updateData)) action="{{route('LeaveConfig.Update',['id'=>$updateData->id])}}" @else() 
         action="{{route('LeaveConfig.Store')}}" @endif enctype = "multipart/form-data">
      {{ csrf_field() }}

        <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Leave Type</label>
          <div class="col-lg-9">
              {!! Form::text('leave_type',@$updateData->leave_type, ['maxlength'=>'255','placeholder' => 'Leave Type', 'required'=>true, 'class'=>'form-control']) !!}  
          </div>
        </div> 
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Default Entitlement</label>
            <div class="col-lg-9">
            {!! Form::text('default_entitlement',@$updateData->default_entitlement, ['maxlength'=>'255','placeholder' => 'Default Entitlement', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Narration</label>
                    <div class="col-lg-9">
                        {!! Form::text('narration',@$updateData->narration, ['maxlength'=>'255','placeholder' => 'Narration', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Recurring</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch1">
                    <input type="checkbox" name="recurring" class="onoffswitch1-checkbox" value="On" id="myonoffswitch1" tabindex="0" {{@$updateData->recurring == 'On' ?  'checked="checked"' : ''}}>
                    <label class="onoffswitch1-label" for="myonoffswitch1">
                        <span class="onoffswitch1-inner"></span>
                        <span class="onoffswitch1-switch"></span>
                    </label>
                      </div>
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
             <th align="left" scope="col">Leave Type</th>
             <th align="left" scope="col">Default Entitlement</th>
             <th align="left" scope="col">Narration</th>
             <th align="left" scope="col">Recurring</th>
             <th scope="col" style="width:70px;">&nbsp;</th>
             <th scope="col">&nbsp;</th>
        </tr>
        @foreach($leaveTypeData as $key => $value)
           <tr>
              <td>{{$key+1}}</td>
              <td>{{$value->leave_type}}</td>
              <td>{{$value->default_entitlement}}</td>
              <td>{{$value->narration}}</td>
              <td><input type="checkbox" name="" {{ $value->recurring  == 'On' ? 'checked="checked"'  : '' }} disabled="disabled"></span></td>
            <td><a href="?Edit={{$value->id}}"><input type="button" value="Edit" class="btn btn-success btn-xs"></a></td>
            <td><a href="{{route('Leave.Delete',['id'=>$value->id])}}"><input type="button" value="Delete" class="btn btn-warning btn-xs"></a></td>
        </tr>@endforeach
        </tbody>
            </table>
            </div>                                      
          </div>
        </div>
     </div>
     <div id="Holidays" class="tabcontent" style="{{ Request::get('Types') == 'Holidays' ? 'display: block;' : ''}}">
    <div class="row" style="margin-top: 15px;">
        <form class="validate form-horizontal" 
        action="{{route('LeaveConfig.HoliDayCreate')}}" role="form" method="POST"  enctype = "multipart/form-data">
      {{ csrf_field() }}

        <div class="col-lg-6">
        <div class="form-group">
          <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Holiday Name</label>
          <div class="col-lg-9">
              {!! Form::text('holiday_name', null, ['maxlength'=>'255','placeholder' => 'Holiday Name', 'required'=>true, 'class'=>'form-control']) !!}  
          </div>
        </div> 
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Description</label>
            <div class="col-lg-9">
            {!! Form::text('description', null, ['maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
            <div class="form-group">
            <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Payrate</label>
            <div class="col-lg-9">
            {!! Form::text('payrate', null, ['maxlength'=>'255','placeholder' => 'Payrate', 'required'=>true, 'class'=>'form-control']) !!}  
            </div>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Date</label>
                    <div class="col-lg-9">
                        {!! Form::text('date',  null, ['maxlength'=>'255','placeholder' => 'Date', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Repeats Annually</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch">
                    <input type="checkbox" name="repeats_annually" class="onoffswitch-checkbox" value="On" id="myonoffswitch" tabindex="0">
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                      </div>
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
                        <th align="left" scope="col">Holiday Name</th>
                        <th align="left" scope="col">Pay Rate</th>
                        <th align="left" scope="col">Date</th>
                        <th scope="col" style="width:70px;">Repeats Annually</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                     </tr>
                     @foreach($holidaysData as $key => $val2)
                     <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$val2->holiday_name}}</td>
                        <td>{{$val2->payrate}}</td>
                        <td>{{$val2->date}}</td>
                        <td><span class="aspNetDisabled"><input type="checkbox" name="ctl00$MainContent$TabContainer1$TabPanel2$gridLeavetype$ctl07$ctl00" disabled="disabled" {{ $val2->repeats_annually  == 'On' ? 'checked="checked"'  : '' }} ></span></td>
                        <td><a href="?Edit={{$val2->id}}"><input type="button" value="Edit" class="btn btn-success btn-xs"></a></td>
                        <td><a href="{{route('HoliDay.Delete',['id'=>$val2->id])}}"><input type="button" value="Delete" class="btn btn-warning btn-xs"></a></td>
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
</script>   
@endsection
