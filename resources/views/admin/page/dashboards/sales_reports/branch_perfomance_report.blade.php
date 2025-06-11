<div class="col-md-8 dashboard-card">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 style="font-weight: bolder">Branch Performance</h4>
        </div>
        <div class="col-md-6 col-md-offset-6 text-right">
            <a href="{{ route('hq-dashboard.order-taking-summary') }}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i>
                Branch Route Performance for {{ date('F') }}
            </a>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 10px !important;">
        <table class="table table-bordered table-hover" id="create_datatable">
            <thead>
            <tr>
                <th>#</th>
                <th>Branch</th>
                <th>Visited Routes</th>
                <th>Centers</th>
                <th>Customers</th>
                <th>Tonnage</th>
                <th>Route Sales</th>
                <th>Pos Sales</th>
                <th>Total Sales</th>
            </tr>
            </thead>
            <tbody>
            @php
                $visited_routes = $total_centers = $total_branch_customers = $total_tonnage = $total_route_sales = $total_pos_sales = 0;
            @endphp
            @foreach ($data as $branch)
                <tr>
                    <td>{{$loop->index+1}}</td>
                    <td>{{$branch->name}}</td>
                    <td class="qty">{{$branch->routes_with_orders ?? 0}}</td>
                    <td class="qty">{{$branch->centers ?? 0}}</td>
                    <td class="qty">{{$branch->branch_customers ?? 0}}</td>
                    <td class="qty">{{manageAmountFormat($branch->tonnage)}}</td>
                    <td class="amount">{{manageAmountFormat($branch->route_sales)}}</td>
                    <td class="amount">{{manageAmountFormat($branch->pos_sales)}}</td>
                    <td class="amount">{{ manageAmountFormat($branch->pos_sales + $branch->route_sales) }}</td>
                </tr>
                @php
                    $visited_routes += $branch->routes_with_orders;
                    $total_centers += $branch->centers;
                    $total_branch_customers += $branch->branch_customers;
                    $total_tonnage += $branch->tonnage;
                    $total_route_sales += $branch->route_sales;
                    $total_pos_sales += $branch->pos_sales;
                @endphp

            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="2">Totals</th>
                <th class="qty">{{$visited_routes}} </th>
                <th class="qty">{{$total_centers}}</th>
                <th class="qty"> {{$total_branch_customers}} </th>
                <th class="qty">{{manageAmountFormat($total_tonnage)}}</th>
                <th class="amount">{{manageAmountFormat($total_route_sales)}}</th>
                <th class="amount">{{manageAmountFormat($total_pos_sales)}}</th>
                <th class="amount">{{manageAmountFormat($total_route_sales + $total_pos_sales)}}</th>

            </tr>
            </tfoot>
        </table>
    </div>
</div>
