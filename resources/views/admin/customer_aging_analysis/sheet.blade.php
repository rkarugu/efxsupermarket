@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <h3 class="box-title">Customer Aging Analysis Report</h3>
            </div>
            <div class="box-body">
                {!! Form::open(['route' => 'customer-aging-analysis.index', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">Date</label>
                            {!! Form::text('start-date', null, [
                                'class' => 'datepicker form-control start-date-value',
                                'placeholder' => 'Start Date',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">Balance</label>
                            <select name="type" id="type_supplier_amount" class="form-control">
                                <option value="All" selected="">All</option>
                                <option value="zero"> Zero Balances</option>
                                <option value="more"> Greater OR Less than zero Balances</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label for="" style="display: block">&nbsp;</label>
                        <button class="btn btn-primary" type="submit" name="action" value="print">
                            <i class="fa fa-file-pdf"></i>
                            Print PDF
                        </button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="box-body">
                <table id="agingReportDataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>0-30 Days</th>
                            <th>31-60 Days</th>
                            <th>61-90 Days</th>
                            <th>91-120 Days</th>
                            <th>> 120 Days</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
@endsection


@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#agingReportDataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{{ route('customer-aging-analysis.index') }}',
                    data: function(data) {
                        data.from = $("#start-date").val();
                        data.type = $("#type").val();
                    }
                },
                columns: [{
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'days_0_30',
                        name: 'days_0_30',
                        orderable: false,
                        searchable: false,
                        className: "text-right",
                    },
                    {
                        data: 'days_31_60',
                        name: 'days_31_60',
                        orderable: false,
                        searchable: false,
                        className: "text-right",
                    },
                    {
                        data: 'days_61_90',
                        name: 'days_61_90',
                        orderable: false,
                        searchable: false,
                        className: "text-right",
                    },
                    {
                        data: 'days_91_120',
                        name: 'days_91_120',
                        orderable: false,
                        searchable: false,
                        className: "text-right",
                    },
                    {
                        data: 'days_120',
                        name: 'days_120',
                        orderable: false,
                        searchable: false,
                        className: "text-right",
                    },
                ]
            });
        })
    </script>
@endpush
