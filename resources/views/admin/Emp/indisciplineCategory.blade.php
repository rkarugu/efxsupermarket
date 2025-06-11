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
                       <div class="box-header with-border no-padding-h-b">  <h4>Create Indiscipline</h4>
                          <hr>  
                          <div class="row" style="margin-top: 15px;">
    <form class="validate form-horizontal"  role="form" method="POST" 
    @if(!empty($bankDetail)) action="{{route('emp.update',['id'=>$bankDetail->id])}}" {} @else() action="{{route('emp.IndisciplineCreate')}}"@endif enctype = "multipart/form-data">
      {{ csrf_field() }}
    <div class="col-lg-6">
          <div class="form-group">
            <input type="hidden" name="emp_id" value="{{$dataEmp3->id}}">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Indiscipline Category</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('indiscipline_category_id', $indisciplineCat, null, ['placeholder'=>'Select Indiscipline Category', 'class' => 'form-control','required'=>true,'title'=>'Please Indiscipline Category'  ])!!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Effective Date</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('effective_date',null, ['maxlength'=>'255','placeholder' => 'Effective Date', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
                    </div>
                </div>
                 <div class="form-group">
            <input type="hidden" name="emp_id" value="{{$dataEmp3->id}}">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Action Taken</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                         {!!Form::select('action_id', $indisciplineCAction, null, ['placeholder'=>'Select Action Taken', 'class' => 'form-control','required'=>true,'title'=>'Please Action Taken'  ])!!}  
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Cost Charged</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('cost_charge',null, ['maxlength'=>'255','placeholder' => 'Cost Charged', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
              <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label"   style="margin-top: 5px;">Indiscipline</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('indiscipline',
                         null, ['maxlength'=>'255','placeholder' => 'Indiscipline', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Loction</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('loction', null, ['maxlength'=>'255','placeholder' => 'Loction', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Attach Letter</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::file('attach_letter', null, ['maxlength'=>'255','placeholder' => 'Attach Letter', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Descrption</label>
                    <div class="col-lg-9" style="margin-top: 5px;">
                        {!! Form::text('descrption', null, ['maxlength'=>'255','placeholder' => 'Descrption', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
    <div class="col-lg-12"><hr>
            <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
            <input type="submit" name="" value="Back"  class="btn btn-success btn-sm">
        </div>
  </form>
  </div>
   <hr>
  <div class="" style="margin-top: 30px;">
    <table class="ctable">
  <tr style="background: #34cfeb;color: white;">
    <th>EmpNo</th>
    <th>Indiscipline Category</th>
    <th>Action Taken</th>
    <th>Indiscipline</th>
    <th>Effective Date</th>
    <th></th>
  </tr>
  @foreach($waEmpIndisciplineCategoryData as $val)
  <tr>
    <td>{{$dataEmp3->staff_number}}</td>
    <td>{{$val->getIndisciplineCategory ? $val->getIndisciplineCategory->indiscipline_category  : 'Na'}}</td>
    <td>{{$val->getIndisciplineAction ? $val->getIndisciplineAction->indiscipline_action  : 'Na'}}</td>

    <td>{{$val->indiscipline}}</td>
    <td>{{$val->effective_date}}</td>
<!--     <td>{{$val->indiscipline}}</td>
 -->    <td style="text-align: center;">
     <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{route('emp.IndisciplineDelete',['id'=>$val->id])}}"> <button class="" style="background-color: #ebdc34;border: 2px solid #ebdc34;">Delete</button></td></a>
  </tr>@endforeach
</table>
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
