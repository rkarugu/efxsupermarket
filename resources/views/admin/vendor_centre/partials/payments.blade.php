<div style="padding:10px">
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <input type="hidden" id="paymentsStartDate">
                <input type="hidden" id="paymentsEndDate">
                <label for="">Select Dates</label>
                <div class="paymentsReportRange reportRange">
                    <i class="fa fa-calendar" style="padding:8px"></i>
                    <span class="flex-grow-1" style="padding:8px"></span>
                    <i class="fa fa-caret-down" style="padding:8px"></i>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped" id="paymentsDataTable">
        <thead>
            <tr>
                <th>Transaction Date</th>
                <th>Document No</th>
                <th>Bank File</th>
                <th>Payer Bank</th>
                <th>Payee Bank</th>
                <th>Payment Mode</th>
                <th>Amount</th>
            </tr>
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            let start = moment().subtract(90, 'days');
            let end = moment();
            $('.paymentsReportRange span, .completedGrnsReportRange span').html(start.format(
                    'MMM D, YYYY') +
                ' - ' + end.format('MMM D, YYYY'));

            $("#paymentsStartDate").val(start.format('YYYY-MM-DD'));
            $("#paymentsEndDate").val(end.format('YYYY-MM-DD'));

            $('.paymentsReportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'Last 90 Days': [moment().subtract(90, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });

            $("#paymentsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.payments', $supplier->supplier_code) !!}',
                    data: function(data) {
                        data.from = $("#paymentsStartDate").val();
                        data.to = $("#paymentsEndDate").val();
                    }
                },
                columns: [{
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'number',
                        name: 'payment_vouchers.number'
                    },
                    {
                        data: 'file_no',
                        name: 'files.file_no'
                    },
                    {
                        data: 'account_name',
                        name: 'accounts.account_name'
                    },
                    {
                        data: 'supplier.bank_name',
                        name: 'supplier.bank_name'
                    },
                    {
                        data: 'payment_mode.mode',
                        name: 'paymentMode.mode'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: "text-right"
                    },
                ],
            });
        })
    </script>
@endpush
