<div style="padding:10px">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#pendingInvoices" data-toggle="tab">Pending Payment</a></li>
        <li><a href="#processingInvoices" data-toggle="tab">Processing Payment</a></li>
        <li><a href="#completedInvoices" data-toggle="tab">Completed Payment</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="pendingInvoices">
            <div style="padding:10px;">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="hidden" id="pendingInvoicesStartDate">
                            <input type="hidden" id="pendingInvoicesEndDate">
                            <label for="">Select Dates</label>
                            <div class="pendingInvoiceReportRange reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px"></span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="pendingInvoicesDataTable">
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>GRN No.</th>
                            <th>CU Invoice No</th>
                            <th>Supplier Reference</th>
                            <th>Prepared By</th>
                            <th>VAT Amount</th>
                            <th>Total Amount</th>
                            <th>Withholding Amount</th>
                            <th>Credit Amount</th>
                            <th>Payable Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-right">Total:</th>
                            <th id="pendingInvoicesBalance"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="processingInvoices">
            <div style="padding:10px">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="hidden" id="processingInvoicesStartDate">
                            <input type="hidden" id="processingInvoicesEndDate">
                            <label for="">Select Dates</label>
                            <div class="processingInvoicesReportRange reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px"></span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="processingInvoicesDataTable">
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>GRN No.</th>
                            <th>CU Invoice No</th>
                            <th>Supplier Reference</th>
                            <th>Prepared By</th>
                            <th>VAT Amount</th>
                            <th>Total Amount</th>
                            <th>Withholding Amount</th>
                            <th>Credit Amount</th>
                            <th>Paid Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-right">Total:</th>
                            <th id="processingInvoicesAmount"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="completedInvoices">
            <div style="padding:10px">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="hidden" id="completedInvoicesStartDate">
                            <input type="hidden" id="completedInvoicesEndDate">
                            <label for="">Select Dates</label>
                            <div class="completedInvoiceReportRange reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px"></span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="completedInvoicesDataTable">
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>GRN No.</th>
                            <th>CU Invoice No</th>
                            <th>Supplier Reference</th>
                            <th>Prepared By</th>
                            <th>VAT Amount</th>
                            <th>Total Amount</th>
                            <th>Withholding Amount</th>
                            <th>Credit Amount</th>
                            <th>Paid</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-right">Total:</th>
                            <th id="completedInvoicesAmount"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            let start = moment().subtract(30, 'days');
            let end = moment();

            $('.completedInvoiceReportRange span').html(start.format(
                    'MMM D, YYYY') +
                ' - ' + end.format('MMM D, YYYY'));

            $("#completedInvoicesStartDate").val(start.format('YYYY-MM-DD'));
            $("#completedInvoicesEndDate").val(end.format('YYYY-MM-DD'));

            $('.pendingInvoiceReportRange, .approvedInvoicesReportRange, .completedInvoiceReportRange')
                .daterangepicker({
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

            $('.pendingInvoiceReportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#pendingInvoicesStartDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#pendingInvoicesEndDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.pendingInvoiceReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' +
                    picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable($("#pendingInvoicesDataTable"));
            });

            $('.approvedInvoicesReportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#processingInvoicesStartDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#processingInvoicesEndDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.approvedInvoicesReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' +
                    picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable($("#processingInvoicesDataTable"));
            });

            $('.completedInvoiceReportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#completedInvoicesStartDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#completedInvoicesEndDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.completedInvoiceReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' +
                    picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable($("#completedInvoicesDataTable"));
            });

            $("#pendingInvoicesDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.payables', $supplier->supplier_code) !!}',
                    data: function(data) {
                        data.status = 'pending';
                        data.from = $("#pendingInvoicesStartDate").val();
                        data.to = $("#pendingInvoicesEndDate").val();
                    }
                },
                columns: [{
                        data: 'trans_date',
                        name: 'trans_date'
                    }, {
                        data: 'invoice.grn_number',
                        name: 'invoice.grn_number'
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    },
                    {
                        data: 'suppreference',
                        name: 'suppreference'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount',
                        className: "text-right"
                    },
                    {
                        data: 'total_amount_inc_vat',
                        name: 'total_amount_inc_vat',
                        className: "text-right"
                    },
                    {
                        data: 'withholding_amount',
                        name: 'withholding_amount',
                        className: "text-right"
                    },
                    {
                        data: 'note_amount',
                        name: 'notes.note_amount',
                        className: "text-right"
                    },
                    {
                        data: 'payable_amount',
                        name: 'payable_amount',
                        className: "text-right",
                        searchable: false,
                        orderable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#pendingInvoicesBalance").html(json.total_payable);
                }
            });

            $("#processingInvoicesDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.payables', $supplier->supplier_code) !!}',
                    data: function(data) {
                        data.status = 'processing';
                        data.from = $("#processingInvoicesStartDate").val();
                        data.to = $("#processingInvoicesEndDate").val();
                    }
                },
                columns: [{
                        data: 'trans_date',
                        name: 'trans_date'
                    }, {
                        data: 'invoice.grn_number',
                        name: 'invoice.grn_number'
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    },
                    {
                        data: 'suppreference',
                        name: 'suppreference'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount',
                        className: "text-right"
                    },
                    {
                        data: 'total_amount_inc_vat',
                        name: 'total_amount_inc_vat',
                        className: "text-right"
                    },
                    {
                        data: 'withholding_amount',
                        name: 'withholding_amount',
                        className: "text-right"
                    },
                    {
                        data: 'note_amount',
                        name: 'notes.note_amount',
                        className: "text-right"
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',
                        className: "text-right",
                        searchable: false,
                        orderable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#processingInvoicesAmount").html(json.total_paid);
                }
            });

            $("#completedInvoicesDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.payables', $supplier->supplier_code) !!}',
                    data: function(data) {
                        data.status = 'completed';
                        data.from = $("#completedInvoicesStartDate").val();
                        data.to = $("#completedInvoicesEndDate").val();
                    }
                },
                columns: [{
                        data: 'trans_date',
                        name: 'trans_date'
                    }, {
                        data: 'invoice.grn_number',
                        name: 'invoice.grn_number'
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    },
                    {
                        data: 'suppreference',
                        name: 'suppreference'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount',
                        className: "text-right"
                    },
                    {
                        data: 'total_amount_inc_vat',
                        name: 'total_amount_inc_vat',
                        className: "text-right"
                    },
                    {
                        data: 'withholding_amount',
                        name: 'withholding_amount',
                        className: "text-right"
                    },
                    {
                        data: 'note_amount',
                        name: 'notes.note_amount',
                        className: "text-right"
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',
                        className: "text-right",
                        searchable: false,
                        orderable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#completedInvoicesAmount").html(json.total_paid);
                }
            });
        })
    </script>
@endpush
