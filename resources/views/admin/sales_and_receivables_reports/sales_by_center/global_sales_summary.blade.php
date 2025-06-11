@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{{ $title }}</h3>
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
                            <a href="{{ route('sales-and-receivables-reports.sales-by-center-global-sales-summary') }}"
                               class="btn btn-primary ml-12"> Clear </a>
                        </div>
                    </div>

                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Sales</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($data)
                        <tr>
                            <td>{{  today() }}</td>
                            <td>{{ number_format($data['total_sales'], 2) }}</td>

                        </tr>
                        @endisset
                        </tbody>
                    </table>
                </div>

                <div id="chart"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        @isset($data)
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                chart: {
                    type: 'line',
                    height: 400
                },
                series: [{
                    name: 'Sales',
                    data: @json($data['sales_by_month']->pluck('total_sales'))
                }],
                xaxis: {
                    categories: @json($data['sales_by_month']->pluck('month'))
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return new Intl.NumberFormat('en-KE', {
                                minimumFractionDigits: 0
                            }).format(value);
                        }
                    },
                    title: {
                        text: 'Total Sales'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return new Intl.NumberFormat('en-KE', {
                                minimumFractionDigits: 0
                            }).format(value);
                        }
                    }
                },
                title: {
                    text: 'Sales by Month',
                    align: 'center'
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        });
        @endisset
    </script>


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
