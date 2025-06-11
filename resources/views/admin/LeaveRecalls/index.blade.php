
@extends('layouts.admin.admin')

@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="card-header" style="padding: 10px;">
                            <i class="fa fa-filter"></i> Filter
                            </div>
                        <div class="box-header with-border no-padding-h-b">
                            <div align = "right">
                            <table class="table table-striped table-bordered nowrap" cellspacing="0" rules="rows" border="1" id="MainContent_gridapproved" style="border-collapse:collapse;">
           <tbody>
            <tr>
            <th align="left" scope="col">Emp No</th>
            <th align="left" scope="col">Employee</th>
            <th align="left" scope="col">Leave Type</th>
            <th align="left" scope="col">From</th>
            <th align="left" scope="col">To</th>
            <th align="left" scope="col">Total Days</th>
            <th align="left" scope="col">#</th>
        </tr>
        @foreach($leaveAssignData as $val)
        <tr>
            <td align="left">{{$val->EmpDataGet2 ? $val->EmpDataGet2->staff_number : 'NA'}}</td>
            <td align="left">{{$val->EmpDataGet2 ? $val->EmpDataGet2->first_name : 'NA'}} {{$val->EmpDataGet2 ? $val->EmpDataGet2->middle_name : 'NA'}} {{$val->EmpDataGet2 ? $val->EmpDataGet2->last_name : 'NA'}}</td>
            <td>{{$val->LeaveDataGet2 ? $val->LeaveDataGet2->leave_type : 'NA'}}</td>
            <td>{{$val->from}}</td>
            <td>{{$val->to}}</td>
            <td>{{$val->day_taken}}</td>
            <td><a class="btn btn-primary btn-xs" href="{{route('LeaveRecalls.manage',['id'=>$val->id])}}">Recall</a></td>
       </tr>@endforeach
    </tbody>
</table>
    </div>
</div>
</section>
    @endsection
    @section('uniquepagescript')

@endsection
