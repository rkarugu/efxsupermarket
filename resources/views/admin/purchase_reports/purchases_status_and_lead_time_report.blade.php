@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Lead Time Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Purchases Rerports </a>
                </div>
                @include('message')
            </div>
            <div class="box-body">
                <form method="GET" action="{{ route('lpo-status-and-leatime-reports') }}">
                    <div class="row">

                        <div class="col-md-3 ">
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <select class="form-control" name="supplier" id="supplier">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request()->supplier == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="from">From Date</label>
                                <input type="date" class="form-control" name="from" id="from"
                                    value="{{ request()->from }}">

                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="to">To Date</label>
                                <input type="date" class="form-control" name="to" id="to"
                                    value="{{ request()->to }}">

                            </div>
                        </div>

                        <div class="col-sm-1">
                            <div class="form-group">
                                <label style="display: block">&nbsp;</label>
                                <button type="submit" name="action" value="pdf" class="btn btn-primary">
                                    Filter
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label style="display: block">&nbsp;</label>
                                <button type="submit" name="action" value="download" class="btn btn-primary">
                                    Download
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label style="display: block">&nbsp;</label>
                                <a href="{{ route('lpo-status-and-leatime-reports') }}" class="btn btn-primary">Clear</a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <th>#</th>
                        <th>LPO Date</th>
                        <th>LPO No.</th>
                        <th>LPO User</th>
                        <th>GRN No.</th>
                        <th>GRN Date</th>
                        <th>GRN User</th>
                        <th>Invoice No.</th>
                        <th>Supplier</th>
                        <th>Supplier Invoice No</th>
                        <th>Supplier Invoice Date</th>
                        <th>CU Invoice No</th>
                        <th>Invoice User</th>
                        <th>LPO Total</th>
                        <th>GRN Total</th>
                        <th>Invoice Amount</th>

                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>{{ \Carbon\Carbon::parse($row->lpo->created_at)->toDateString() }}</td>
                                <td>{{ $row->lpo->purchase_no }}</td>
                                {{-- <td>{{ $row->lpo->purchase_date }}</td> --}}
                                <td>{{ $row->lpo->getrelatedEmployee?->name }}</td>
                                <td>{{ $row->grn_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->grn_date)->toDateString() }}</td>
                                <td>{{ $row->lpo->getrelatedEmployee?->name }}</td>
                                <td> {{ $row->supplierInvoice[0]->invoice_number ?? '-' }} </td>
                                <td> {{ $row->lpo->supplier?->name }} </td>
                                <td> {{ $row->supplierInvoice[0]->supplier_invoice_number ?? '-' }} </td>
                                <td> {{ $row->supplierInvoice[0]->supplier_invoice_date ?? '-' }} </td>

                                {{-- <td> {{ optional($row->supplierInvoice->first())->created_at ? $row->supplierInvoice->first()->created_at->format('Y-m-d') : '-' }} </td> --}}
                                <td> {{ $row->supplierInvoice[0]->cu_invoice_number ?? '-' }} </td>
                                <td> {{ $row->supplierInvoice[0]->user->name ?? '-' }} </td>
                                <td style="text-align: right">{{ manageAmountFormat($row->lpoTotal) }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($row->grnTotal) }}</td>
                                <td style="text-align: right">
                                    {{ manageAmountFormat($row->supplierInvoice[0]->amount ?? 0) }} </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#supplier, #location").select2()

            $("#supplier, #location").change(function() {
                refreshTable();
            })

            let start = moment().subtract(30, 'days');
            let end = moment();

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

                $('.reportRange span').html(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate
                    .format('YYYY-MM-DD'));

                refreshTable();
            });
        })

        $("#processedInvoicesDataTable2").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, "asc"]
            ],
            autoWidth: true,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('lpo-status-and-leatime-reports') !!}',
                data: function(data) {
                    data.from = $("#startDate").val();
                    data.to = $("#endDate").val();
                    data.supplier = $("#supplier").val();
                    data.location = $("#location").val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'lpo.purchase_no',
                    name: 'lpo.purchase_no',
                },
                {
                    data: 'lpo.created_at',
                    name: 'lpo.created_at',
                },
                {
                    data: 'lpo.employee',
                    name: 'lpo.employee',
                },
                {
                    data: 'grn_number',
                    name: 'grn_number',
                },
                {
                    data: 'grn_date',
                    name: 'grn_date',
                },
                // {
                //     data: 'grn.user',
                //     name: 'grn.user',
                // },
                {
                    data: 'invoice_number',
                    name: 'invoice_number',
                },
                {
                    data: 'supplier.name',
                    name: 'supplier.name',
                },
                {
                    data: 'supplier_invoice_number',
                    name: 'supplier_invoice_number',
                },
                {
                    data: 'supplier_invoice_date',
                    name: 'supplier_invoice_date',
                },
                {
                    data: 'cu_invoice_number',
                    name: 'cu_invoice_number',
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                },
                {
                    data: 'vat_amount',
                    name: 'vat_amount',
                    className: 'text-right',
                },
                // {
                //     data: 'lpo.total',
                //     name: 'lpo.total',
                //     className: 'text-right',
                // },
                // {
                //     data: 'grn.total',
                //     name: 'grn.total',
                //     className: 'text-right',
                // },
                {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-right',
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    searchable: false,
                    orderable: false,
                },
            ]
        });

        function refreshTable() {
            $("#processedInvoicesDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
