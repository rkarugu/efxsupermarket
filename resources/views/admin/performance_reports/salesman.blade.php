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
                    <h3 class="box-title">Salesman Performance Report</h3>
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('salesman-performance-report') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t" required>
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{request()->branch ==  $branch->id ? 'selected' : ''}}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group" >
                                <label for="group" class="control-label "> Group</label>
                                    <select  id="group" name="group" class="form-control mlselec6t">
                                        <option value="">Select Option</option>
                                        <option value="A" {{  request()->group == 'A' ? 'selected':'' }}>A</option>
                                        <option value="B" {{  request()->group == 'B' ? 'selected':'' }}>B</option>
                                        <option value="C" {{  request()->group == 'C' ? 'selected':'' }}>C</option>
                                        <option value="D" {{  request()->group == 'D' ? 'selected':'' }}>D</option>
                                        <option value="E" {{  request()->group == 'E' ? 'selected':'' }}>E</option>
                                    </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="">From</label>
                                <input type="date" name="start" id="start" class="form-control" value="{{request()->start ? request()->start : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="">To</label>
                                <input type="date" name="end" id="end" class="form-control" value="{{request()->end ? request()->end : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                                <button type="submit" name="intent" value="Excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                                <a href="{{route('salesman-performance-report')}}" class="btn btn-success"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    @if (isset($excelData))
                        <table class="table table-bordered table-hover" id="sticky_header">
                            <thead class="sticky-header">
                                <tr>
                                    <th>#</th>
                                    <th>ROUTE</th>
                                    <th>SALESMAN</th>
                                    <th>GROUP</th>
                                    <th>SALES</th>
                                    <th>SHIFT TONNAGE TARGET</th>
                                    <th>EXPECTED SHIFTS</th>
                                    <th>ACTUAL SHIFTS</th>
                                    <th>EXPECTED TONNAGE</th>
                                    <th>ACHIEVED TONNAGE</th>
                                    <th>TONNAGE REWARD</th>
                                    <th>CTNS</th>
                                    <th>CTNS REWARD</th>
                                    <th>DZNS</th>
                                    <th>DZNS REWARD</th>
                                    <th>EXPECTED MET</th>
                                    <th>ACTUAL MET</th>
                                    <th>MET %</th>
                                    <th>MET REWARD</th>
                                    <th>FULLY ONSITE SHIFT</th>
                                    <th>FULLY ONSITE REWARD</th>
                                    <th>SHIFTS OPENED ON TIME</th>
                                    <th>ONTIME REWARD</th>
                                    <th>SHIFTS CLOSED ON TIME</th>
                                    <th>TIME MANAGEMENT REWARD</th>
                                    <th>RETURNS</th>
                                    <th>RETURNS REWARD</th>
                                    <th>EXPECTED REWARD</th>
                                    <th>EARNED REWARDS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                   $totalSales =$totalShiftTonnageTarget = $totalExpectedShifts = $totalActualShifts = $totalExpectedTonnage = $totalAchievedTonnage  = $totalTonnageReward
                                    = $totalCategorizedTonnageReward
                                   = $totalExpectedMet = $totalActualMet = $totalMetReward = $totalOntimeReward  = $totalOnsiteReward = $totalTimeManagementReward = $totalReturns =  $totalReturnsReward =  $totalExpectedRewards = $totalEarnedRewards = 0;
                                @endphp
                                @foreach ($excelData as $data)
                                    <tr  class="shift-row" data-route-id="{{ $data['route_id']}}" data-start-date="{{request()->start ? request()->start : \Carbon\Carbon::now()->toDateString() }}" data-end-date="{{request()->end ? request()->end : \Carbon\Carbon::now()->toDateString() }}">
                                        <th><i class="fa fa-plus-circle toggle-details" style="cursor: pointer; font-size: 16px;"></i></th>
                                        <td>{{$data['route']}}</td>
                                        <td>{{$data['salesman']}}</td>
                                        <td>{{$data['group']}}</td>
                                        <td class="amount">{{manageAmountFormat($data['sales'])}}</td>
                                        <td class="sub-table-qty">{{$data['shift_tonnage_target']}}</td>
                                        <td class="sub-table-qty">{{$data['total_shifts']}}</td>
                                        <td class="sub-table-qty">{{$data['actual_shifts']}}</td>
                                        <td class="sub-table-qty">
                                            <a target="_blank"  href="{{ route('salesman-performance-route-tonnage-details', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()]) }}">
                                                {{$data['expected_tonnage']}}
                                            </a>
                                        </td>
                                        <td class="sub-table-qty">
                                            <a target="_blank"  href="{{ route('salesman-performance-route-tonnage-details', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()]) }}">
                                                {{$data['achieved_tonnage']}}
                                            </a>

                                        </td>
                                        <td @style(['background-color:#00ff00' => $data['tonnage_reward'] > 0]) class="amount">{{manageAmountFormat($data['tonnage_reward'])}}</td>
                                        <td class="sub-table-qty">
                                            <a target="_blank" href="{{ route('salesman-performance-route-tonnage-details', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()]) }}">
                                                {{$data['ctn_tonnage']}}
                                            </a>
                                        </td>
                                        <td @style(['background-color:#00ff00' => $data['ctns_reward'] > 0]) class="amount">{{manageAmountFormat($data['ctns_reward'])}}</td>

                                        <td class="sub-table-qty">
                                            <a target="_blank"  href="{{ route('salesman-performance-route-tonnage-details', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()]) }}">
                                                {{$data['dzn_tonnage']}}
                                            </a>
                                        </td>
                                        <td @style(['background-color:#00ff00' => $data['dzns_reward'] > 0]) class="amount">{{manageAmountFormat($data['dzns_reward'])}}</td>

                                        {{-- <td class="sub-table-qty">
                                            <a target="_blank"  href="{{ route('salesman-performance-route-tonnage-details', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()]) }}">
                                                {{$data['bulk_tonnage']}}
                                            </a>
                                        </td> --}}
                                        {{-- <td @style(['background-color:#00ff00' => $data['category_tonnage_reward'] > 0]) class="amount">{{manageAmountFormat($data['category_tonnage_reward'])}}</td> --}}
                                        <td class="sub-table-qty">
                                            <a target="_blank" href="{{route('salesman-performance-route-met-unmet-summary', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()])}}">
                                                {{$data['expected_met']}}
                                            </a>
                                        </td>
                                        <td class="sub-table-qty">
                                            <a target="_blank"  href="{{route('salesman-performance-route-met-unmet-summary', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()])}}">
                                                {{$data['actual_met']}}
                                            </a>
                                        </td>
                                        <td class="sub-table-qty">
                                            <a target="_blank"  href="{{route('salesman-performance-route-met-unmet-summary', ['route_id' => $data['route_id'], 'start' => request()->start ?? \Carbon\Carbon::now()->toDateString(), 'end' => request()->end ?? \Carbon\Carbon::now()->toDateString()])}}">
                                                {{$data['met_percentage']}}
                                            </a>
                                        </td>
                                        <td @style(['background-color:#00ff00' => $data['met_reward'] > 0]) class="amount">{{$data['met_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['fully_onsite_shifts']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['fully_onsite_reward'] > 0]) class="amount">{{$data['fully_onsite_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['shifts_opened_ontime']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['shifts_opened_ontime_reward'] > 0]) class="amount">{{$data['shifts_opened_ontime_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['shifts_closed_past_time']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['time_management_reward'] > 0]) class="amount">{{$data['time_management_reward']}}</td>
                                        <td class="sub-table-qty">{{$data['returns']}}</td>
                                        <td @style(['background-color:#00ff00' => $data['returns_reward']> 0]) class="amount">{{$data['returns_reward']}}</td>
                                        <td class="amount">{{manageAmountFormat($data['expected_rewards'])}}</td>
                                        <td class="amount">{{manageAmountFormat($data['total_rewards'])}}</td>
                                    </tr>
                                    @php
                                        $totalExpectedRewards +=  $data['expected_rewards'];
                                        $totalEarnedRewards += $data['total_rewards'];
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Totals</th>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['sales'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['shift_tonnage_target'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['expected_shifts'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['actual_shifts'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['expected_tonnage'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['achieved_tonnage'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['tonnage_reward'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['ctns_tonnage'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['ctns_reward'])}}</strong></td>

                                    <td class="amount"><strong>{{ manageAmountFormat($totals['dzns_tonnage'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['dzns_reward'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['expected_met'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['actual_met'])}}</strong></td>
                                    <td></td>                                   
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['met_reward'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['fully_onsite_shifts'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['fully_onsite_reward'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['shifts_opened_ontime'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['shifts_opened_ontime_reward'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['shifts_closed_past_time'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['time_management_reward'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['returns'])}}</strong></td>
                                    <td class="amount"><strong>{{ manageAmountFormat($totals['returns_reward'])}}</strong></td>
                                    <td style="text-align: right" class="amount"><strong>{{ manageAmountFormat($totalExpectedRewards)}}</strong></td>
                                    <td style="text-align: right" class="amount"><strong>{{ manageAmountFormat($totalEarnedRewards)}}</strong></td>
                        
                                </tr>
                            </tfoot>
                        </table>

                        
                    @endif
          
                </div>

            </div>
        </div>


    </section>
@endsection
@section('uniquepagestyle')
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
.sticky-header th {
    position: sticky;
    top: 0;
    background-color: white; 
    z-index: 12;
}
/* 
.table-responsive {
    max-height: 500px;
    overflow-y: auto;
} */

</style>
@endsection
@section('uniquepagescript')
   
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            var table = $('#salesman_table').DataTable({
                fixedHeader: true,
                scrollX: true,
                "paging": true,
                // "scrollY": "400px",
                "scrollCollapse": true,
                "pagingType": "full_numbers",
            });
        });
        // $(document).ready(function() {
            // var $qsr = $('#header_id');
            // var offset = $qsr.offset().top;
            //
            // $(window).scroll(function() {
            //     if ($(window).scrollTop() > offset) {
            //         $qsr.addClass('sticky');
            //     } else {
            //         $qsr.removeClass('sticky');
            //     }
            // });
        // });

        $(function() {
            
            $('body').addClass('sidebar-collapse');
            $(".mlselec6t").select2();

        $('.toggle-details').on('click', function() {
            var $row = $(this).closest('tr');
            var routeId = $row.data('route-id');
            var start = $row.data('start-date');
            var end = $row.data('end-date');
            var $icon = $(this);
            var url = '{{ route("salesman-performance-shift-details", [":routeId", ":start", ":end"]) }}';
            url = url.replace(':routeId', routeId).replace(':start', start).replace(':end', end);

            $icon.toggleClass('fa-plus-circle fa-minus-circle');

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
                    var detailsRow = '<tr class="shifts-details"><td colspan="28"><table class="table table-bordered" width="100%"><thead><tr><th>#</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Sales</th><th>Total Route Customers</th><th>Met Customers</th><th>Tonnage Target</th><th>Achieved Tonnage</th><th>Ctn Tonnage</th><th>Dzn Tonnage</th><th>Bulk Tonnage</th><th>Action</th></tr></thead><tbody>';
                    var counter = 1;
                    var totalExpectedCustomers = 0, totalMetCustomers = 0;
                    var totalTargetTonnage = 0, totalAchievedTonnage = 0;
                    var totalCtnsTonnage = 0, totalDznsTonnage = 0, totalBulkTonnage = 0, totalSales=0;

                    data.forEach(function(item) {
                        var metCustomerPercentage = '(' + item.met_customer_percentage + '%)';
                        var achievedTonnagePercentage = '(' + item.achieved_tonnage_percentage + '%)';
                        var ctnsTonnagePercentage = '(' + item.ctns_tonnage_percentage + '%)';
                        var dznsTonnagePercentage = '(' + item.dzns_tonnage_percentage + '%)';
                        var bulkTonnagePercentage = '(' + item.bulk_tonnage_percentage + '%)';

                        totalExpectedCustomers += item.expected_customers;
                        totalMetCustomers += item.met_customers;
                        totalTargetTonnage += item.tonnage_target;
                        totalAchievedTonnage += item.achieved_tonnage;
                        totalCtnsTonnage += item.ctns_tonnage;
                        totalDznsTonnage += item.dzns_tonnage;
                        totalBulkTonnage += item.bulk_tonnage;
                        totalSales += item.sales;

                        var backgroundColor = item.group_count > 1 ? 'background-color: #d0abab;' : '';
                        detailsRow += '<tr style="' + backgroundColor + '"><th>' + counter + '</th><td>' + item.date + '</td><td>' + item.start_time + '</td><td >' + item.closing_time + '</td><td >' + numberWithCommas(item.sales) + '</td><td class="sub-table-qty">'
                             + item.expected_customers + '</td><td class="sub-table-qty">' + item.met_customers + metCustomerPercentage + '</td><td class="sub-table-qty">'
                                 + item.tonnage_target + '</td><td class="sub-table-qty">' + item.achieved_tonnage.toFixed(2) + achievedTonnagePercentage + '</td><td class="sub-table-qty">' + item.ctns_tonnage.toFixed(2) + ctnsTonnagePercentage + '</td><td class="sub-table-qty">' + item.dzns_tonnage.toFixed(2) + dznsTonnagePercentage + '</td><td class="sub-table-qty">' + item.bulk_tonnage.toFixed(2) + bulkTonnagePercentage
                                    + '</td><td class="sub-table-qty"><a href="' + '{{ route('salesman-shift-details', ['id' => '__ID__']) }}'.replace('__ID__', item.salesman_shift_id) + '" target="_blank"><i class="fas fa-eye"></i></a></td></tr>';
                        counter++;
                    });

                    detailsRow += '</tbody><tfoot><tr><th colspan="4">Total</th><th class="sub-table-qty">'+ numberWithCommas(totalSales.toFixed(2)) +'</th><th class="sub-table-qty">'+ totalExpectedCustomers +'</th><th class="sub-table-qty">'+ totalMetCustomers +'</th><th class="sub-table-qty">'
                        + totalTargetTonnage +'</th><th class="sub-table-qty">'+ totalAchievedTonnage.toFixed(2) +'</th><th class="sub-table-qty">'+ totalCtnsTonnage.toFixed(2) +'</th><th class="sub-table-qty">'+ totalDznsTonnage.toFixed(2) +'</th><th class="sub-table-qty">'+ totalBulkTonnage.toFixed(2) +'</th><th></th></tr></tfoot></table></td></tr>';
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
        function numberWithCommas(value) {
                if (value) {
                    let parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
                } else {
                    return "0";
                }
            }
    </script>
@endsection
