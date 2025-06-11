@extends('layouts.admin.admin')

@section('content')
            <!-- Main content -->
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Onsite Vs Offsite Shifts </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'onsite-vs-offsite-shifts-report', 'method' => 'get']) !!}
                <div class="row">
                    {{-- filter by  branch --}}
                    {{-- @if ($logged_user_info->role_id == 1 ||  $logged_user_info->role_id == 147)

                        <div class="col-md-3 form-group">
                            <select name="branch" id="branch" class="mlselect">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ $branch->id == request()->branch ? 'selected' : '' }}>{{$branch->name}}</option>

                                @endforeach
                            </select>

                        </div>
                    @endif --}}
                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <select name="route" id="route" class="mlselect">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ $route->id == request()->route ? 'selected' : '' }}>{{$route->route_name}}</option>

                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <input type="submit" class="btn btn-success" name="type" value="Download">
                        <a class="btn btn-success ml-12" href="{!! route('onsite-vs-offsite-shifts-report') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            {{-- <th>Date</th> --}}
                            <th>Route</th>
                            <th>Salesman</th>
                            <th>Onsite Start</th>
                            <th>Onsite End</th>
                            <th>Onsite Duration</th>
                            <th>Onsite Customers Served</th>
                            <th>Offsite Start</th>
                            <th>Offsite End</th>
                            <th>Offsite Duration</th>
                            <th>Offsite Customers Served</th>
                            <th>Met with No Orders</th>
                            <th>Totally Unmet</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($shifts as $shift)
                            <tr>
                                <th>{{$loop->index + 1}}</th>
                                <td>{{$shift['route']}}</td>
                                <td>{{$shift['salesman']}}</td>
                                <td>{{$shift['onsite_start']}}</td>
                                <td>{{$shift['onsite_end']}}</td>
                                <td>{{$shift['onsite_duration']}}</td>
                                <td>{{$shift['onsite_customers_served']}}</td>
                                <td>{{$shift['offsite_start']}}</td>
                                <td>{{$shift['offsite_end']}}</td>
                                <td>{{$shift['offsite_duration']}}</td>
                                <td>{{$shift['offsite_customers_served']}}</td>
                                <td>{{$shift['met_with_no_orders']}}</td>
                                <td>{{$shift['totally_unmet']}}</td>
    
                            </tr>
                                
                            @endforeach
                     
                        </tbody>
                        <tfoot>
                    
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
            $(".mlselect").select2();
            $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
        });
     
    </script>

@endsection
