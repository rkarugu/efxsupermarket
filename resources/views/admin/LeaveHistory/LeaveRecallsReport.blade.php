@extends('layouts.admin.admin')
@section('content')
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
               <h4>REPORTS LEAVES RECALLS REPORT</h4><hr>
               <form method="get">
                <div class="row">
                  <div class="col-lg-3">
                    <div class="form-group">
                    <label>Leave Type</label>
                    <select class="form-control" name="leave_type_id">
                      <option value="">--Select---</option>
                      @foreach($leaveTypeData3 as $val)
                      <option value="{{$val->id}}"{{ trim(Request::get('leave_type_id')) == $val ? 'selected="selected"' : ''}}>{{$val->leave_type}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                    <label>From</label>
                     <input type="text" name="from" value="{{Request::get('from')}}" placeholder="From" class="form-control datepicker">
                  </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                    <label>To</label>
                     <input type="text" name="to"  value="{{Request::get('to')}}"  placeholder="To" class="form-control datepicker">
                  </div>
                </div>
                <div class="col-lg-12">
                  <button class="btn btn-danger" type="submit">Filter</button>
                  @if(!empty(Request::get('from')) && Request::get('to'))
                  <a href="{{route('RecallReportPdf.Pdf')}}?leave_type_id={{Request::get('leave_type_id')}}&from={{Request::get('from')}}&to={{Request::get('to')}}"><button title="Export In PDF" type="button" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf" aria-hidden="true"></i></button></a>
                  @else()
                     <a href="{{route('RecallReportPdf.Pdf')}}?leave_type_id={{Request::get('leave_type_id')}}&from={{Request::get('from')}}&to={{Request::get('to')}}"><button disabled="disabled" title="Export In PDF" type="button" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf" aria-hidden="true"></i></button></a>
                  @endif() 
                </div>
              </div>
            </form>
               <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridapproved" style="border-collapse:collapse; margin-top: 30px;">
           <tbody>
            <tr>
            <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col">Employee</th>
            <th align="left" scope="col">Leave Type</th>
            <th align="left" scope="col">From</th>
            <th align="left" scope="col">To</th>
            <th align="left" scope="col">Total Days</th>
            <th align="left" scope="col">Recalls Date</th>
            <th align="left" scope="col">Recalls By</th>
        </tr>
       @foreach($leaveRecallsData as $value)
        <tr>
          <td>{{$value->EmpData ? $value->EmpData->staff_number : 'NA'}}</td>
          <td>{{$value->EmpData ? $value->EmpData->first_name : 'NA'}} {{$value->EmpData ? $value->EmpData->middle_name : 'NA'}} {{$value->EmpData ? $value->EmpData->last_name : 'NA'}}</td>
          <td>{{$value->LeaveDataGet2 ? $value->LeaveDataGet2->leave_type : 'NA'}}</td>
          <td>{{$value->AssignLeave ? $value->AssignLeave->from : 'NA'}}</td>
          <td>{{$value->AssignLeave ? $value->AssignLeave->to : 'NA'}}</td>
          <td>{{$value->AssignLeave ? $value->AssignLeave->day_taken : 'NA'}}</td>
          <td>{{$value->date_recalled}}</td>
          <td>{{$value->ReCallsLeave ? $value->ReCallsLeave->name : 'NA'}}</td>
        </tr>
        @endforeach
            </tbody>
</table>
      
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
