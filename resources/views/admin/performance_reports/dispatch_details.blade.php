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
                    <h3 class="box-title"> Dispatch Summary  For {{$data->first()->route_name}}</h3>
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
                        <th>Delivery Date</th>
                        <th>Route</th>
                        <th>Start Time</th>
                        <th >End Time</th>
                        <th>Total Dispatches</th>
                        <th>Dispatches Loaded Next Day</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDispatches = $totalLateDispatches = 0;
                        @endphp
                    @foreach($data as $record)
                      <tr data-schedule-id="{{ $record->schedule_id}}">
                        <th><i class="fa fa-plus-circle toggle-details" style="cursor: pointer; font-size: 16px;"></i></th>
                        <td>{{$record->delivery_date}}</td>
                        <td>{{$record->route_name}}</td>
                        <td>{{$record->start_time}}</td>
                        <td>{{$record->finish_time}}</td>
                        <td class="qty">{{$record->total_store_dispatches}}</td>
                        <td class="qty">{{$record->shifts_dispatched_next_day . '('.manageAmountFormat($record->dispatch_percentage) . '%)'}}</td>
                      </tr>
                      @php
                          $totalDispatches += $record->total_store_dispatches;
                          $totalLateDispatches += $record->shifts_dispatched_next_day;
                      @endphp
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">Totals</th>
                            <th class="qty">{{$totalDispatches}}</th>
                            <th class="qty">{{$totalLateDispatches }}</th>
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
            var scheduleId = $row.data('schedule-id');
            var date = $row.data('date');
            var $icon = $(this);
            var url = '{{ route("driver-performance.driver-dispatch-details.late", [":scheduleId"]) }}';
            url = url.replace(':scheduleId', scheduleId);

            $icon.toggleClass('fa-plus-circle fa-minus-circle');

            if ($row.next('.shifts-details').length > 0) {
                $row.next('.shifts-details').toggle();
                return;
            }
            // if ($row.next('.shifts-details2').length > 0) {
            //     $row.next('.shifts-details2').toggle();
            //     return;
            // }

            var loadingRow = '<tr class="loading-row"><td colspan="27" class="text-center">Loading...</td></tr>';
            $row.after(loadingRow);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    var detailsRow = '<tr><th colspan="7">Dispatches</th></tr><tr class="shifts-details"><td colspan="7"><table class="table table-bordered" width="100%" id="create_datatable_50"><thead><tr><th>#</th><th>Bin Location</th><th>Expected Dispatch Day</th><th>Dispatched At</th><th>Items</th></tr></thead><tbody>';
                    var counter = 1;
                    
                    data.forEach(function(item) {
                        var backgroundColor = item.is_late ? 'background-color: #d0abab;' : '';

                        detailsRow += '<tr style="' + backgroundColor + '"><th>' + counter + '</th><td>' + item.bin + '</td><td>' + item.created_at + '</td><td >' + item.dispatch_time + '</td><td class="qty">'
                             + item.dispatch_items + '</td></tr>';
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
