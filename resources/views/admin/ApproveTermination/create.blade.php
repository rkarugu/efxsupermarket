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


.onoffswitch3 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch3-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch3-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch3-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch3-inner:before, .onoffswitch3-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch3-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch3-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch3-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-inner {
    margin-left: 0;
}
.onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-switch {
    right: 0px; 
}

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
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$dataEmp3->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </a>
                         
                        </div>
                       <div class="col-md-9 col-sm-9">
                          <table class="table">
                            <tbody>
                            	<tr>
                                <td><strong>Staff No</strong></td>
                                <td>:</td>
                                <td><b>{{$dataEmp3->staff_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Employee Name</strong></td>
                                <td>:</td>
                                <td><b>{{$dataEmp3->first_name}} {{$dataEmp3->middle_name}} {{$dataEmp3->last_name}}</b></td>
                            </tr>
                            <tr>
                               <td><strong>PIN Number</strong></td>
                               <td>:</td>
                               <td><b>{{$dataEmp3->pin_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>ID Number</strong></td>
                                <td>:</td>
                                <td><b>{{$dataEmp3->Id_number}}</b></td>
                            </tr>
                            <tr>
                            	<td><strong>NSSF Number</strong></td>
                                <td>:</td>
                                <td><b>{{$dataEmp3->nssf_no}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>NHIF Number</strong></td>
                                <td>:</td>
                                <td><b>{{$dataEmp3->nhif_no}}</b></td>
                            </tr>
                            </tbody>
                        </table>
                       </div>
                        </div>
                    </div>
                </div>
                 <div class="box box-primary">
                       <div class="box-header with-border no-padding-h-b">  <h4>Termination Detail</h4>
                          <hr>  
                          <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" 
    action="{{route('ApproveTermination.store')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$dataEmp3->id}}">
                <div class="form-group">
                <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Type of Termination</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                    {!!Form::select('type_of_termination', $termination_types, $separationTermnation->type_of_termination, ['placeholder'=>'Select Type of Termination', 'class' => 'form-control','required'=>true,'title'=>'Please Type of Termination'  ])!!} 
                    </div>
                </div>
                 <div class="form-group">
            <input type="hidden" name="emp_id" value="{{$dataEmp3->id}}">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Termination Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                    {!! Form::text('termination_date',$separationTermnation->termination_date, ['maxlength'=>'255','placeholder' => 'Termination Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Last Day Worked</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!! Form::text('last_day_worked',$separationTermnation->last_day_worked, ['maxlength'=>'255','placeholder' => 'Last Day Worked', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Attach Termination Letter</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!! Form::file('termination_letter',null, ['maxlength'=>'255','placeholder' => 'Termination Letter', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
               
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Eligible For Rehire ?</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch1">
                    <input type="checkbox" name="eligible_for_rehire" class="onoffswitch1-checkbox" value="On" id="myonoffswitch1" tabindex="0"  @if($separationTermnation->eligible_for_rehire) == 'On' checked @endif>
                    <label class="onoffswitch1-label" for="myonoffswitch1">
                        <span class="onoffswitch1-inner"></span>
                        <span class="onoffswitch1-switch"></span>
                    </label>
                      </div>
                </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Notice Period</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('notice_period', $separationTermnation->notice_period, ['maxlength'=>'255','placeholder' => 'Notice Period', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Attach Clearance Letter</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!! Form::file('termination_clearance',null, ['maxlength'=>'255','placeholder' => 'Termination Clearance', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Reason</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!! Form::textarea('reason', $separationTermnation->reason, ['maxlength'=>'255','placeholder' => 'Reason', 'required'=>true,'rows' => 8, 'cols' => 54, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Comment</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('comment', $separationTermnation->comment, ['maxlength'=>'255','placeholder' => 'Comment', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
               <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Notice Given ?</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch3">
                    <input type="checkbox" name="notice_given" class="onoffswitch3-checkbox" value="On" id="myonoffswitch3" tabindex
                    ="0" @if($separationTermnation->notice_given) == 'On' checked @endif>
                    <label class="onoffswitch3-label" for="myonoffswitch3">
                        <span class="onoffswitch3-inner"></span>
                        <span class="onoffswitch3-switch"></span>
                    </label>
                      </div>
                 </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Attach Service Letter</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!! Form::file('termination_service',null, ['maxlength'=>'255','placeholder' => 'Termination Service', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label">Cleared</label>
                    <div class="col-lg-9">
                    <div class="onoffswitch">
                    <input type="checkbox" name="cleared" class="onoffswitch-checkbox" value="On" id="myonoffswitch" tabindex
                    ="0" checked>
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                      </div>
                 </div>
                </div>
                
            </div>
    <div class="col-lg-12"><hr>
      @if(count($ApporveTermnation) > 0)
        <input type="submit" name="" disabled="disabled" value="Approve Terminated" class="btn btn-success btn-sm">
        @else()
        <input type="submit" name="" value="Approve Terminated" class="btn btn-success btn-sm">
         @endif()
            <a href="{{route('ApproveTermination.index')}}">
            <input style="width: 10%;" name="" value="Back"  class="btn btn-success btn-sm"></a>
        </div>
  </form>
  </div>
   <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Type of Termination</th>
    <th>Termination Date</th>
    <th>Last Day Worked</th>
    <th>Notice Period</th>
    <th></th>
  </tr>
  @foreach($separationTermnationData as $val3)
  <tr>
    <td>{{$dataEmp3->staff_number}}</td>
    <td>{{$val3->DataGet3 ? $val3->DataGet3->separation_type : 'NA'}}</td>
    <td>{{$val3->termination_date}}</td>
    <td>{{$val3->last_day_worked}}</td>
    <td>{{$val3->notice_period}}</td>
     <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('ApproveTermination.delete',['id'=>$val3->id])}}"> <button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
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
</script>	
@endsection
