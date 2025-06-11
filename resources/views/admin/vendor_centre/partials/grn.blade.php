<ul class="nav nav-tabs">
    <li class="active"><a href="#pendingGrn" data-toggle="tab">Pending Invoice</a></li>
    <li><a href="#CompletedGrn" data-toggle="tab">Invoiced GRNs</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="pendingGrn">
        <div style="padding:10px">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="hidden" id="pendingGrnsStartDate">
                        <input type="hidden" id="pendingGrnsEndDate">
                        <label for="">Select Dates</label>
                        <div class="pendingGrnsReportRange reportRange">
                            <i class="fa fa-calendar" style="padding:8px"></i>
                            <span class="flex-grow-1" style="padding:8px"></span>
                            <i class="fa fa-caret-down" style="padding:8px"></i>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped" id="pendingGrnsDataTable">
                <thead>
                    <tr>
                        <th>Date Received</th>
                        <th>GRN No</th>
                        <th>Order No</th>
                        <th>Received By</th>
                        <th>Store Location</th>
                        <th>Supplier Invoice No</th>
                        <th>Cu Invoice No</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th style="text-align: right;" colspan="7">Total:</th>
                        <th id="pendingGrnTotal"
                            style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="CompletedGrn">
        <div style="padding:10px">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="hidden" id="completedGrnsStartDate">
                        <input type="hidden" id="completedGrnsEndDate">
                        <label for="">Select Dates</label>
                        <div class="completedGrnsReportRange reportRange">
                            <i class="fa fa-calendar" style="padding:8px"></i>
                            <span class="flex-grow-1" style="padding:8px"></span>
                            <i class="fa fa-caret-down" style="padding:8px"></i>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped" id="completedGrnsDataTable">
                <thead>
                    <tr>
                        <th>Date Received</th>
                        <th>GRN No</th>
                        <th>Order No</th>
                        <th>Received By</th>
                        <th>Store Location</th>
                        <th>Supplier Invoice No</th>
                        <th>Cu Invoice No</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th style="text-align: right;" colspan="7">Total:</th>
                        <th id="completedGrnTotal"
                            style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let start = moment().subtract(30, 'days');
            let end = moment();
            $('.completedGrnsReportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $("#completedGrnsStartDate").val(start.format('YYYY-MM-DD'));
            $("#completedGrnsEndDate").val(end.format('YYYY-MM-DD'));

            $('.pendingGrnsReportRange, .completedGrnsReportRange').daterangepicker({
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

            $('.pendingGrnsReportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#pendingGrnsStartDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#pendingGrnsEndDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.pendingGrnsReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' +
                    picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable($("#pendingGrnsDataTable"));
            });

            $('.completedGrnsReportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#completedGrnsStartDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#completedGrnsEndDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.completedGrnsReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' +
                    picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable($("#completedGrnsDataTable"));
            });

            $("#pendingGrnsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.grn', $supplier->id) !!}',
                    data: function(data) {
                        data.status = 'pending';
                        data.from = $("#pendingGrnsStartDate").val();
                        data.to = $("#pendingGrnsEndDate").val();
                    }
                },
                columns: [
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    },{
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'purchase_order.purchase_no',
                        name: 'purchaseOrder.purchase_no'
                    },
                    {
                        data: 'purchase_order.getrelated_employee.name',
                        name: 'purchaseOrder.getrelatedEmployee.name'
                    },
                    {
                        data: 'purchase_order.get_store_location.location_name',
                        name: 'purchaseOrder.getStoreLocation.location_name'
                    },
                    {
                        data: 'supplier_invoice_no',
                        name: 'supplier_invoice_no'
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: "text-right",
                        searchable: false
                    },
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, row, meta)
                        {
                            if (type === 'display')
                            {
                                data = '<a title="Export To Pdf" href="/admin/completed-grn/'+row.purchase_order.slug+'/print-to-pdf?grn='+data+'">'+data+'</a>';
                            }
                            return data;
                        }
                    }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#pendingGrnTotal").text(json.total);
                }
            });

            $("#completedGrnsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.grn', $supplier->id) !!}',
                    data: function(data) {
                        data.status = 'completed';
                        data.from = $("#completedGrnsStartDate").val();
                        data.to = $("#completedGrnsEndDate").val();
                    }
                },
                columns: [
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    },{
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'purchase_order.purchase_no',
                        name: 'purchaseOrder.purchase_no'
                    },
                    {
                        data: 'purchase_order.getrelated_employee.name',
                        name: 'purchaseOrder.getrelatedEmployee.name'
                    },
                    {
                        data: 'purchase_order.get_store_location.location_name',
                        name: 'purchaseOrder.getStoreLocation.location_name'
                    },
                    {
                        data: 'supplier_invoice_no',
                        name: 'supplier_invoice_no'
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: "text-right",
                        searchable: false
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#completedGrnTotal").text(json.total);
                }
            });
        });
    </script>
@endpush
