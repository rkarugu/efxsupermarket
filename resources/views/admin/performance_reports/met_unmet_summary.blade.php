@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{{ $route->route_name }} MET / UNMET SHIFTS SUMMARY </h3>
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('salesman-performance-report') }}" method="GET">
                        <div class="row">

                            <div class="col-md-3 form-group">
                                <label for="">From</label>
                                <input readonly type="date" name="start" id="start" class="form-control" value="{{request()->start ? request()->start : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">To</label>
                                <input readonly type="date" name="end" id="end" class="form-control" value="{{request()->end ? request()->end : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                {{-- <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title"></h3>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="create_datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Start</th>
                        <th>End</th>
                        <th >Total Customers</th>
                        <th>Met</th>
                        <th>Unmet</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                    $totalCustomers = $totalMetCustomers = $totalUnmetCustomers = 0 
                    @endphp
                    @foreach($data as $record)
                      <tr data-route-id="{{ $route->id}}" data-date="{{$record->date}}">
                        <th><i class="fa fa-plus-circle toggle-details" style="cursor: pointer; font-size: 16px;"></i></th>
                        <td>{{$record->date}}</td>
                        <td>{{$record->start_time}}</td>
                        <td>{{$record->closing_time}}</td>
                        <td class="qty">{{$record->expected_customers}}</td>
                        <td class="qty">{{$record->met_customers . '('.$record->met_customer_percentage . '%)'}}</td>
                        <td class="qty">{{($record->expected_customers - $record->met_customers) . '('.(100 - $record->met_customer_percentage ).'%)'}}</td>
                      </tr>
                    @php
                        $totalCustomers += $record->expected_customers;
                        $totalMetCustomers += $record->met_customers;
                        $totalUnmetCustomers += ($record->expected_customers - $record->met_customers);
                    @endphp
                    @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $totalMetPercentage = ($totalMetCustomers / $totalCustomers) * 100;
                            $totalUnmetPercentage = (100 - $totalMetPercentage);
                        @endphp
                        <tr>
                            <th colspan="4">Totals</th>
                            <th class="qty">{{$totalCustomers}}</th>
                            <th class="qty">{{$totalMetCustomers .'('. manageAmountFormat($totalMetPercentage) .'%)'}}</th>
                            <th class="qty">{{$totalUnmetCustomers }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </section>
@endsection
@section('uniquepagestyle')
<style>
     .qty{
        text-align: center;
    }

</style>
   

@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.toggle-details').on('click', function() {
            var $row = $(this).closest('tr');
            var routeId = $row.data('route-id');
            var date = $row.data('date');
            var $icon = $(this);
            var url = '{{ route("salesman-performance-route-met-unmet-summary.details", [":routeId", ":date"]) }}';
            url = url.replace(':routeId', routeId).replace(':date', date);

            $icon.toggleClass('fa-plus-circle fa-minus-circle');

            if ($row.next('.shifts-details').length > 0) {
                $row.next('.shifts-details').toggle();
                return;
            }
            if ($row.next('.shifts-details2').length > 0) {
                $row.next('.shifts-details2').toggle();
                return;
            }

            var loadingRow = '<tr class="loading-row"><td colspan="27" class="text-center">Loading...</td></tr>';
            $row.after(loadingRow);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    var detailsRow = '<tr><th colspan="7">Met Customers</th></tr><tr class="shifts-details"><td colspan="7"><table class="table table-bordered" width="100%" id="create_datatable_50"><thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Shop Name</th><th>Order Taken</th></tr></thead><tbody>';
                    var unmetDetailsRow = '<tr><th colspan="7">UnMet Customers</th></tr><tr class="shifts-details2"><td colspan="7"><table class="table table-bordered" width="100%" id="create_datatable_50"><thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Shop Name</th></tr></thead><tbody>';
                    var counter = 1;
                    var unmetCounter = 1;
                    data.unmet.forEach(function(item) {
                        var order_taken = item.order_taken == 1 ? '<i class="fas fa-check" style="color:green;"></i>' : '<i class="fas fa-times" style="color:red;"></i>';
                        unmetDetailsRow += '<tr><th>' + unmetCounter + '</th><td>' + item.name + '</td><td>' + item.bussiness_name + '</td><td >' + item.phone + '</td></tr>';
                        unmetCounter++;
                    });

                    unmetDetailsRow += '</tbody></table></td></tr>';
                    $row.after(unmetDetailsRow);

                    data.met.forEach(function(item) {
                        var order_taken = item.order_taken == 1 ? '<i class="fas fa-check" style="color:green;"></i>' : '<i class="fas fa-times" style="color:red;"></i>';
                        detailsRow += '<tr><th>' + counter + '</th><td>' + item.name + '</td><td>' + item.bussiness_name + '</td><td >' + item.phone + '</td><td class="qty">'
                             + order_taken + '</td></tr>';
                        counter++;
                    });

                    detailsRow += '</tbody></table></td></tr>';
                    $row.after(detailsRow);
                   
                    $row.next('.loading-row').remove();
                },
                error: function() {
                    alert('Error loading performance details.');
                    $row.next('.loading-row').remove();
                    $icon.toggleClass('fa-plus-circle fa-minus-circle');
                }
            });
        });
          
        });
        $(function() {
            $('body').addClass('sidebar-collapse');
            $(".mlselec6t").select2();

        });
    </script>
@endsection
