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
   background-color: #337ab7;
   border: 1px solid gainsboro;
   color: #fff;
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
   <div class="box box-primary" style="margin-top: 20px;">
      <div class="box-header with-border no-padding-h-b">
         <h4>Employee Detail</h4>
         <hr>
         <div class="col-lg-12 col-md-12">
            <div class="tab">
               <a href="?Types=Employee">
               <button class="tablinks {{ Request::get('Types') == 'Employee' || Request::get('Types') =='' ? 'active' : ''}}" onclick="openCity(event, 'London')">Employees On Leave</button></a>
               <a href="?Types=LeavesPending"><button class="tablinks {{ Request::get('Types') == 'LeavesPending' ? 'active' : ''}}" onclick="openCity(event, 'Paris')">Leaves Pending Approval </button></a>
               <a href="?Types=ScheduledLeaves"><button class="tablinks {{ Request::get('Types') == 'ScheduledLeaves' ? 'active' : ''}}" onclick="openCity(event, 'Imran')">Scheduled Leaves </button></a>
               <a href="?Types=CompletedLeaves"><button class="tablinks {{ Request::get('Types') == 'CompletedLeaves' ? 'active' : ''}}" onclick="openCity(event, 'Completed')">Completed Leaves </button></a>
               <a href="?Types=DeclinedLeaves"><button class="tablinks {{ Request::get('Types') == 'DeclinedLeaves' ? 'active' : ''}}" onclick="openCity(event, 'Rejected')">Declined Leaves </button></a>
            </div>
            <div id="London" class="tabcontent" style="{{ Request::get('Types') == 'Employee' || Request::get('Types') == '' ? 'display: block;' : ''}}">
               <div class="row" style="margin-top: 15px;">
                  <form class="validate form-horizontal"  role="form" method="get"  enctype = "multipart/form-data">
                     {{ csrf_field() }}
                     <div class="col-lg-4">
                        <div class="form-group">
                           <input type="hidden" name="Types2" value="EmployeeOnLeave">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">Leave Type</label>
                           <div class="col-lg-9">
                              {!!Form::select('leave_id', $leaveData, null, ['placeholder'=>'Select Leave Type', 'class' => 'form-control','required'=>true,'title'=>'Please Leave Type'  ])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">From Date</label>
                           <div class="col-lg-9">
                              {!!Form::text('from', null, ['placeholder'=>'Select From', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">To Date</label>
                             <div class="col-lg-9">
                              {!!Form::text('to', null, ['placeholder'=>'Select To', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                       <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-4">
                     </div>
                     <div class="col-md-3">
                     </div>
                     <div class="col-md-5">
                        <div class="form-group">
                           <input type="submit" value="Preview" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;">
                         <a href="{{route('LeaveStatus.PdfDownloadEmployeeLeaveon')}}?leave_id={{Request::get('leave_id')}}&to={{Request::get('to')}}&from={{Request::get('from')}}&Types2=EmployeeOnLeave">
                           <input type="button" value="Export to Pdf" class="btn btn-info btn-sm"></a>
                        <!--    <a href="{{route('LeaveStatus.export')}}"><input type="button" value="Export to Excel" class="btn btn-sm" style="background-color: #5cb85c !important; color: #fff !important; border-color: #4cae4c !important;"></a>
                           <input type="submit" value="Export to Word" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;"> -->
                        </div>
                     </div>
                  </div>
               </div>
                  </form>
               </div>
             
               <center>
                  <span id="MainContent_Label2" class="label label-success" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Employees On Leave</span>
               </center>
               <div>
                  <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridemp" style="border-collapse:collapse;">
                     <tbody>
                        <tr>
                           <th align="left" scope="col">Emp No</th>
                           <th align="left" scope="col">Employee</th>
                           <th align="left" scope="col">Leave Type</th>
                           <th align="left" scope="col">From</th>
                           <th align="left" scope="col">To</th>
                           <th align="left" scope="col">Total Days</th>
                           <th align="left" scope="col">Date Approved</th>
                           <th align="left" scope="col">Approved By</th>
                        </tr>
                        @foreach($mainData as $value)
                        <tr>
                           <td align="left" scope="col">{{$value->EmpDataGet2 ? $value->EmpDataGet2->staff_number : 'NA'}}</td>
                           <td align="left" scope="col">{{$value->EmpDataGet2 ? $value->EmpDataGet2->first_name : 'NA'}} {{$value->EmpDataGet2 ? $value->EmpDataGet2->middle_name : 'NA'}} {{$value->EmpDataGet2 ? $value->EmpDataGet2->last_name : 'NA'}} </td>
                           <td align="left" scope="col">{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : 'NA'}}</td>
                           <td align="left" scope="col">{{$value->from}}</td>
                           <td align="left" scope="col">{{$value->to}}</td>
                           <td align="left" scope="col">{{$value->day_taken}}</td>
                           <td align="left" scope="col">{{$value->day_taken}}</td>
                           <td align="left" scope="col">{{$value->UData ? $value->UData->name : 'NA'}}</td>
                        </tr>@endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <div id="Paris" class="tabcontent" style="{{ Request::get('Types') == 'LeavesPending' ? 'display: block;' : ''}}">
               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-12">
                     <div id="MainContent_UpdatePanel2">
                        <div>
                           <center>
                              <span id="MainContent_Label13" class="label label-warning" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Leaves Pending at Manager Level</span>
                           </center>
                           <div>
                              <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_GridView4" style="border-collapse:collapse;">
                                 <tbody>
                                    <tr>
                                       <th align="left" scope="col">Emp No</th>
                                       <th align="left" scope="col">Employee</th>
                                       <th align="left" scope="col">Leave Type</th>
                                       <th align="left" scope="col">From</th>
                                       <th align="left" scope="col">To</th>
                                       <th align="left" scope="col">Total Days</th>
                                       <th align="left" scope="col">Approved Days</th>
                                       <th align="left" scope="col">Date Approved</th>
                                       <th align="left" scope="col">Approved By</th>
                                    </tr>
                                    @foreach($pendingRequestManager as $val)
                                    <tr>
                                       <td align="left">{{$val->EmpDataGet2 ? $val->EmpDataGet2->staff_number : ''}}</td>
                                       <td align="left">{{$val->EmpDataGet2 ? $val->EmpDataGet2->first_name : ''}} {{$val->EmpDataGet2 ? $val->EmpDataGet2->middle_name : ''}} {{$val->EmpDataGet2 ? $val->EmpDataGet2->last_name : ''}}</td>
                                       <td>{{$val->LeaveDataGet2 ? $val->LeaveDataGet2->leave_type : ''}}</td>
                                       <td>{{$val->from}}</td>
                                       <td>{{$val->to}}</td>
                                       <td>{{$val->day_taken}}</td>
                                       <td>{{$val->total_days ? : 'NA'}}</td>
                                       <td>{{$val->manage_approve_date ? : 'NA'}}</td>
                                       <td>{{$val->UMangerData  ? $val->UMangerData->name : 'NA'}}</td>
                                       <td>&nbsp;</td>
                                    </tr>@endforeach
                                 </tbody>
                              </table>
                           </div>
                           <hr>
                        </div>
                        <center>
                           <span id="MainContent_Label3" class="label label-warning" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Leave Pending at HR Level</span>
                        </center>
                        <div>
                           <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_GridView1" style="border-collapse:collapse;">
                              <tbody>
                                 <tr>
                                    <th align="left" scope="col">Emp No</th>
                                    <th align="left" scope="col">Employee</th>
                                    <th align="left" scope="col">Leave Type</th>
                                    <th align="left" scope="col">From</th>
                                    <th align="left" scope="col">To</th>
                                    <th align="left" scope="col">Total Days</th>
                                    <th align="left" scope="col">Approved Days</th>
                                    <th align="left" scope="col">Date Approved</th>
                                    <th align="left" scope="col">Approved By</th>
                                 </tr>
                                 @foreach($pendingRequestHr as $value)
                                <tr>
                                       <td align="left">{{$value->EmpDataGet2 ? $value->EmpDataGet2->staff_number : ''}}</td>
                                       <td align="left">{{$value->EmpDataGet2 ? $value->EmpDataGet2->first_name : ''}} {{$value->EmpDataGet2 ? $value->EmpDataGet2->middle_name : ''}} {{$value->EmpDataGet2 ? $value->EmpDataGet2->last_name : ''}}</td>
                                       <td>{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : ''}}</td>
                                       <td>{{$value->from}}</td>
                                       <td>{{$value->to}}</td>
                                       <td>{{$value->day_taken}}</td>
                                       <td>{{$value->total_days ? : 'NA'}}</td>
                                       <td>{{$value->date_approved ? : 'NA'}}</td>
                                       <td>{{$value->UData  ? $value->UData->name : 'NA'}}</td>
                                       <td>&nbsp;</td>
                                    </tr>@endforeach
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="Imran" class="tabcontent" 
             style="{{ Request::get('Types') == 'ScheduledLeaves' ? 'display: block;' : ''}}">
                 <div class="row" style="margin-top: 15px;">
                  <form class="validate form-horizontal"  role="form" method="get"  enctype = "multipart/form-data">
                     {{ csrf_field() }}
                     <div class="col-lg-4">
                        <div class="form-group">
                           <input type="hidden" name="Types2" value="ScheduledLeaves">
                           <input type="hidden" name="Types" value="ScheduledLeaves">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">Leave Type</label>
                           <div class="col-lg-9">
                              {!!Form::select('leave_id', $leaveData, null, ['placeholder'=>'Select Leave Type', 'class' => 'form-control','required'=>true,'title'=>'Please Leave Type'  ])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">From Date</label>
                           <div class="col-lg-9">
                              {!!Form::text('from', null, ['placeholder'=>'Select From', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">To Date</label>
                             <div class="col-lg-9">
                              {!!Form::text('to', null, ['placeholder'=>'Select To', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                       <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-4">
                     </div>
                     <div class="col-md-3">
                     </div>
                     <div class="col-md-5">
                        <div class="form-group">
                           <input type="submit" value="Preview" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;">
                            <a href="{{route('LeaveStatus.PdfDownloadScheduledLeaves')}}?leave_id={{Request::get('leave_id')}}&to={{Request::get('to')}}&from={{Request::get('from')}}&Types2=ScheduledLeaves">
                           <input type="button" value="Export to Pdf" class="btn btn-info btn-sm"></a>
                        <!--    <input type="submit" value="Export to Excel" class="btn btn-sm" style="background-color: #5cb85c !important; color: #fff !important; border-color: #4cae4c !important;">
                           <input type="submit" value="Export to Word" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;"> -->
                        </div>
                     </div>
                  </div>
               </div>
                  </form>
               </div>
               <center>
                  <span id="MainContent_Label2" class="label label-success" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Employees On Leave</span>
               </center>
               <div>
                  <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridemp" style="border-collapse:collapse;">
                     <tbody>
                        <tr>
                           <th align="left" scope="col">Emp No</th>
                           <th align="left" scope="col">Employee</th>
                           <th align="left" scope="col">Leave Type</th>
                           <th align="left" scope="col">From</th>
                           <th align="left" scope="col">To</th>
                           <th align="left" scope="col">Total Days</th>
                           <th align="left" scope="col">Date Approved</th>
                           <th align="left" scope="col">Approved By</th>
                        </tr>
                        @foreach($scheduledLeavesDataMainData as $value2)
                        <tr>
                           <td align="left" scope="col">{{$value2->EmpDataGet2 ? $value2->EmpDataGet2->staff_number : 'NA'}}</td>
                           <td align="left" scope="col">{{$value2->EmpDataGet2 ? $value2->EmpDataGet2->first_name : 'NA'}} {{$value2->EmpDataGet2 ? $value2->EmpDataGet2->middle_name : 'NA'}} {{$value2->EmpDataGet2 ? $value2->EmpDataGet2->last_name : 'NA'}} </td>
                           <td align="left" scope="col">{{$value2->LeaveDataGet2 ? $value2->LeaveDataGet2->leave_type : 'NA'}}</td>
                           <td align="left" scope="col">{{$value2->from}}</td>
                           <td align="left" scope="col">{{$value2->to}}</td>
                           <td align="left" scope="col">{{$value2->day_taken}}</td>
                           <td align="left" scope="col">{{$value2->day_taken}}</td>
                           <td align="left" scope="col">{{$value2->UData ? $value2->UData->name : 'NA'}}</td>
                        </tr>@endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <div id="Completed" class="tabcontent" 
            style="{{ Request::get('Types') == 'CompletedLeaves' ? 'display: block;' : ''}}">  <div class="row" style="margin-top: 15px;">
                  <form class="validate form-horizontal"  role="form" method="get"  enctype = "multipart/form-data">
                     {{ csrf_field() }}
                     <div class="col-lg-4">
                        <div class="form-group">
                           <input type="hidden" name="Types2" value="CompletedLeaves">
                           <input type="hidden" name="Types" value="CompletedLeaves">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">Leave Type</label>
                           <div class="col-lg-9">
                              {!!Form::select('leave_id', $leaveData, null, ['placeholder'=>'Select Leave Type', 'class' => 'form-control','required'=>true,'title'=>'Please Leave Type'  ])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">From Date</label>
                           <div class="col-lg-9">
                              {!!Form::text('from', null, ['placeholder'=>'Select From', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">To Date</label>
                             <div class="col-lg-9">
                              {!!Form::text('to', null, ['placeholder'=>'Select To', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                       <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-4">
                     </div>
                     <div class="col-md-3">
                     </div>
                     <div class="col-md-5">
                        <div class="form-group">
                           <input type="submit" value="Preview" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;">
                            <a href="{{route('LeaveStatus.PdfDownloadCompletedLeaves')}}?leave_id={{Request::get('leave_id')}}&to={{Request::get('to')}}&from={{Request::get('from')}}&Types2=CompletedLeaves">
                           <input type="button" value="Export to Pdf" class="btn btn-info btn-sm"></a>
                        <!--    <input type="submit" value="Export to Excel" class="btn btn-sm" style="background-color: #5cb85c !important; color: #fff !important; border-color: #4cae4c !important;">
                           <input type="submit" value="Export to Word" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;"> -->
                        </div>
                     </div>
                  </div>
               </div>
                  </form>
               </div>
               <center>
                  <span id="MainContent_Label2" class="label label-success" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Employees On Leave</span>
               </center>
               <div>
                  <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridemp" style="border-collapse:collapse;">
                     <tbody>
                        <tr>
                           <th align="left" scope="col">Emp No</th>
                           <th align="left" scope="col">Employee</th>
                           <th align="left" scope="col">Leave Type</th>
                           <th align="left" scope="col">From</th>
                           <th align="left" scope="col">To</th>
                           <th align="left" scope="col">Total Days</th>
                           <th align="left" scope="col">Date Approved</th>
                           <th align="left" scope="col">Approved By</th>
                        </tr>
                        @foreach($completedLeavesData2 as $value3)
                        <tr>
                           <td align="left" scope="col">{{$value3->EmpDataGet2 ? $value3->EmpDataGet2->staff_number : 'NA'}}</td>
                           <td align="left" scope="col">{{$value3->EmpDataGet2 ? $value3->EmpDataGet2->first_name : 'NA'}} {{$value3->EmpDataGet2 ? $value3->EmpDataGet2->middle_name : 'NA'}} {{$value3->EmpDataGet2 ? $value3->EmpDataGet2->last_name : 'NA'}} </td>
                           <td align="left" scope="col">{{$value3->LeaveDataGet2 ? $value3->LeaveDataGet2->leave_type : 'NA'}}</td>
                           <td align="left" scope="col">{{$value3->from}}</td>
                           <td align="left" scope="col">{{$value3->to}}</td>
                           <td align="left" scope="col">{{$value3->day_taken}}</td>
                           <td align="left" scope="col">{{$value3->day_taken}}</td>
                           <td align="left" scope="col">{{$value3->UData ? $value3->UData->name : 'NA'}}</td>
                        </tr>@endforeach
                     </tbody>
                  </table>
               </div>
               
            </div>
            <div id="Rejected" class="tabcontent" style="{{ Request::get('Types') == 'DeclinedLeaves' ? 'display: block;' : ''}}">
               <div class="row" style="margin-top: 15px;">
                  <form class="validate form-horizontal"  role="form" method="get"  enctype = "multipart/form-data">
                     {{ csrf_field() }}
                     <div class="col-lg-4">
                        <div class="form-group">
                           <input type="hidden" name="Types2" value="DeclinedLeaves">
                           <input type="hidden" name="Types" value="DeclinedLeaves">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">Leave Type</label>
                           <div class="col-lg-9">
                              {!!Form::select('leave_id', $leaveData, null, ['placeholder'=>'Select Leave Type', 'class' => 'form-control','required'=>true,'title'=>'Please Leave Type'  ])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">From Date</label>
                           <div class="col-lg-9">
                              {!!Form::text('from', null, ['placeholder'=>'Select From', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-lg-3 control-label"  style="margin-top: 5px;">To Date</label>
                             <div class="col-lg-9">
                              {!!Form::text('to', null, ['placeholder'=>'Select To', 'class' => 'form-control datepicker','required'=>true])!!}  
                           </div>
                        </div>
                     </div>
                       <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-4">
                     </div>
                     <div class="col-md-3">
                     </div>
                     <div class="col-md-5">
                        <div class="form-group">
                           <input type="submit" value="Preview" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;">
                            <a href="{{route('LeaveStatus.PdfDownloadDeclinedLeaves')}}?leave_id={{Request::get('leave_id')}}&to={{Request::get('to')}}&from={{Request::get('from')}}&Types2=DeclinedLeaves">
                           <input type="button" value="Export to Pdf" class="btn btn-info btn-sm"></a>
                          <!--  <input type="submit" value="Export to Excel" class="btn btn-sm" style="background-color: #5cb85c !important; color: #fff !important; border-color: #4cae4c !important;">
                           <input type="submit" value="Export to Word" class="btn btn-sm" style="background-color: #337ab7 !important; border: 1px solid gainsboro !important; color: #fff !important;"> -->
                        </div>
                     </div>
                  </div>
               </div>
                  </form>
               </div>
               <center>
                  <span id="MainContent_Label2" class="label label-success" style="display:inline-block;color:White;font-family:Franklin Gothic Book;font-size:Medium;width:100%;">Declined Leaves</span>
               </center>
               <div>
                  <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridemp" style="border-collapse:collapse;">
                     <tbody>
                        <tr>
                           <th align="left" scope="col">Emp No</th>
                           <th align="left" scope="col">Employee</th>
                           <th align="left" scope="col">Leave Type</th>
                           <th align="left" scope="col">From</th>
                           <th align="left" scope="col">To</th>
                           <th align="left" scope="col">Total Days</th>
                           <th align="left" scope="col">Date Approved</th>
                           <th align="left" scope="col">Approved By</th>
                        </tr>
                        @foreach($declinedLeavesData2 as $value4)
                        <tr>
                           <td align="left" scope="col">{{$value4->EmpDataGet2 ? $value4->EmpDataGet2->staff_number : 'NA'}}</td>
                           <td align="left" scope="col">{{$value4->EmpDataGet2 ? $value4->EmpDataGet2->first_name : 'NA'}} {{$value4->EmpDataGet2 ? $value4->EmpDataGet2->middle_name : 'NA'}} {{$value4->EmpDataGet2 ? $value4->EmpDataGet2->last_name : 'NA'}} </td>
                           <td align="left" scope="col">{{$value4->LeaveDataGet2 ? $value4->LeaveDataGet2->leave_type : 'NA'}}</td>
                           <td align="left" scope="col">{{$value4->from}}</td>
                           <td align="left" scope="col">{{$value4->to}}</td>
                           <td align="left" scope="col">{{$value4->day_taken}}</td>
                           <td align="left" scope="col">{{$value4->day_taken}}</td>
                           <td align="left" scope="col">{{$value4->UData ? $value4->UData->name : 'NA'}}</td>
                        </tr>@endforeach
                     </tbody>
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