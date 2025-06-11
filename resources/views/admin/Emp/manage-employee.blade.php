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
        content: "Active";
        padding-left: 10px;
        background-color: #34A7C1; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "DeActive";
        padding-right: 1px;
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
        content: "Active";
        padding-left: 10px;
        background-color: #34A7C1; color: #FFFFFF;
    }
    .onoffswitch1-inner:after {
        content: "DeActive";
        padding-right: 1px;
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
                        </a><a href="" target="_blank">Download CV</a>
                        
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

  <button class="tablinks {{ Request::get('Types') == '' ? 'active' : ''}}" onclick="openCity(event, 'London')">Bio Data</button>
  <a href="?Types=WorkInfo"><button class="tablinks {{ Request::get('Types') == 'WorkInfo' ? 'active' : ''}}" onclick="openCity(event, 'Paris')">Work Info</button></a>
  <a href="?Types=Bank"><button class="tablinks {{ Request::get('Types') == 'Bank' ? 'active' : ''}}" onclick="openCity(event, 'Tokyo')">Bank</button></a>

  <a href="?Types=Experience"><button class="tablinks {{ Request::get('Types') == 'Experience' ? 'active' : ''}}" onclick="openCity(event, 'Expraince')">Experience</button></a>
  <a href="?Types=Kin"><button class="tablinks {{ Request::get('Types') == 'Kin' ? 'active' : ''}}" onclick="openCity(event, 'Next of Kin')">Next of Kin</button></a>
  <a href="?Types=Dependents"><button class="tablinks {{ Request::get('Types') == 'Dependents' ? 'active' : ''}}" onclick="openCity(event, 'Dependents')">Dependents</button></a>
  <a href="?Types=Educations">
  <button class="tablinks {{ Request::get('Types') == 'Educations' ? 'active' : ''}}" onclick="openCity(event, 'Educations')">Education</button></a>
    <a href="?Types=Docments">
  <a href="?Types=Docments"><button class="tablinks  {{ Request::get('Types') == 'Docments' ? 'active' : ''}}" onclick="openCity(event, 'Educations')" onclick="openCity(event, 'Docments')">Document</button></a>
  <a href="?Types=Contacts">
  <button class="tablinks {{ Request::get('Types') == 'Contacts' ? 'active' : ''}}" onclick="openCity(event, 'Contacts')">Contacts</button></a>
  <a href="?Types=Referees">
  <button class="tablinks {{ Request::get('Types') == 'Referees' ? 'active' : ''}}" onclick="openCity(event, 'Referees')">Referees</button></a>
</div>

<div id="London" class="tabcontent" style="{{ Request::get('Types') == '' ? 'display: block;' : ''}}">
	<div class="row" style="margin-top: 15px;">
		<form class="validate form-horizontal"  role="form" method="POST" action="{{route('Emp-Bio-Data.Update',['id' => $empData->id])}}" enctype = "multipart/form-data">
      {{ csrf_field() }}

		<div class="col-lg-6">
			  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">First name</label>
                    <div class="col-lg-9">
                        {!! Form::text('first_name', $empData->first_name, ['maxlength'=>'255','placeholder' => 'First name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Last name</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('last_name', $empData->last_name, ['maxlength'=>'255','placeholder' => 'Last name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">NHIF No</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('nhif_no', $empData->nhif_no, ['maxlength'=>'255','placeholder' => 'NHIF No', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">PIN No</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('pin_number', $empData->pin_number, ['maxlength'=>'255','placeholder' => 'NHIF No', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">D.O.B</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('date_of_birth', $empData->date_of_birth, ['maxlength'=>'255','placeholder' => 'D.O.B', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Marital Status</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!!Form::select('marital_status_id',
                             $marital_statusM, $empData->marital_status, ['placeholder'=>'Select Marital Status', 'class' => 'form-control','required'=>true,'title'=>'Please Marital Status'  ])!!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Cellphone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('cellphone', $empData->cellphone, ['maxlength'=>'255','placeholder' => 'Cellphone', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Passport No.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('password_number', $empData->passport_number, ['maxlength'=>'255','placeholder' => 'Passport No.', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                   <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Driving License.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('driving_license', $empData->driving_license, ['maxlength'=>'255','placeholder' => 'Driving License', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                   <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Years of Service.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::number('years_of_service', $empData->years_of_service, ['maxlength'=>'255','placeholder' => 'Years of Service', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                   <label for="inputEmail3" class="col-lg-3 control-label" 	style="margin-top: 5px;">Home District.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!! Form::text('home_district', $empData->home_district, ['maxlength'=>'255','placeholder' => 'Home District', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
                 <div class="form-group">
                 <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;"> Curiculum Vitae</label>
                  <div class="col-lg-9" style="margin-top: 5px;">
                  {!! Form::file('curiculum_vitae', null, ['maxlength'=>'255','placeholder' => 'Curiculum Vitae', 'required'=>true, 'class'=>'form-control']) !!}
                </div>
              </div>
               <div class="form-group">
                   <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">Helb No.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!! Form::text('helb_number', $empData->helb_number, ['maxlength'=>'255','placeholder' => 'Helb No.', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
		</div>
		<div class="col-lg-6">
			 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Middle name</label>
                    <div class="col-lg-9">
                        {!! Form::text('middle_name', $empData->middle_name, ['maxlength'=>'255','placeholder' => 'Middle name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Staff No.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('staff_number', $empData->staff_number, ['maxlength'=>'255','placeholder' => 'Staff No.', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">NSSF No.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('nssf_no', $empData->nssf_no, ['maxlength'=>'255','placeholder' => 'NSSF No', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Id Number</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('Id_number', $empData->Id_number, ['maxlength'=>'255','placeholder' => 'ID Number', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Gender</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('gender_id', $genderDataM, $empData->gender_id, ['placeholder'=>'Select Gender', 'class' => 'form-control','required'=>true,'title'=>'Please Gender'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Salutation</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('salutation',
                             $salutation_manage, $empData->salutation_id, ['placeholder'=>'Select Salutation', 'class' => 'form-control','required'=>true,'title'=>'Please Salutation'  ])!!} 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Email</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('email_address', $empData->email_address, ['maxlength'=>'255','placeholder' => 'Email Address', 'required'=>false, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Date Employed</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('date_employeed', $empData->date_employed, ['maxlength'=>'255','placeholder' => 'Date employeed', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
             <!--    <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Date Terminated</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('date_terminated', $empData->date_terminated, ['maxlength'=>'255','placeholder' => 'Date Terminated', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                            <br>
                    </div>
                </div> -->
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ethnicity</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('ethnicity', $empData->ethnicity, ['maxlength'=>'255','placeholder' => 'Ethnicity', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                            <br>
                    </div>
                </div>
                <div class="form-group">
                 <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;"> Image</label>
                  <div class="col-lg-9" style="margin-top: 5px;">
                  {!! Form::file('photo_emp', null, ['maxlength'=>'255','placeholder' => 'Photo Image', 'required'=>true, 'class'=>'form-control']) !!}
                </div>
              </div>
              <div class="form-group">
                 <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Sacco Member No.</label>
                  <div class="col-lg-9" style="margin-top: 5px;">
                  {!! Form::text('sacco_member_no', $empData->sacco_member_no, ['maxlength'=>'255','placeholder' => 'Sacco Member No', 'required'=>true, 'class'=>'form-control']) !!}
                </div>
              </div>
                 <div class="form-group">
                        <label for="inputEmail3" class="col-lg-3 control-label">Status</label>
                        <div class="col-lg-9">
                        <div class="onoffswitch1">
                        <input type="checkbox" name="status" class="onoffswitch1-checkbox" value="On" id="myonoffswitch1" tabindex="0" {{$empData->status == 'Active' ? 'checked="checked"' : ''}}>
                        <label class="onoffswitch1-label" for="myonoffswitch1">
                            <span class="onoffswitch1-inner"></span>
                            <span class="onoffswitch1-switch"></span>
                        </label>
                          </div>
                    </div>
                    </div>
                     <div class="form-group">
                 <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Pension No.</label>
                  <div class="col-lg-9" style="margin-top: 5px;">
                  {!! Form::text('pension_number', $empData->pension_number, ['maxlength'=>'255','placeholder' => 'Pension No', 'required'=>true, 'class'=>'form-control']) !!}
                </div>
              </div>

            </div>
		<div class="col-lg-12"><hr>
            <input type="submit" name="" value="Update" class="btn btn-success btn-sm">
        </div>
	</form>
	</div>
</div>

<div id="Paris" class="tabcontent" style="{{ Request::get('Types') == 'WorkInfo' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" @if(!empty($dataWorkExp)) action="{{route('employee.BankInformationUpdate',['id'=>@$dataWorkExp->id])}}" @else() action="{{route('employee.BankInformation')}}" @endif enctype = "multipart/form-data">
      {{ csrf_field() }}
    <div class="col-lg-6">
          <div class="form-group">
            <input type="hidden" name="Emp_id" value="{{$empData->id}}">
            <input type="hidden" name="emp_id" value="{{$empData->id}}">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Company</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('company_id', $companyPreferenceData, @$dataWorkExp->company_id, ['placeholder'=>'Select Compnay', 'class' => 'form-control','required'=>true,'title'=>'Please Compnay'  ])!!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Region</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('region',
                         @$dataWorkExp->region, ['maxlength'=>'255','placeholder' => 'Region', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Department</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('department_id', $departmentDataMange, @$dataWorkExp->department_id, ['placeholder'=>'Select Department', 'class' => 'form-control','required'=>true,'title'=>'Please Department'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Job Group</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('job_group_id', $job_groupManage, @$dataWorkExp->job_group_id, ['placeholder'=>'Select Job Group', 'class' => 'form-control','required'=>true,'title'=>'Please Job Group'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Job Grade</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('job_grade_id', $jobGradeManage, @$dataWorkExp->job_grade_id, ['placeholder'=>'Select Grade', 'class' => 'form-control','required'=>true,'title'=>'Please Grade'  ])!!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Shift</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('shift',
                         @$dataWorkExp->shift, ['maxlength'=>'255','placeholder' => 'Shift', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Manager</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('manager',
                         @$dataWorkExp->manager, ['maxlength'=>'255','placeholder' => 'Manager', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
                   <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Employement Status</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('employement_status', $emp_status, @$dataWorkExp->employement_status, ['placeholder'=>'Select Employement Status', 'class' => 'form-control','required'=>true,'title'=>'Please Employement Status'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Probation Start Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                     {!! Form::text('probation_start_date',
                         @$dataWorkExp->probation_start_date, ['maxlength'=>'255','placeholder' => 'Probation Start Date', 'required'=>true, 'class'=>'form-control datepicker']) !!}
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
            <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Branch</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('branch_id', $branchDataM, @$dataWorkExp->branch_id, ['placeholder'=>'Select Branch', 'class' => 'form-control','required'=>true,'title'=>'Please Branch'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Station</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('station', @$dataWorkExp->station, ['maxlength'=>'255','placeholder' => 'Station', 'required'=>true, 'class'=>'form-control']) !!} 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Section</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!! Form::text('section', @$dataWorkExp->section, ['maxlength'=>'255','placeholder' => 'Section', 'required'=>true, 'class'=>'form-control']) !!} 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Designation</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('designation_id', $jobDataManage, @$dataWorkExp->designation_id, ['placeholder'=>'Select Designation', 'class' => 'form-control','required'=>true,'title'=>'Please Designation'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Employement Type</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('employement_type_id', $empTypeData, @$dataWorkExp->employement_type_id, ['placeholder'=>'Select Employement Type', 'class' => 'form-control','required'=>true,'title'=>'Please Employement Type'  ])!!}  
                    </div>
                </div>
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">HomePhone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('home_phone',
                         @$dataWorkExp->home_phone, ['maxlength'=>'255','placeholder' => 'Home Phone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">HOD</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('hod', @$dataWorkExp->hod, ['maxlength'=>'255','placeholder' => 'HOD', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Date Of Confirmation</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('date_of_confirmation', @$dataWorkExp->date_of_confirmation, ['maxlength'=>'255','placeholder' => 'Date Of Confirmation', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Probation End Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('probation_end_date', @$dataWorkExp->probation_end_date, ['maxlength'=>'255','placeholder' => 'Probation End Date', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
  </form>
  </div>
</div>

<div id="Tokyo" class="tabcontent" style="{{ Request::get('Types') == 'Bank' ? 'display: block;' : ''}}">
<div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" 
    @if(!empty($bankDetail)) action="{{route('emp.update',['id'=>$bankDetail->id])}}" {} @else() action="{{route('employee.EmpBankStore')}}"@endif enctype = "multipart/form-data">
      {{ csrf_field() }}
    <div class="col-lg-6">
          <div class="form-group">
            <input type="hidden" name="emp_id" value="{{$empData->id}}">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Bank</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('bank_id', $bankDataM, @$bankDetail->bank_id, ['placeholder'=>'Select Bank', 'class' => 'form-control','required'=>true,'title'=>'Please Bank'  ])!!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Pay Frequency</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('pay_frequency_id', $pay_frequency, @$bankDetail->pay_frequency_id, ['placeholder'=>'Select Pay Frequency', 'class' => 'form-control','required'=>true,'title'=>'Please Pay Frequency'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Relief</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('relief_id', $relief, @$bankDetail->relief_id, ['placeholder'=>'Select Relief', 'class' => 'form-control','required'=>true,'title'=>'Please Relief'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Valuntary NSSF</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('valuntary_nssf',@$bankDetail->valuntary_nssf, ['maxlength'=>'255','placeholder' => 'Valuntary NSSF', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
            <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Branch</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('branch_id', $branchDataM, @$bankDetail->branch_id, ['placeholder'=>'Select Branch', 'class' => 'form-control','required'=>true,'title'=>'Please Branch'  ])!!}  
                    </div>
                </div>
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Account Name</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('account_name',
                         @$bankDetail->account_name, ['maxlength'=>'255','placeholder' => 'Account Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Account Number</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('account_number', @$bankDetail->account_number, ['maxlength'=>'255','placeholder' => 'Account Number', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Payment Mode</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('payment_mode_id', $payment_mode,@$bankDetail->payment_mode_id, ['placeholder'=>'Select Payment Mode', 'class' => 'form-control','required'=>true,'title'=>'Please Payment Mode'  ])!!}  
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Update" class="btn btn-success btn-sm">
        </div>
  </form>
  </div>
</div>
<div id="Expraince" class="tabcontent"  style="{{ Request::get('Types') == 'Experience' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" action="{{route('employee.EmpExperienceStore')}}" enctype = "multipart/form-data">
      {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="typeTabs" value="Expraince">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Organization</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('organization', null, ['maxlength'=>'255','placeholder' => 'Organization', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Form</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('from', null, ['maxlength'=>'255','placeholder' => 'form', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Reason for Leaving</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                      {!! Form::textarea('reason_for_leaving', null, ['id' => 'Reason for Leaving', 'rows' => 4, 'cols' => 54, 'placeholder' => 'From', 'required'=>true,'class'=>'form-control']) !!} 
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Job Title</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('job_title_id', $job_title,null, ['placeholder'=>'Select Job Title', 'class' => 'form-control','required'=>true,'title'=>'Please Job Title'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">To</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!! Form::text('to', null, ['maxlength'=>'255','placeholder' => 'To', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Memo</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('memo', null, ['maxlength'=>'255','placeholder' => 'Memo', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
  </form>
  </div>
  <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Organization</th>
    <th>Job Title</th>
    <th>Form</th>
    <th>To</th>
    <th></th>
    <th></th>
  </tr>
  @foreach($empExpData as $val)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$val->organization}}</td>
    <td>Accountad Manager</td>
    <td>{{$val->from}}</td>
    <td>{{$val->to}}</td>
    <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td>
    <td style="text-align: center;">
      <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('EmpExperience.Delete',['id'=>$val->id])}}">
      <button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
  </div>
</div>

<div id="Next of Kin" class="tabcontent" style="{{ Request::get('Types') == 'Kin' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" action="{{route('next-kin.store')}}" enctype = "multipart/form-data">
       {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Name</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Job Title</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('job_title_id', $job_title,null, ['placeholder'=>'Select Job Title', 'class' => 'form-control','required'=>true,'title'=>'Please Job Title'  ])!!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Relationship</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('relationship', null, ['maxlength'=>'255','placeholder' => 'Relationship', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Email</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('email', null, ['maxlength'=>'255','placeholder' => 'Email', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Postal Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('postal_address', null, ['maxlength'=>'255','placeholder' => 'Postal Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Organization</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('organization',
                         Null, ['maxlength'=>'255','placeholder' => 'Organization', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Memo</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('memo', null, ['maxlength'=>'255','placeholder' => 'Memo', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Profession</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('profession', null, ['maxlength'=>'255','placeholder' => 'Profession', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Cellphone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('cellphone', null, ['maxlength'=>'255','placeholder' => 'Cellphone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Physical Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('physical_address', null, ['maxlength'=>'255','placeholder' => 'Physical Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
    </form>
   </div>
   <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>Next Of Kin Name</th>
    <th>Job Titile</th>
    <th>Relationship</th>
    <th>Cell phone</th>
    <th>Email Address</th>
    <th></th>
    <th></th>
  </tr>
  @foreach($nextKinStoreData as $val2)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$val2->getNextKin  ? $val2->getNextKin->job_title : 'NA'}}</td>
    <td>{{$val2->relationship}}</td>
    <td>{{$val2->cellphone}}</td>
    <td>{{$val2->email}}</td>
 <!--    <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td> -->
    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('NextKin.Delete',['id'=>$val2->id])}}"><button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></a></td><
  </tr>@endforeach
</table>
  </div>
</div>
<div id="Dependents" class="tabcontent" style="{{ Request::get('Types') == 'Dependents' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="post" action="{{route('Dependents.store')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Name</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Memo</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('memo', null, ['maxlength'=>'255','placeholder' => 'Memo', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Relationship</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('relationship', null, ['maxlength'=>'255','placeholder' => 'Relationship', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Date of Birth</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('date_of_birth', null, ['maxlength'=>'255','placeholder' => 'Date Of Birth', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Cellphone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('cellphone', null, ['maxlength'=>'255','placeholder' => 'Cellphone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
    </form>
   </div>

  <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Dependant Name</th>
    <th>Date Of Birth</th>
    <th>Relationship</th>
    <th>Cellphone</th>
    <th>Memo</th>
    <th></th>
  </tr>
  @foreach($dependents_Dataa as $dependents_Dataa_val)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$dependents_Dataa_val->name}}</td>
    <td>{{$dependents_Dataa_val->date_of_birth}}</td>
    <td>{{$dependents_Dataa_val->relationship}}</td>
    <td>{{$dependents_Dataa_val->cellphone}}</td>
    <td>{{$dependents_Dataa_val->memo}}</td>
   <!--  <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td> -->
    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('Dependents.Delete',['id'=>$dependents_Dataa_val->id])}}"><button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
  </div>
</div>
<div id="Educations" class="tabcontent" style="{{ Request::get('Types') == 'Educations' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="post" action="{{route('employee.EductionCreate')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Course</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('course', null, ['maxlength'=>'255','placeholder' => 'Course', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Institution</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('institution', null, ['maxlength'=>'255','placeholder' => 'Institution', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">To</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('to', null, ['maxlength'=>'255','placeholder' => 'To', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Point</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('point', null, ['maxlength'=>'255','placeholder' => 'Point', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Memo</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('memo', null, ['maxlength'=>'255','placeholder' => 'Memo', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
               <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Education Level</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('education_level_id', $dataEducationLevel,null, ['placeholder'=>'Select Education Level', 'class' => 'form-control','required'=>true,'title'=>'Please Education Level'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">From</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('from', null, ['maxlength'=>'255','placeholder' => 'From', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Grade</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!!Form::select('job_grade_id', $job_groupManage,null, ['placeholder'=>'Select Grade', 'class' => 'form-control','required'=>true,'title'=>'Please Grade'  ])!!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Ranking</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('ranking', null, ['maxlength'=>'255','placeholder' => 'Ranking', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
    </form>
   </div>
   <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Course</th>
    <th>Education Level</th>
    <th>Institution</th>
    <th>Point</th>
    <th>Grade</th>
    <th>To</th>
    <th>From</th>
    <th></th>
  </tr>
  @foreach($WaEmpEducationDtaa as $dataEducationLevelVal)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$dataEducationLevelVal->course}}</td>
    <td>{{$dataEducationLevelVal->JobEductionData ?  $dataEducationLevelVal->JobEductionData->education_level : ''}}</td>
    <td>{{$dataEducationLevelVal->institution}}</td>
    <td>{{$dataEducationLevelVal->point}}</td>
    <td>{{$dataEducationLevelVal->JobGradeID  ? $dataEducationLevelVal->JobGradeID->job_grade : 'Na' }}</td>
    <td>{{$dataEducationLevelVal->from}}</td>
   <!--  <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td> -->
    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('employee.EductionDelete',['id'=>$dataEducationLevelVal->id])}}"><button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
  </div>
</div>
<div id="Docments" class="tabcontent" style="{{ Request::get('Types') == 'Docments' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="post" action="{{route('employee.EductionDocumentCreate')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Document</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('document', null, ['maxlength'=>'255','placeholder' => 'Document', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Ref No.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('ref_number', null, ['maxlength'=>'255','placeholder' => 'Ref No.', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Issued By</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('issued_by', null, ['maxlength'=>'255','placeholder' => 'Issued By', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Expiry Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('expiry_date', null, ['maxlength'=>'255','placeholder' => 'Expiry Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Select File</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::file('select_file', null, ['maxlength'=>'255','placeholder' => 'File', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                  </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Descrption</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('descrption', null, ['maxlength'=>'255','placeholder' => 'Descrption.', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Issue Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('issue_date', null, ['maxlength'=>'255','placeholder' => 'Issue Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Received Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                       {!! Form::text('received_date', null, ['maxlength'=>'255','placeholder' => 'Received Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
    </form>
   </div>
   <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Document</th>
    <th>Ref Number</th>
    <th>Issued By</th>
    <th>Expiry Date</th>
    <th>Received Date</th>
    <th></th>
  </tr>
  @foreach($WaEmpDocumentsData as $WaEmpDocumentsDataVal)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$WaEmpDocumentsDataVal->document}}</td>
    <td>{{$WaEmpDocumentsDataVal->ref_number}}</td>
    <td>{{$WaEmpDocumentsDataVal->issued_by}}</td>
    <td>{{$WaEmpDocumentsDataVal->expiry_date}}</td>
    <td>{{$WaEmpDocumentsDataVal->received_date}}</td>
   <!--  <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td> -->
    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('employee.DocusDelete',['id'=>$WaEmpDocumentsDataVal->id])}}"><button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
  </div>
</div>
<div id="Contacts" class="tabcontent" style="{{ Request::get('Types') == 'Contacts' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="post" action="{{route('employee.ContactsCreate')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Postal Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('postal_addess', null, ['maxlength'=>'255','placeholder' => 'Postal Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Postal Code</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('postal_code', null, ['maxlength'=>'255','placeholder' => 'Postal Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Country</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('country', null, ['maxlength'=>'255','placeholder' => 'Country', 'required'=>true, 'class'=>'form-control datepicke2r','readonly'=>false]) !!}
                    </div>
                </div>
               <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Mobile</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('mobile', null, ['maxlength'=>'255','placeholder' => 'Mobile', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Email Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('email_address', null, ['maxlength'=>'255','placeholder' => 'Email Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Emergency Contact Cellphone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('emergency_contact_cellphone', null, ['maxlength'=>'255','placeholder' => 'Emergency Contact Cellphone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Street Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('street_address', null, ['maxlength'=>'255','placeholder' => 'Street Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                  </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Town</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('town', null, ['maxlength'=>'255','placeholder' => 'Town', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Home Telephone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('home_telephone', null, ['maxlength'=>'255','placeholder' => 'Home Telephone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Work Telephone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('work_telephone', null, ['maxlength'=>'255','placeholder' => 'Work Telephone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Emergency Contact Person</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('emergency_contact_person', null, ['maxlength'=>'255','placeholder' => 'Emergency Contact Person', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Emergency Contact Relationship</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('emergency_contact_relationship', null, ['maxlength'=>'255','placeholder' => 'Emergency Contact Relationship', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
    </form>
   </div>
    <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Postal Address</th>
    <th>Postal Code</th>
    <th>Street Address</th>
    <th>Country</th>
    <th>Mobile</th>
    <th>Email Address</th>
    <th>Work Telephone</th>
    <th></th>
  </tr>
  @foreach($waEmpContactsData as $waEmpContactsDataValue)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$waEmpContactsDataValue->postal_addess}}</td>
    <td>{{$waEmpContactsDataValue->postal_code}}</td>
    <td>{{$waEmpContactsDataValue->street_address}}</td>
    <td>{{$waEmpContactsDataValue->country}}</td>
    <td>{{$waEmpContactsDataValue->mobile}}</td>
    <td>{{$waEmpContactsDataValue->email_address}}</td>
    <td>{{$waEmpContactsDataValue->work_telephone}}</td>
   <!--  <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td> -->
    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('employee.ContactsDelete',['id'=>$waEmpContactsDataValue->id])}}"><button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
  </div>
</div>
<div id="Referees" class="tabcontent" style="{{ Request::get('Types') == 'Referees' ? 'display: block;' : ''}}">
  <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="post" action="{{route('Emp.Referees')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$empData->id}}">
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Name</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Postal Code</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('postal_code', null, ['maxlength'=>'255','placeholder' => 'Postal Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Email</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                          {!! Form::text('email', null, ['maxlength'=>'255','placeholder' => 'Email', 'required'=>true, 'class'=>'form-control','readonly'=>false]) !!}
                    </div>
                </div>
               <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Postal Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('postal_address', null, ['maxlength'=>'255','placeholder' => 'Postal Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Notes</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('notes', null, ['maxlength'=>'255','placeholder' => 'Notes', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Organization</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('organization', null, ['maxlength'=>'255','placeholder' => 'Organization', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                  </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Profession</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('profession', null, ['maxlength'=>'255','placeholder' => 'Profession', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Cellphone</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('cellphone', null, ['maxlength'=>'255','placeholder' => 'Cellphone', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Physical Address</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('physical_address', null, ['maxlength'=>'255','placeholder' => 'Physical Address', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Memo</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('memo', null, ['maxlength'=>'255','placeholder' => 'Memo', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
        </div>
    </form>
   </div>
   <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Name</th>
    <th>Postal Code</th>
    <th>Organization</th>
    <th>Profession</th>
    <th>Email</th>
    <th>Cellphone</th>
    <th>Postal Address</th>
    <th></th>
  </tr>
  @foreach($empRefereesData as $empRefereesDataValuue)
  <tr>
    <td>{{$empData->staff_number}}</td>
    <td>{{$empRefereesDataValuue->name}}</td>
    <td>{{$empRefereesDataValuue->postal_code}}</td>
    <td>{{$empRefereesDataValuue->organization}}</td>
    <td>{{$empRefereesDataValuue->profession}}</td>
    <td>{{$empRefereesDataValuue->email}}</td>
    <td>{{$empRefereesDataValuue->cellphone}}</td>
    <td>{{$empRefereesDataValuue->postal_address}}</td>
   <!--  <td style="text-align: center;">
      <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td> -->
    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('employee.RefereesDelete',['id'=>$empRefereesDataValuue->id])}}"><button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
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
