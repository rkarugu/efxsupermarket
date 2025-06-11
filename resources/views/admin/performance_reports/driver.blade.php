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
                    <h3 class="box-title">Driver Performance Report</h3>
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('driver-performance-report') }}" method="GET">
                        <div class="row">
                            
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t" >
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{request()->branch ==  $branch->id ? 'selected' : ''}}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-md-3 form-group">
                                <label for="">Select Month</label>
                                <input type="month" name="month" id="month" class="form-control" value="{{request()->month ? request()->month : \Carbon\Carbon::now()->subMonth()->format('Y-m') }}" max="{{\Carbon\Carbon::now()->subMonth()->format('Y-m')}}">
                            </div> --}}
                            <div class="col-md-3 form-group">
                                <label for="">From</label>
                                <input type="date" name="start" id="start" class="form-control" value="{{request()->start ? request()->start : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">To</label>
                                <input type="date" name="end" id="end" class="form-control" value="{{request()->end ? request()->end : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                                <button type="submit" name="intent" value="Excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                                <a href="{{route('driver-performance-report')}}" class="btn btn-success"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    @if (isset($excelData))
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>DRIVER</th>
                                    <th>TOTAL SHIFTS</th>
                                    <th>SHIFTS STARTED ON TIME</th>
                                    <th>START SHIFT REWARD</th>
                                    <th>SHIFTS ENDED ON TIME</th>
                                    <th>END SHIFT REWARD</th>
                                    <th>TOTAL DISPATCHES</th>
                                    <th>STORES LOADED NEXT DAY</th>
                                    <th>LOADING REWARD</th>
                                    <th>ACTUAL DELIVERIES</th>
                                    <th>SYSTEM DELIVERIES</th>
                                    <th>SYSTEM USAGE REWARD</th>
                                    <th>TOTAL FUEL ENTRIES</th>
                                    <th>ENTRIES BELOW EXPECTED</th>
                                    <th>ENTRIES WITHIN EXPECTED</th>
                                    <th>FUEL REWARD</th>
                                    <th>EXPECTED REWARDS</th>
                                    <th>EARNED REWARD</th>
                                    <th>TURN BOY</th>
                                    <th>TURN BOY REWARD</th>
                        
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalExpectedRewards = $totalEarnedRewards = 0;
                                @endphp
                                @foreach ($excelData as $data)
                                <tr  class="shift-row" data-user-id="{{ $data['user_id']}}" data-start-date="{{request()->start ? request()->start : \Carbon\Carbon::now()->toDateString() }}" data-end-date="{{request()->end ? request()->end : \Carbon\Carbon::now()->toDateString() }}">

                                        <th><i class="fa fa-plus-circle toggle-details" style="cursor: pointer; font-size: 16px;"></i></th>
                                        <td>{{$data['driver']}}</td>
                                        <td class="sub-table-qty">{{$data['total_shifts']}}</td>
                                        <td class="sub-table-qty">{{$data['shifts_started_on_time']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['start_shift_reward']> 0]) class="amount">{{$data['start_shift_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['end_shifts_on_time']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['end_shift_reward']> 0]) class="amount">{{$data['end_shift_reward']}}</td>
                                        <td class="sub-table-qty">
                                            <a target="_blank" href="{{route('driver-performance.driver-dispatch-details', ['userId' => $data['user_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()])}}">
                                                {{$data['total_dispatches']}}
                                            </a>
                                        </td>
                                        <td class="sub-table-qty">
                                            <a target="_blank" href="{{route('driver-performance.driver-dispatch-details', ['userId' => $data['user_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()])}}">
                                                {{$data['store_dispatches_loaded_next_day']}}
                                            </a>
                                        </td>
                                        <td @style(['background-color:#00ff00' => $data['dispatch_reward']> 0]) class="amount">{{$data['dispatch_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['expected_deliveries']}}</td>
                                        <td class="sub-table-qty">{{$data['actual_deliveries']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['system_usage_reward']> 0]) class="amount">{{$data['system_usage_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['total_fuel_entries']}}</td>
                                        <td class="sub-table-qty">{{$data['fueled_below_expected']}}</td>
                                        <td class="sub-table-qty">{{$data['fueled_within_expected']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['fuelling_reward']> 0]) class="amount">{{$data['fuelling_reward']}}</td>
                                        <td class="amount">{{manageAmountFormat($data['expected_rewards'])}}</td>
                                        <td class="amount">{{manageAmountFormat($data['total_reward'])}}</td>
                                        <td >{{$data['turnboy']}}</td>
                                        <td class="amount">{{manageAmountFormat($data['total_reward'] / 2)}}</td>

                        
                                    </tr>
                                    @php
                                        $totalExpectedRewards +=  $data['expected_rewards'];
                                        $totalEarnedRewards += $data['total_reward'];
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="text-align: left"><strong>Total</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['total_shifts']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['shifts_started_on_time']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['start_shift_reward']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['end_shifts_on_time']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['end_shift_reward']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['total_dispatches']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['store_dispatches_loaded_next_day']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['dispatch_reward']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['expected_deliveries']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['actual_deliveries']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['system_usage_reward']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['total_fuel_entries']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['fueled_below_expected']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['fueled_within_expected']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ $totals['fuelling_reward']}}</strong></td>
                                    <td style="text-align: right"><strong>{{ manageAmountFormat($totalExpectedRewards)}}</strong></td>
                                    <td style="text-align: right"><strong>{{ manageAmountFormat($totalEarnedRewards)}}</strong></td>
                                    <td></td>
                                    <td style="text-align: right"><strong>{{ manageAmountFormat($totalEarnedRewards / 2)}}</strong></td>

                        
                                </tr>
                            </tfoot>
                        </table>
                    @endif
          
                </div>

            </div>
        </div>


    </section>
@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
        .sub-table-qty{
            text-align: center;
        }
        .amount{
            text-align: right;
        }
    </style>
    <style>
        .selected-row {
            /* background-color: red !important; */
        }
        .selected-row, .selected-inner-row {
        /* background-color: #f0f8ff !important; */
    }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       
        $(function() {
            $('body').addClass('sidebar-collapse');

            $(".mlselec6t").select2();


            $('.toggle-details').on('click', function() {
            var $row = $(this).closest('tr');
            var userId = $row.data('user-id');
            var start = $row.data('start-date');
            var end = $row.data('end-date');
            var $icon = $(this);
            var url = '{{ route("driver-performance-shift-details", [":userId", ":start", ":end"]) }}';
            url = url.replace(':userId', userId).replace(':start', start).replace(':end', end);

            $icon.toggleClass('fa-plus-circle fa-minus-circle');

            $('.shift-row').removeClass('selected-row'); 
            $row.addClass('selected-row');

            if ($row.next('.shifts-details').length > 0) {
                $row.next('.shifts-details').toggle();
                return;
            }

            var loadingRow = '<tr class="loading-row"><td colspan="27" class="text-center">Loading...</td></tr>';
            $row.after(loadingRow);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    var detailsRow = '<tr class="shifts-details"><td colspan="19"><table class="table table-bordered" width="100%"><thead><tr><th>#</th><th>Delivery Date</th><th>Route</th><th>Start Time</th><th>End Time</th><th>Total Store Dispatches</th><th>Dispatches loaded next day</th><th>Total Customers</th><th>Customers With Orders</th><th>Actual Deliveries</th><th>System Deliveries</th><th>Expected Fuel</th><th>Actual Fuel</th><th>Action</th></tr></thead><tbody>';
                    var counter = 1;
                    var totalStoreDispatches = 0;
                    var totalLateDispatches = 0;
                    var totalCustomers = 0;
                    var totalCustomersWithOrders = 0;
                    var totalExpectedDeliveries = 0;
                    var totalActualDeliveries = 0;
                    var totalManualFuelEstimate = 0;
                    var totalActualFuel = 0;

                    data.forEach(function(item) {
                        totalStoreDispatches += item.total_store_dispatches;
                        totalLateDispatches += item.shifts_dispatched_next_day;
                        totalCustomers += item.customers;
                        totalCustomersWithOrders += item.customers_with_orders;
                        totalExpectedDeliveries += item.expected_deliveries;
                        totalActualDeliveries += item.actual_deliveries;
                        totalManualFuelEstimate += item.manual_fuel_estimate;
                        totalActualFuel += item.actual_fuel_quantity;

                   
                        detailsRow += '<tr class="inner-row"><th>' + counter + '</th><td>' + item.delivery_date + '</td><td>' + item.route_name + '</td><td >' + item.start_time + '</td><td>'
                             + item.finish_time + '</td><td class="sub-table-qty">' + item.total_store_dispatches +'</td><td class="sub-table-qty">' + item.shifts_dispatched_next_day +'('+ item.dispatch_percentage.toFixed(2) +'%)' +'</td><td class="sub-table-qty">' + item.customers +'</td><td class="sub-table-qty">' + item.customers_with_orders +'</td><td class="sub-table-qty">' + item.expected_deliveries +'</td><td class="sub-table-qty">'
                                 + item.actual_deliveries +'('+ item.system_usage_percentage.toFixed(2) +'%)' + '</td><td class="sub-table-qty">' + item.manual_fuel_estimate +'</td><td class="sub-table-qty">' + item.actual_fuel_quantity +'</td><td class="sub-table-amounts"><a href="' + '{{ route('delivery-schedules.show', ['delivery_schedule' => '__delivery_schedule__']) }}'.replace('__delivery_schedule__', item.schedule_id) + '" target="_blank"><i class="fas fa-eye"></i></a></td></tr>';
                        counter++;
                    });

                    detailsRow += '</tbody><tfoot><tr><th colspan="5">Total</th><th class="sub-table-qty">'+ totalStoreDispatches +'</th><th class="sub-table-qty">'+ totalLateDispatches +'</th><th class="sub-table-qty">'+ totalCustomers +'</th><th class="sub-table-qty">'+ totalCustomersWithOrders +'</th><th class="sub-table-qty">'+ totalExpectedDeliveries
                        +'</th><th class="sub-table-qty">'+ totalActualDeliveries +'</th><th class="sub-table-qty">'+ totalManualFuelEstimate +'</th><th class="sub-table-qty">'+ totalActualFuel +'</th><th></th></tr></tfoot></table></td></tr>';
                    $row.after(detailsRow);
                    $row.next('.loading-row').remove();
                    // highlight clicked row
                    $('.inner-row').on('click', function() {
                        $('.inner-row').removeClass('selected-inner-row');
                        $(this).addClass('selected-inner-row');
                    });

                },
                error: function() {
                    alert('Error loading performance details.');
                    $row.next('.loading-row').remove();
                    $icon.toggleClass('fa-plus-circle fa-minus-circle');
                }
            });
        });


        });
    </script>
@endsection
