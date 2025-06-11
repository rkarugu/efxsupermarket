@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border  no-padding-h-b">
                <h3 class="box-title">Processed Invoices</h3>
                @include('message')
            </div>
            <div class="box-body">
                <form method="GET" action="{{ route('maintain-suppliers.processed_invoices.index') }}">
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="hidden" id="startDate" name="from">
                            <input type="hidden" id="endDate" name="to">
                            <div class="form-group">
                                <label for="reportRange">Dates</label>
                                <div id="reportRange" class="reportRange">
                                    <i class="fa fa-calendar" style="padding:8px"></i>
                                    <span class="flex-grow-1" style="padding:8px">
                                        {{ request()->to && request()->from ? request()->from . ' - ' . request()->to : 'Select Dates' }}
                                    </span>
                                    <i class="fa fa-caret-down" style="padding:8px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <select class="form-control" name="supplier" id="supplier">
                                    <option value="">SELECT ALL</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request()->supplier == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <select class="form-control" name="location" id="location">
                                    <option value="">SELECT ALL</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ request()->location == $location->id ? 'selected' : '' }}>
                                            {{ $location->location_name }} ({{ $location->location_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label style="display: block">&nbsp;</label>
                                <button type="submit" name="action" value="excel" class="btn btn-primary">
                                    Excel
                                </button>
                                <button type="submit" name="action" value="pdf" class="btn btn-primary">
                                    Print PDF
                                </button>


                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-hover" id="processedInvoicesDataTable">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Posting Date</th>
                            <th>LPO No.</th>
                            <th>LPO Date</th>
                            <th>GRN No.</th>
                            <th>GRN Date</th>
                            <th>Invoice No.</th>
                            <th>Supplier</th>
                            <th>Supplier Invoice No</th>
                            <th>Supplier Invoice Date</th>
                            <th>CU Invoice No</th>
                            <th>Prepared By</th>
                            <th>Vat Amount</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th colspan="13"> TOTAL </th>
                            <th colspan="2">{{ manageAmountFormat($sumTotal) }}</th>
                        </tr>
                    </tfoot>
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
        });

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

            $("#processedInvoicesDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.processed_invoices.index') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                        data.supplier = $("#supplier").val();
                        data.location = $("#location").val();
                        data.period = "{{ request()->period }}";
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
                        data: 'grn_number',
                        name: 'grn_number',
                    },
                    {
                        data: 'grn_date',
                        name: 'grn_date',
                    },
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

            $("#processedInvoicesDataTable tbody").on('click', '[data-toggle="reverse"]', function(){
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure you want to reverse the invoice? This action cannot be undone',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, I Confirm',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            })
        })

        function refreshTable() {
            $("#processedInvoicesDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
