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
                    <h3 class="box-title"> Shifts Delivery  Reports</h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'dispatch-reports.shift-delivery-report', 'method' => 'get']) !!}
                <div class="row">
                    @if ($logged_user_info->role_id == 1 ||  $logged_user_info->role_id == 147)

                    <div class="col-md-3 form-group">
                        <select name="branch" id="branch" class="mlselect">
                            <option value="" selected disabled>Select branch</option>
                            @foreach ($branches as $branch)
                            <option value="{{$branch->id}}" {{ $branch->id == $selectedBranchId ? 'selected' : '' }}>{{$branch->name}}</option>
                                
                            @endforeach
                        </select>

                    </div>
                    @endif
                    <div class="col-md-3 form-group">
                        <select name="route" id="route" class="mlselect">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                            <option value="{{$route->id}}" {{ $route->id == $selectedRouteId ? 'selected' : '' }}>{{$route->route_name}}</option>
                                
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="from" id="from" class="form-control" value="{{ request()->from ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    {{-- <div class="col-md-2 form-group">
                        <input type="date" name="to" id="to" class="form-control" value="{{ request()->to ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div> --}}

                    <div class="col-md-2 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <button type="submit" class="btn btn-success" name="manage-request" value="download">Download</button>
                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')
              

                <div class="col-md-12">
                    @if(request()->route && request()->from)

                    <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>A/C Name</th>
                            <th>Location</th>
                            <th>Weight</th>
                            <th>Total</th>
                           
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalTonnage = 0;
                            $totalAmount = 0;
                                ?>
                        @foreach ($shift->actual_orders as $data)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{ $data['invoice_id'] }}</td>
                                <td>{{ $data['customer_name'] }}</td>
                                <td>{{ $data['location'] }}</td>
                                <td>{{ number_format($data['tonnage'],2) }}</td>
                                <td style="text-align:right;">{{ manageAmountFormat($data['total'])  }}</td>
                            
                            </tr>
                            <?php
                            $totalTonnage = $totalTonnage + $data['tonnage'];
                            $totalAmount = $totalAmount + $data['total'];
                                ?>
                            
                        @endforeach
                      
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total</th>
                                <th>{{ number_format($totalTonnage, 2) }}</th>
                                <th style="text-align:right;">{{ manageAmountFormat($totalAmount) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                    @else
                    <p> Select a route and a date to continue. </p>
                @endif
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



    <script type="text/javascript" class="init">
        $(document).ready(function () {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endsection
