@extends('layouts.admin.admin')
<style type="text/css">
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
    </style>
 @section('content')
 <section class="content">    
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
             @include('message')
            <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.update',['id'=>$row->id]) }}" enctype = "multipart/form-data">
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="col-lg-6">
                           <label>Staff No</label>
                            <span style="color: red;"> *</span>
                            {!! Form::text('staff_number', $row->staff_number, ['maxlength'=>'255','placeholder' => 'Staff No', 'required'=>true, 'class'=>'form-control']) !!}  
                            <br>
                             <label>Branch</label>
                            <span style="color: red;"> *</span>
                              {!!Form::select('branch_id', $restaurantDataEdit, $row->branch_id, ['placeholder'=>'Select Branch', 'class' => 'form-control','required'=>true,'title'=>'Please Branch'  ])!!} 
                            <br>
                             <label>Job Title</label>
                            <span style="color: red;"> *</span>
                            {!!Form::select('job_title_id', $jobDataUp, $row->job_title, ['placeholder'=>'Select Job Title', 'class' => 'form-control','required'=>true,'title'=>'Please Job Title'  ])!!} 
                            <br>
                             <label>Middle Name</label>
                            <span style="color: red;"> *</span>
                            {!! Form::text('middle_name', $row->middle_name, ['maxlength'=>'255','placeholder' => 'Middle Name', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                             <label>Id Number</label>
                            <span style="color: red;"> *</span>
                            {!! Form::text('id_number', $row->Id_number, ['maxlength'=>'255','placeholder' => 'Id Number', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                             <label>NSSF No</label>
                            <span style="color: red;"> *</span>
                            {!! Form::text('nssf_no', $row->nssf_no, ['maxlength'=>'255','placeholder' => 'NSSF No', 'required'=>true, 'class'=>'form-control']) !!}<br>
                              <label>Marital Status</label><span style="color: red;"> *</span>
                            {!!Form::select('marital_status_id',
                             $marital_statusUp, $row->marital_status, ['placeholder'=>'Select Marital Status', 'class' => 'form-control','required'=>true,'title'=>'Please Marital Status'  ])!!}
                             <br>
                              <label>Salutation</label><span style="color: red;"> *</span>
                            {!!Form::select('salutation',
                             $salutationUp, $row->salutation_id, ['placeholder'=>'Select Salutation', 'class' => 'form-control','required'=>true,'title'=>'Please Salutation'  ])!!} <br>
                            <label>Cellphone</label>
                            {!! Form::text('cellphone', $row->cellphone, ['maxlength'=>'255','placeholder' => 'Cellphone', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Job Group</label><span style="color: red;"> *</span>
                            {!!Form::select('job_group', $job_groupUp, $row->job_group_id, ['placeholder'=>'Select Job Group', 'class' => 'form-control','required'=>true,'title'=>'Please Job Group'  ])!!}<br>
                            <label>Bank</label><span style="color: red;"> *</span>
                            {!!Form::select('bank_id', $bankDataUp, $row->bank_id, ['placeholder'=>'Select Bank', 'class' => 'form-control','required'=>true,'title'=>'Please Bank'  ])!!}
                            <br>
                            <label>Account No</label><span style="color: red;"> *</span> 
                            {!! Form::text('account_number', $row->account_no, ['maxlength'=>'255','placeholder' => 'Account No', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                            <label>Passport No.</label>
                            {!! Form::text('password_number', $row->passport_number, ['maxlength'=>'255','placeholder' => 'Passport No.', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Postal Address</label>
                            {!! Form::text('postal_address', $row->postal_address, ['maxlength'=>'255','placeholder' => 'Postal Address', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Town</label>
                            {!! Form::text('town', $row->town, ['maxlength'=>'255','placeholder' => 'Town', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Home Phone</label>
                            {!! Form::text('home_phone', $row->home_phone, ['maxlength'=>'255','placeholder' => 'Home Phone', 'required'=>false, 'class'=>'form-control']) !!}<br>

                       </div>
                  <div class="col-lg-6">
                      <label>Emp Number</label>
                          <span style="color: red;"> *</span>
                             {!!Form::text('emp_number',$row->emp_number, ['placeholder'=>'Emp Number', 'class' => 'form-control','required'=>true])!!} 
                             <br>

                        <label>Employment Type</label>
                          <span style="color: red;"> *</span>
                             {!!Form::select('type_id', $empTypeUp, $row->employment_type_id, ['placeholder'=>'Select Employment Type', 'class' => 'form-control','required'=>true,'title'=>'Please Employment type'  ])!!} 
                             <br>
                            <label>Department</label>
                          <span style="color: red;"> *</span>
                             {!!Form::select('department_id', $departmentDataUp, $row->department_id, ['placeholder'=>'Select Department', 'class' => 'form-control','required'=>true,'title'=>'Please Department'  ])!!} 
                            <br>
                            <label>First Name</label>
                          <span style="color: red;"> *</span>
                            {!! Form::text('first_name', $row->first_name, ['maxlength'=>'255','placeholder' => 'First Name', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                            <label>Last Name</label>
                          <span style="color: red;"> *</span>
                            {!! Form::text('last_name', $row->last_name, ['maxlength'=>'255','placeholder' => 'Last Name', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                            <label>NHIF No</label>
                            {!! Form::text('nhif_no', $row->nhif_no, ['maxlength'=>'255','placeholder' => 'NHIF No', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Gender</label><span style="color: red;"> *</span>
                            {!!Form::select('gender_id', $genderDataUp, $row->gender_id, ['placeholder'=>'Select Gender', 'class' => 'form-control','required'=>true,'title'=>'Please Gender'  ])!!}<br>
                            <label>D.O.B</label><span style="color: red;"> *</span>  {!! Form::text('date_of_birth', $row->date_of_birth, ['maxlength'=>'255','placeholder' => 'D.O.B', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                          <br>
                            <label>Date Employeed</label><span style="color: red;"> *</span> 
                            {!! Form::text('date_employeed', $row->date_employed, ['maxlength'=>'255','placeholder' => 'Date employeed', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                            <br>
                            <label>PIN No</label><span style="color: red;"> *</span> 
                            {!! Form::text('pin_number', $row->pin_number, ['maxlength'=>'255','placeholder' => 'PIN No', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                            <label>Pay Frequency</label>
                              {!!Form::select('payment_frequency_id', $payment_frequencyUp, $row->pay_frequency_id, ['placeholder'=>'Select Pay Frequency', 'class' => 'form-control','required'=>false,'title'=>'Please Pay Frequency'  ])!!} <br>
                            <label>Basic Pay</label><span style="color: red;"> *</span> 
                            {!! Form::text('basic_pay', $row->basic_pay, ['maxlength'=>'255','placeholder' => 'Basic Pay', 'required'=>true, 'class'=>'form-control']) !!}
                            <br>
                            <label>Email Address</label>
                            {!! Form::text('email_address', $row->email_address, ['maxlength'=>'255','placeholder' => 'Email Address', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Postal Code</label>
                            {!! Form::text('postal_code', $row->postal_code, ['maxlength'=>'255','placeholder' => 'Postal Code', 'required'=>false, 'class'=>'form-control']) !!}<br>
                            <label>Country</label>
                            {!! Form::text('country', $row->country, ['maxlength'=>'255','placeholder' => 'Country', 'required'=>false, 'class'=>'form-control']) !!}
                            <br>
                            <label>Home District</label>
                            {!! Form::text('home_district', $row->home_district, ['maxlength'=>'255','placeholder' => 'Home District', 'required'=>false, 'class'=>'form-control']) !!}
                            
                      </div>
                    </div>
                </div>
            </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
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

        