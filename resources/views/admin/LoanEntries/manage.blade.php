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

        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                       <div class="box-header with-border no-padding-h-b">  <h4>Employee Profile</h4>
                        	<hr>	
                        	<div class="row">
                        <div class="col-md-3 col-sm-3">
                            <a href="#">
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$empData->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </a>
                        
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
                         <h4>Employee Profile</h4><hr>
                       <form class="validate form-horizontal"  role="form" method="POST" action="{{route('LoanEntries.create')}}" enctype = "multipart/form-data">
                         {{ csrf_field() }}
                         <div class="col-lg-12 col-md-12">
                        <div class="col-lg-6">
                          <input type="hidden" name="emp_id" value="{{$empData->id}}">
                          <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Loan Type</label>
                              <div class="col-lg-9">
                              {!!Form::select('loan_type_id', $loanTypeData, null, ['placeholder'=>'Select Loan Type', 'class' => 'form-control','required'=>true,'title'=>'Please Loan Type'  ])!!}
                          </div>
                        </div> 
                        <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">No. Of Installments</label>
                              <div class="col-lg-9">
                              {!!Form::text('no_of_installments', null, ['placeholder'=>'Select No. Of Installments', 'class' => 'form-control','required'=>true  ])!!}
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Ref Number</label>
                              <div class="col-lg-9">
                              {!!Form::text('ref_number', null, ['placeholder'=>'Ref Number', 'class' => 'form-control','required'=>true  ])!!}
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Monthly Deduction</label>
                              <div class="col-lg-9">
                              {!!Form::text('monthly_deduction', null, ['placeholder'=>'Monthly Deduction', 'class' => 'form-control','required'=>true  ])!!}
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Amount Applied</label>
                            <div class="col-lg-9">
                            {!! Form::text('amount_applied', null, ['maxlength'=>'255','placeholder' => 'Amount Applied', 'required'=>true, 'class'=>'form-control']) !!}  
                          </div>
                         </div>
                         <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Date</label>
                            <div class="col-lg-9">
                            {!! Form::text('date', null, ['maxlength'=>'255','placeholder' => 'Date', 'required'=>true, 'class'=>'form-control datepicker']) !!}  
                          </div>
                         </div>
                         <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Memo</label>
                            <div class="col-lg-9">
                            {!! Form::text('memo', null, ['maxlength'=>'255','placeholder' => 'Memo', 'required'=>true, 'class'=>'form-control']) !!}  
                          </div>
                         </div>
                        <div class="form-group">
                        <label for="inputEmail3" class="col-lg-3 control-label">Active</label>
                        <input type="radio" name="active" value="Yes" selected="selected"> Yes 
                        <input type="radio" name="active" value="No"> No<br>
                        </div>
                        </div>
                        <div class="col-lg-12">
                         <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
                        </div>
                      </div>
                    </form>
                      <hr>
                      <div class="col-lg-12" style="margin-top: 20px;">
                      <center>
                       <span id="MainContent_lblactive" class="label-success" style="display:inline-block;color:White;font-size:Medium;width:100%;">Active</span>
                    </center>
                  </div>
                      <div class="col-lg-12">
                        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1"  style="border-collapse:collapse;margin-top: 20px;">
                        <tbody>
                          <tr style="color:White;background-color:#3AC0F2;">
                            <th scope="col">#</th>
                            <th scope="col">Loan Type</th>
                            <th scope="col">Ref Number</th>
                            <th scope="col">Amount Applied</th>
                            <th scope="col">No. Of Installments</th>
                            <th scope="col">Date</th>
                            <th scope="col">Monthly Deduction</th>
                            <th scope="col">Memo</th>
                            <th scope="col">&nbsp;</th>
              </tr>
              @foreach($loanEntriesData as $key => $avl2)
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$avl2->LoanEntriesData ? $avl2->LoanEntriesData->loan_type : 'NA'}}</td>
                <td>{{$avl2->ref_number}}</td>
                <td>{{$avl2->amount_applied}}</td>
                <td>{{$avl2->no_of_installments}}</td>
                <td>{{$avl2->date}}</td>
                <td>{{$avl2->monthly_deduction}}</td>
                <td>{{$avl2->memo}}</td>
               <td><a href="{{route('LoanEntries.Delete',['id'=>$avl2->id])}}"><input type="button" value="Delete" onclick="javascript:__doPostBack('ctl00$MainContent$gridTimesheet','DeleteWE$0')" class="btn btn-warning btn-xs"></td>
            </tr>@endforeach
        </tbody>
      </table>
        <center>
                       <span id="MainContent_lblactive" class="label-danger" style="display:inline-block;color:White;font-size:Medium;width:100%;">DeActive</span>
                    </center>
                  </div>
                      <div class="col-lg-12">
                        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1"  style="border-collapse:collapse;margin-top: 20px;">
                        <tbody>
                          <tr style="color:White;background-color:#3AC0F2;">
                            <th scope="col">#</th>
                            <th scope="col">Loan Type</th>
                            <th scope="col">Ref Number</th>
                            <th scope="col">Amount Applied</th>
                            <th scope="col">No. Of Installments</th>
                            <th scope="col">Date</th>
                            <th scope="col">Monthly Deduction</th>
                            <th scope="col">Memo</th>
                            <th scope="col">&nbsp;</th>
              </tr>
              @foreach($loanEntriesDeData as $key => $avl)
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$avl->LoanEntriesData ? $avl->LoanEntriesData->loan_type : 'NA'}}</td>
                <td>{{$avl->ref_number}}</td>
                <td>{{$avl->amount_applied}}</td>
                <td>{{$avl->no_of_installments}}</td>
                <td>{{$avl->date}}</td>
                <td>{{$avl->monthly_deduction}}</td>
                <td>{{$avl->memo}}</td>
               <td><a href="{{route('LoanEntries.Delete',['id'=>$avl->id])}}"><input type="button" value="Delete" onclick="javascript:__doPostBack('ctl00$MainContent$gridTimesheet','DeleteWE$0')" class="btn btn-warning btn-xs"></td>
            </tr>@endforeach
        </tbody>
      </table>
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
