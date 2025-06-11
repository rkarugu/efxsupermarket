@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <h4 class="box-title">Bank Payments Report</h4>
            </div>
            <div class="box-header with-border">
                <form>
                    <input type="hidden" id="startDate" name="from">
                    <input type="hidden" id="endDate" name="to">
                    <div class="row">
                        <div class="col-sm-3">
                            <div id="reportRange" class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="supplier" id="supplier" class="form-control select2">
                                <option value="" selected>Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(request()->supplier == $supplier->id)>
                                        {{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <button type="submit" class="btn btn-primary" name="download" value="excel">
                                    <i class="fa fa-file-excel"></i> Excel
                                </button>
                                <button type="submit" class="btn btn-primary" name="download" value="pdf">
                                    <i class="fa fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                @include('admin.bank_files.partials.bank_payments')
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $("select.form-control").select2();

            let start_date = "{{ request()->from }}";
            let end_date = "{{ request()->to }}";
            let start = start_date ? moment(start_date) : moment().subtract(7, 'days');
            let end = end_date ? moment(end_date) : moment();

            $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));

            $("#startDate").val(start.format('YYYY-MM-DD'));
            $("#endDate").val(end.format('YYYY-MM-DD'));

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });

            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));
            });
        });
    </script>
@endpush
