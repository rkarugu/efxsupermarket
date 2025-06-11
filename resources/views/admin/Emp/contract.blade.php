@extends('layouts.admin.admin')
<style type="text/css">
  .box{
    border-top:none !important;
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
</style>
@section('content')
  <!-- Main content -->
    <section class="content">
                   @include('message')

       <div class="box">
         <div class="box-header no-padding-h-b">
          <h4>Employee Contracts</h4>
          <div class="row">
            <div class="col-lg-4 col-md-4">
              <h5><b style="color:red;">Emp No :   {{$emDatat2->staff_number}}</b></h5>
            </div>
             <div class="col-lg-4 col-md-4">
               <h5><b style="color:red;">Contracts</b></h5>
            </div>
             <div class="col-lg-4 col-md-4">
                <h5><b style="color:red;">Employee Name :  {{$emDatat2->first_name}} {{$emDatat2->middle_name}} {{$emDatat2->last_name}}</b></h5>
            </div>
          </div>
            <hr style="background-color:red;height: 2px;margin-top: 0px !important;">
<div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" action="{{route('contract.store')}}" enctype = "multipart/form-data">
          {{ csrf_field() }}
    <div class="col-lg-6">
      <input type="hidden" name="emp_id" value="{{$emDatat2->id}}">
          <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Contract Start Date</label>
                    <div class="col-lg-9">
                        {!! Form::text('contract_start_date', null, ['maxlength'=>'255','placeholder' => 'Contract Start Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Employment Type</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('emp_type', $empType, null, ['placeholder'=>'Select Employment Type', 'class' => 'form-control','required'=>true,'title'=>'Please Employment Type'  ])!!}  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Comment</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('comment', null, ['maxlength'=>'255','placeholder' => 'Comment', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
         <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Contract End Date</label>
                    <div class="col-lg-9">
                        {!! Form::text('contract_end_date', null, ['maxlength'=>'255','placeholder' => 'Contract End Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">New Staff No.</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('staff_no', null, ['maxlength'=>'255','placeholder' => 'New Staff No', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
           </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
           <a href="{{route('employee.index')}}"> <input name="" value="Back"  class="btn btn-success btn-sm" style="width: 9%;"></a>
        </div>
     </form>
    </div>

  <hr>
  <div class="" style="margin-top: 30px;">
    <table class="table ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Name</th>
    <th>Employment Type</th>
    <th>Start Date</th>
    <th>Expiry Date</th>
    <th></th>
    <th></th>
  </tr>
  @foreach($empDataGet as $val)
  <tr>
    <td>{{$emDatat2->staff_number}}</td>
    <td>{{$emDatat2->first_name}} {{$emDatat2->middle_name}} {{$emDatat2->last_name}}</td>
    <td>{{$val->getEmploymentType ? $val->getEmploymentType->type : 'NA'}}</td>
    <td>{{$val->contract_start_date}}</td>
    <td>{{$val->contract_end_date}}</td>
    <td style="text-align: center;">
<!--       <button class="" style="background-color: #43eb34;border: 2px solid #43eb34;">Edit</button></td>
 -->    <td style="text-align: center;">
  <a href="{{route('contract.delete',['id'=>$val->id])}}" onclick="return confirm('Are you sure you want to delete this item?');">
      <button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></a></td>
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
