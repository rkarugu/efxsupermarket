@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Unmet Customers Report</h3>
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
                            <label for="branch">Branch</label>
                            <select name="branch" id="branch" class="form-control new_filters">
                                <option value="">select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ request()->branch == $branch->id ? 'selected' : ''}}>{{$branch->name}}</option>

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

                        <div class="form-group col-md-2">
                            <label for="route" class="control-label"> Select Route </label>
                            <select name="route" id="route" class="form-control">
                                <option value="" selected disabled> Select a route</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}" @if (request()->route == $route->id) selected @endif>
                                        {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="filter" class="control-label"> Select group </label>
                            <select name="group" id="group" class="form-control">
                                <option value="" selected disabled> Select a group</option>
                                <option value="A" @if (request()->group == 'A') selected @endif>A</option>
                                <option value="B" @if (request()->group == 'B') selected @endif>B</option>
                                <option value="C" @if (request()->group == 'C') selected @endif>C</option>
                                <option value="D" @if (request()->group == 'D') selected @endif>D</option>
                                <option value="E" @if (request()->group == 'E') selected @endif>E</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary" />
                            <input type="submit" name="intent" value="EXCEL" class="btn btn-primary ml-12" />
                            <a href="{{ route('sales-and-receivables-reports.sales-per-route') }}"
                               class="btn btn-primary ml-12"> Clear </a>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>ROUTE</th>
                            <th>GROUP</th>
                            <th>SALESMAN</th>
                            <th>FREQUENCY</th>
                            <th>CENTRES</th>
                            <th>SHOPS</th>
                            <th>MET</th>
                            <th>UNMET</th>
                            <th>MET CUSTOMERS %</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($data))
                            @foreach ($data as $record)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $record->route }}</td>
                                    <td>{{ $record->group ?? '-' }}</td>
                                    <td>{{ $record->salesman }}</td>
                                    <td>{{ $record->multiplier }}</td>
                                    <td>{{ $record->centre_count }}</td>
                                    <td>{{ $record->shop_count }}</td>
                                    <td>{{ $record->met_shops }}</td>
                                    <td>
                                        <a target="blank"
                                           href="{{ route('sales-and-receivables-reports.group-filter-route-item-unment-report', ['route_id' => $record->route_id, 'frequency_filter' => request()->frequency_filter, 'ctns_dzns' => 'unmet', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                                            {{ max($record->unmet, 0) }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ min($record ->met_customers_percentage, 100) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>

                        </tfoot>
                        @endif
                    </table>
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
