<form action="{{ route('maintain-suppliers.supplier-statement') }}" method="get">
    <div style="padding:10px">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="hidden" id="statementStartDate" name="from">
                    <input type="hidden" id="statementEndDate" name="to">
                    <input type="hidden" name="supplier_code" value="{{ $supplier->supplier_code }}">
                    <div class="statementReportRange reportRange">
                        <i class="fa fa-calendar" style="padding:8px"></i>
                        <span class="flex-grow-1" style="padding:8px"></span>
                        <i class="fa fa-caret-down" style="padding:8px"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary" name="manage-request" value="pdf">
                    <i class="fa fa-print"></i>
                    Print Pdf</button>
            </div>
        </div>
    </div>
</form>
<div style="padding: 10px">
    <table class="table table-striped table-bordered" id="statementDataTable">
        <thead>
            <tr>
                <th class="text-right" colspan="7">Opening Balance</th>
                <th id="openingBalance"
                    style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;"></th>
            </tr>
            <tr>
                <th>Transaction Date</th>
                <th>Memo</th>
                <th>Document No</th>
                <th>Supplier Reference</th>
                <th>Cu Inoice Number</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Credit</th>
                <th style="text-align: right;">Running Balance</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th style="text-align: right;" colspan="7">Closing Balance:</th>
                <th id="total" style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                </th>
            </tr>
        </tfoot>
    </table>
</div>
@push('styles')
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            let start = moment().subtract(30, 'days');
            let end = moment();

            $('.statementReportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $("#statementStartDate").val(start.format('YYYY-MM-DD'));
            $("#statementEndDate").val(end.format('YYYY-MM-DD'));

            $('.statementReportRange').daterangepicker({
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
            $('.statementReportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#statementStartDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#statementEndDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.statementReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker
                    .endDate
                    .format('MMM D, YYYY'));

                refreshTable($("#statementDataTable"));
            });
        })

        $("#statementDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('maintain-suppliers.vendor_centre.statement', $supplier->supplier_code) !!}',
                data: function(data) {
                    data.from = $("#statementStartDate").val();
                    data.to = $("#statementEndDate").val();
                }
            },
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                }, {
                    data: 'memo',
                    name: 'memo',
                    orderable: false,
                },
                {
                    data: 'document_no',
                    name: 'document_no',
                    orderable: false,
                },
                {
                    data: 'suppreference',
                    name: 'suppreference',
                    orderable: false,
                },
                {
                    data: 'cu_invoice_number',
                    name: 'cu_invoice_number',
                    orderable: false,
                },
                {
                    data: 'debit',
                    name: 'debit',
                    orderable: false,
                    searchable: false,
                    className: "text-right"
                },
                {
                    data: 'credit',
                    name: 'credit',
                    orderable: false,
                    searchable: false,
                    className: "text-right"
                },
                {
                    data: 'running_balance',
                    name: 'running_balance',
                    orderable: false,
                    searchable: false,
                    className: "text-right"
                },
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();

                $("#total").html(json.total);
                $("#openingBalance").html(json.opening_balance);
            }
        });
    </script>
@endpush
