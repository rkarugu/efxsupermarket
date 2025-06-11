@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Order Taking and POS Sales Summary</h3>
                    <div>
                        <a href="{{route('hq-dashboard.index')}}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['route' => 'hq-dashboard.order-taking-summary', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-2 form-group">
                        <input type="date" name="from" id="from" class="form-control" value="{{ request()->get('from') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="date" name="to" id="to" class="form-control" value="{{ request()->get('to') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                        <button type="submit" class="btn btn-success" name="download" value="download"><i class="fas fa-download"></i> Download</button>
                        <a class="btn btn-success" href="{!! route('hq-dashboard.order-taking-summary') !!}"><i class="fas fa-eraser"></i> Clear </a>
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
                            <th>Branch</th>
                            <th>Visited Routes</th>
                            <th>Centers</th>
                            <th>Customers</th>
                            <th>Pos Customers</th>
                            <th>Tonnage</th>
                            <th class="amount">Route Sales</th>
                            <th class="amount">Pos Sales</th>
                            <th class="amount">Total Sales</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                               $visited_routes =  $total_expected_routes = $total_centers = $total_visited_customers =$total_branch_customers = $total_pos_customers = $total_tonnage = $total_route_sales = $total_pos_sales = 0;
                               $branchNames = [];
                               $totalSales = [];
                            @endphp
                            @foreach ($data as $branch)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$branch->name}}</td>
                                <td class="qty">{{($branch->routes_with_orders ?? 0)  .' / '. ($branch->expected_routes ?? 0)}}</td>
                                <td class="qty">{{($branch->visited_centres ?? 0) . ' / '.($branch->centers ?? 0)}}</td>
                                <td class="qty">{{($branch->visited_customers ?? 0) . ' / '.($branch->branch_customers ?? 0)}}</td>
                                <td class="qty">{{($branch->pos_customers ?? 0)}}</td>
                                <td class="qty">{{manageAmountFormat($branch->tonnage) . ' / ' . ($branch->expected_tonnage ?? 0)}}</td>
                                <td class="amount">{{manageAmountFormat($branch->route_sales)}}</td>
                                <td class="amount">{{manageAmountFormat($branch->pos_sales)}}</td>
                                <td class="amount">{{ manageAmountFormat($branch->pos_sales + $branch->route_sales) }}</td>
                            </tr>
                            @php
                                $visited_routes += $branch->routes_with_orders;
                                $total_expected_routes += $branch->expected_routes;
                                $total_centers += $branch->centers;
                                $total_visited_customers += $branch->visited_customers;
                                $total_branch_customers += $branch->branch_customers;
                                $total_pos_customers += $branch->pos_customers;
                                $total_tonnage += $branch->tonnage;
                                $total_route_sales += $branch->route_sales;
                                $total_pos_sales += $branch->pos_sales;
                                 // Prepare data for the chart
                                 $branchNames[] = $branch->name;
                                $totalSales[] = $branch->pos_sales + $branch->route_sales;
                            @endphp
                                
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Totals</th>
                                <th class="qty">{{$visited_routes . ' / ' . $total_expected_routes}} </th>
                                <th class="qty">{{$total_centers}}</th>
                                <th class="qty"> {{ ($total_visited_customers). ' / ' . ($total_branch_customers)}} </th>
                                <th class="qty"> {{ $total_pos_customers}} </th>
                                <th class="qty">{{manageAmountFormat($total_tonnage)}}</th>
                                <th class="amount">{{manageAmountFormat($total_route_sales)}}</th>
                                <th class="amount">{{manageAmountFormat($total_pos_sales)}}</th>
                                <th class="amount">{{manageAmountFormat($total_route_sales + $total_pos_sales)}}</th>

                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-md-12">
                    <hr>
                    <canvas id="salesBarChart"></canvas>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .amount{
            text-align: right;
        }
        .qty{
            text-align: center;
        }
        #salesBarChart{
            max-height: 400px;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
            $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        });
    </script>
    <script>
     
        var branchNames = @json($branchNames);
        var totalSales = @json($totalSales);

        var ctx = document.getElementById('salesBarChart').getContext('2d');
        var salesBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: branchNames, 
                datasets: [{
                    label: 'Total Sales',
                    data: totalSales, 
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Sales'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    
    </script>

@endsection
