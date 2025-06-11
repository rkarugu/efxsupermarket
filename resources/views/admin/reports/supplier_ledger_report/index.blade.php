@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Supplier Ledger Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Account Payables Rerports </a> --}}
                </div>
            </div>

            <div class="box-body">
                @include('message')
                <div class="row">
                    <form action="{!! route('maintain-suppliers.supplier-ledger-report') !!}">
                        <input type="hidden" id="startDate" name="from">
                        <input type="hidden" id="endDate" name="to">
                        <div class="col-sm-3">
                            <div id="reportRange" class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="col-sm-3">
                                <button type="submit" name="action" value="excel" class="btn btn-primary">
                                    Export Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-hover" id="supplierLedgerDataTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Transaction Date</th>
                            <th>Supplier No</th>
                            <th>Supplier Name</th>
                            <th>Transaction No</th>
                            <th>Reference</th>
                            <th>CU Invoice Number</th>
                            <th>Description</th>
                            <th>VAT</th>
                            <th>Withholding VAT</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <th colspan="8" class="text-right">Grand Total</th>
                        <th id="total_vat" class="text-right"></th>
                        <th id="total_withholding" class="text-right"></th>
                        <th id="total_amount" class="text-right"></th>
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
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            let start = moment().startOf('month');
            let end = moment().endOf('month');

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

                refreshTable();
            });

            $("#supplierLedgerDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [11, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.supplier-ledger-report') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val()
                        data.to = $("#endDate").val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: "trans_date",
                        name: "trans_date",
                    },
                    {
                        data: "supplier_no",
                        name: "supplier_no",
                    },
                    {
                        data: "supplier.name",
                        name: "supplier.name",
                    },
                    {
                        data: "document_no",
                        name: "document_no",
                    },
                    {
                        data: "suppreference",
                        name: "suppreference",
                    },
                    {
                        data: "cu_invoice_number",
                        name: "cu_invoice_number",
                    },
                    {
                        data: "description",
                        name: "description",
                    },
                    {
                        data: "vat_amount",
                        name: "vat_amount",
                        className: "text-right",
                    },
                    {
                        data: "withholding_amount",
                        name: "withholding_amount",
                        className: "text-right",
                    },
                    {
                        data: "total_amount_inc_vat",
                        name: "total_amount_inc_vat",
                        className: "text-right",
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        visible: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#total_vat").html(json.total_vat);
                    $("#total_withholding").html(json.total_withholding);
                    $("#total_amount").html(json.total_amount);
                }
            })
        })

        function refreshTable() {
            $("#supplierLedgerDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
