@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Group Performance Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="filterForm" action="{{ route('sales-and-receivables-reports.group-performance-report') }}"
                    method="get">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="branch">Select Branch</label>
                            <select name="branch" id="branch" class="form-control branch_filter">
                                <option value="" disabled>--Select Branch--</option>
                                @foreach($branches as $branch)
                                    <option value="{{$branch->id}}" {{ request()->branch == $branch->id ? 'selected' : '' }}>{{$branch->name}}</option>
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

                        <div class="form-group col-md-2">
                            <label for="filter" class="control-label"> Select filter </label>
                            <select name="filter" id="filter" class="form-control">
                                <option value="" selected disabled> Select a filter</option>
                                <option value="sales">SALES</option>
                                <option value="tonnage">TONNAGE</option>
                                <option value="dzns">DZNS</option>
                                <option value="ctns">CTNS</option>
                                <option value="unmet">UNMET</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <input type="hidden" name="intent" id="intent" value="">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" id="filterButton" name="intent" value="FILTER" class="btn btn-primary" />
                            <input type="submit" id="excelButton" name="intent" value="EXCEL"
                                class="btn btn-primary ml-12" />
                            <a href="#" id="clearButton" class="btn btn-primary ml-12">Clear</a>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="group-performance-table">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>FREQUENCY</th>
                                <th>CENTERS</th>
                                <th>SHOPS</th>
                                <th>MET</th>
                                <th>UNMET</th>
                                <th>CTNS</th>
                                <th>DZNS</th>
                                <th>TONNAGE</th>
                                <th>GROSS SALES</th>
                                <th>RETURNS</th>
                                <th>NET SALES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedData as $group => $groupData)
                                
                                <tr>
                                    <td>{{ $group }}</td>
                                    <td>{{ $groupSums[$group]['freq'] }}</td>
                                    <td>{{ $groupSums[$group]['centre_count'] }}</td>
                                    <td>{{ $groupSums[$group]['shop_count'] }}</td>
                                    <td>{{ $groupSums[$group]['met_shops'] }}</td>
                                    <td>
                                        <a
                                            href="{{ route('sales-and-receivables-reports.group-data-filter-route-item-report', ['group' => $group, 'ctns_dzns' => 'unmet', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                                            {{ manageAmountFormat($groupSums[$group]['unmet']) }}
                                        </a>
                                    </td>

                                    <td>
                                        <a
                                            href="{{ route('sales-and-receivables-reports.group-data-filter-route-item-report', ['group' => $group, 'ctns_dzns' => 'ctns', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                                            {{ manageAmountFormat($groupSums[$group]['ctns']) }}
                                        </a>
                                    </td>

                                    <td>
                                        <a
                                            href="{{ route('sales-and-receivables-reports.group-data-filter-route-item-report', ['group' => $group, 'ctns_dzns' => 'dzns', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                                            {{ manageAmountFormat($groupSums[$group]['dzns']) }}
                                        </a>
                                    </td>

                                    <td>{{ manageAmountFormat($groupSums[$group]['tonnage']) }}</td>
                                    <td>{{ manageAmountFormat($groupSums[$group]['gross_sales']) }}</td>
                                    <td>{{ manageAmountFormat($groupSums[$group]['returns']) }}</td>
                                    <td>{{ manageAmountFormat($groupSums[$group]['gross_sales'] - $groupSums[$group]['returns']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:bolder">
                                <td>Totals</td>
                                <td>{{ manageAmountFormat($totalGroupSums['freq']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['centre_count']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['shop_count']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['met_shops']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['unmet']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['ctns']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['dzns']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['tonnage']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['gross_sales']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['returns']) }}</td>
                                <td>{{ manageAmountFormat($totalGroupSums['gross_sales'] - $totalGroupSums['returns']) }}
                                </td>
                            </tr>
                        </tfoot>
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
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script type="text/javascript">
        $(function() {
            // $('body').addClass('sidebar-collapse');
            $("#route").select2();
            $("#filter").select2();
            $("#group").select2();
            $(".new_filters").select2();
            $(".branch_filter").select2();


        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        $(document).ready(function() {
            $('#group-performance-table').DataTable({
                "paging": true,
                "pageLength": 10,
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

        $('#clearButton').click(function(event) {
            event.preventDefault();

            $('#filterButton').addClass('disabled').attr('disabled', true);
            $(this).addClass('loading').attr('disabled', true).text('Processing...');

            var currentDate = new Date().toISOString().slice(0, 10);
            $('input[type="date"]').val(currentDate);

            $.ajax({
                type: 'GET',
                url: '{{ route('sales-and-receivables-reports.group-performance-report') }}',
                success: function(response) {
                    $('#filterForm select').val('').trigger('change');

                    $('#group-performance-table').DataTable().clear().draw();
                    $('#group-performance-table tfoot').empty();
                    $('#group-performance-table').DataTable().rows.add($(response).find('tbody tr'))
                        .draw();
                    var newTfootContent = $(response).find('tfoot').html();
                    $('#group-performance-table tfoot').html(newTfootContent);

                    history.pushState({}, '', window.location.pathname);
                },
                error: function(xhr, status, error) {},
                complete: function() {
                    Toast.fire({
                        icon: "success",
                        title: 'Data cleared'
                    });
                    $('#filterButton').removeClass('disabled').removeAttr('disabled');
                    $('#clearButton').removeClass('loading').removeAttr('disabled').text('Clear');
                }
            });
        });

        $('#excelButton').click(function(event) {
            event.preventDefault();

            var formData = $('#filterForm').serialize();

            var excelForm = document.createElement('form');
            excelForm.setAttribute('method', 'GET');
            excelForm.setAttribute('action',
                '{{ route('sales-and-receivables-reports.group-performance-report') }}');

            var filterInputs = $('#filterForm').find('input[name!=intent], select[name!=intent]').clone();
            $(excelForm).append(filterInputs);

            var intentInput = document.createElement('input');
            intentInput.setAttribute('type', 'hidden');
            intentInput.setAttribute('name', 'intent');
            intentInput.setAttribute('value', 'EXCEL');
            excelForm.appendChild(intentInput);

            document.body.appendChild(excelForm);

            excelForm.submit();

            $(excelForm).remove();
        });

        $('#filterForm').submit(function(event) {
            $('#intent').val('FILTER');
            event.preventDefault();

            var formData = $(this).serialize();
            var buttonClicked = $(document.activeElement).attr('name');

            if (buttonClicked === 'intent') {
                $('#clearButton').addClass('disabled').attr('disabled', true);
                $('#filterButton').addClass('loading').attr('disabled', true).val('Processing...');

                $.ajax({
                    type: 'GET',
                    url: $(this).attr('action'),
                    data: formData,
                    success: function(response) {
                        $('#clearButton').removeClass('disabled').removeAttr('disabled');
                        $('#filterButton').removeClass('loading').removeAttr('disabled').val('FILTER');
                        $('#group-performance-table').DataTable().clear().draw();
                        $('#group-performance-table').DataTable().rows.add($(response).find('tbody tr'))
                            .draw();
                        var newTfootContent = $(response).find('tfoot').html();
                        $('#group-performance-table tfoot').html(newTfootContent);
                        var newUrl = window.location.pathname + '?' + formData;
                        history.pushState({}, '', newUrl);
                    },
                    error: function(xhr, status, error) {
                        $('#clearButton').removeClass('disabled').removeAttr('disabled');
                        $('#filterButton').removeClass('loading').removeAttr('disabled').val('FILTER');
                    },
                    complete: function() {
                        Toast.fire({
                            icon: "success",
                            title: 'Data fetched successfully'
                        });
                    }
                });
            } else {
                $('#filterForm select').val('').trigger('change');
                $(this).submit();
            }
        });
    </script>
    {{-- weeekly filter functionality --}}
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
