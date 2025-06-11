@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
            <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Route Returns Summary Report </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'route-returns-summary-report', 'method' => 'get']) !!}
                <div class="row">
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
                    {{-- <div class="col-md-2 form-group">
                        <select name="route" id="route" class="mlselect">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ $route->id == request()->route ? 'selected' : '' }}>{{$route->route_name}}</option>

                            @endforeach
                        </select>

                    </div> --}}

                    <div class="col-md-3 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <input type="submit" class = "btn btn-success" name="type" value="Excel">
                        <input type="submit" class = "btn btn-success" name="type" value="PDF">

                        <a class="btn btn-success ml-12" href="{!! route('route-returns-summary-report') !!}">Clear </a>
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
                            <th>Route</th>
                            <th>Salesman</th>
                            <th>No. Of Returns</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalReturns = 0;
                                $countTotal = 0;
                            @endphp
                            @foreach ($returns as $return)
                            <tr>
                                <th>{{$loop->index + 1}}</th>
                                <td>{{$return->route_name}}</td>
                                <td>{{$return->salesman}}</td>
                                <td>{{$return->return_count}}</td>
                                <td style="text-align: right;">{{number_format($return->total_returns, 2)}}</td>


                            </tr>
                            @php
                                $totalReturns += $return->total_returns;
                                $countTotal += $return->return_count;
                            @endphp
                            @endforeach
                            

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th>{{$countTotal}}</th>
                                <th style="text-align: right;">{{number_format($totalReturns, 2)}}</th>
                            </tr>
                           
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

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('.download-link').on('click', function (event) {
                event.preventDefault();
                var shiftId = $(this).data('shift-id');
                $('#confirmDownloadBtn').attr('href', "{{ url('admin/salesman-shifts') }}/" + shiftId + "/loading-sheet");
                $('#confirmDownloadModal').modal('show');
            });

            //close modal
            $('#confirmDownloadBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmDownloadModal').modal('hide');
            });

            //shift reopen
            $('.shift-reopen').on('click', function (event) {
                event.preventDefault();
                var shiftId = $(this).data('shift-id');
                $('#confirmShiftReopenBtn').attr('href', "{{ url('admin/salesman-shifts') }}/" + shiftId + "/reopen-from-back-end");
                $('#confirmShiftReopenModal').modal('show');
            });

            //close modal
            $('#confirmShiftReopenBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmShiftReopenModal').modal('hide');

            });
        });
    </script>
@endsection
