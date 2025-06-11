@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Sales Vs Payments Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">

                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="branch">Route</label>
                            <select name="route_id" id="route_id" class="form-control new_filters">
                                <option value="">select Route</option>
                                @foreach ($routes as $route)
                                    <option value="{{$route->id}}" {{ request()->route_id == $route->id ? 'selected' : ''}}>{{$route->route_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="selectionType">Select Date Type</label>
                            <select name = "selectionType" id="selectionType" class="form-control new_filters">
                                <option value="single" {{ request()->selectionType == 'single' ? 'selected' : '' }}>Single
                                    Date</option>
                                <option value="range" {{ request()->selectionType == 'range' ? 'selected' : '' }}>Date
                                    Range</option>
                            </select>


                        </div>
                        <div class="form-group col-md-2">
                            <label for="dateSelector">Select Date/Range</label>
                            <input type="text" name="datePicker" id="datePicker" placeholder="Select Date or Range"
                                   class="form-control"
                                   value="{{ old('datePicker', request('datePicker', \Carbon\Carbon::now()->toDateString())) }}">

                        </div>
                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary" />
{{--                            <input type="submit" name="intent" value="EXCEL" class="btn btn-primary ml-12" />--}}
                            <button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button>
                            <a href="{{ route('sales-and-receivables-reports.route-customers-reports') }}"
                               class="btn btn-primary ml-12"> Clear </a>
                        </div>
                    </div>

                </form>

                <hr>

                <div class="table-responsive">
                    @isset($salesAndPayments)
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>DATE</th>
                                <th>Day</th>
                                <th>SALES</th>
                                <th>RETURNS</th>
                                <th>PAYMENTS NEXT DAY</th>
                                <th>BALANCE</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($salesAndPayments as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->sales_date }}</td>
                                    <td>{{  date('l', strtotime($item->sales_date)) }}</td>
                                    <td>{{ number_format($item->total_sales, 2) }}</td>
                                    <td>{{ number_format(abs($item->total_returns), 2) }}</td>
                                    <td>{{number_format( abs($item->total_payments_following_day), 2) }}</td>
                                    <td>{{ number_format($item->total_payments_following_day + $item->total_returns + $item->total_sales,2)  }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr style="font-weight:bolder">
                                <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total</td>
                                <td>{{ number_format($salesAndPayments->sum('total_sales'), 2) }}</td>
                                <td>{{ number_format(abs($salesAndPayments->sum('total_returns')), 2) }}</td>
                                <td>{{ number_format(abs($salesAndPayments->sum('total_payments_following_day')), 2) }}</td>
                                <td>{{ number_format($salesAndPayments->sum('total_sales')+ $salesAndPayments->sum('total_returns') + $salesAndPayments->sum('total_payments_following_day'), 2)  }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    @endisset
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script type="text/javascript">
        $(function() {
            $('body').addClass('sidebar-collapse');
            $("#route").select2();
            $("#filter").select2();
            $(".new_filters").select2();
            $("#frequency_filter").select2();
            $("#group").select2();
            $("#frequency_filter").val("1").trigger("change");
            $("#filter").val("sales").trigger("change");
        });

        $(document).ready(function() {
            $('#create_datatable').DataTable().destroy();
            $('#create_datatable').DataTable({
                "paging": true,
                "pageLength": 100,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            const datePicker = $('#datePicker')[0];
            const selectionType = $('#selectionType');
            let flatpickrInstance;


            const initFlatpickr = (mode) => {
                if (flatpickrInstance) {
                    flatpickrInstance.destroy();
                }

                flatpickrInstance = flatpickr(datePicker, {
                    mode: mode,
                    dateFormat: "Y-m-d",
                    // defaultDate: today,
                    onClose: function(selectedDates, dateStr, instance) {
                        if (mode === "range" && selectedDates.length === 2) {
                            const startDate = selectedDates[0];
                            const endDate = selectedDates[1];

                            const startDay = startDate.getDay();
                            const endDay = endDate.getDay();

                            const startOfWeek = new Date(startDate);
                            const endOfWeek = new Date(endDate);

                            startOfWeek.setDate(startDate.getDate() - (startDay === 0 ? 6 :
                                startDay - 1));
                            endOfWeek.setDate(endDate.getDate() + (endDay === 0 ? 0 : 7 - endDay));

                            instance.setDate([startOfWeek, endOfWeek]);
                        }
                    }
                });


            };
            const selectedMode = selectionType.val() === 'single' ? 'single' : 'range';
            initFlatpickr(selectedMode);




            selectionType.on('change', function(event) {
                const selectedMode = $(this).val() === 'single' ? 'single' : 'range';

                initFlatpickr(selectedMode);
            });
        });
    </script>
@endsection
