@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="box-title">Withholding Tax Payments Report</h4>
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <select name="account" id="account" class="form-control">
                                    <option value="">Show All Accounts</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
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
                <table class="table table-bordered" id="paymentVouchersDataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Voucher No</th>
                            <th>Cheque No</th>
                            <th>Memo</th>
                            <th>Account</th>
                            <th>Withholding File</th>
                            <th>Date Paid</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-right">Total</th>
                            <th id="totalAmount" class="text-right"></th>
                            <td></td>
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
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $("select.form-control").select2();

            $("#account").change(function() {
                refreshTable();
            });

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

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable();
            });

            let table = $("#paymentVouchersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [6, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('withholding-tax-payments-report.index') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                        data.account = $("#account").val();
                    }
                },
                columns: [{
                    data: "id",
                    name: "id",
                }, {
                    data: "number",
                    name: "number",
                }, {
                    data: "cheque_number",
                    name: "cheque_number",
                }, {
                    data: "memo",
                    name: "memo",
                }, {
                    data: "account_name",
                    name: "accounts.account_name",
                }, {
                    data: "withholding_file_no",
                    name: "withholding_files.file_no",
                }, {
                    data: "payment_date",
                    name: "vouchers.payment_date",
                }, {
                    data: "amount",
                    name: "amount",
                    className: "text-right",
                }, {
                    data: "actions",
                    name: "actions",
                    className: "text-center"
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalAmount").html(json.total_amount);
                }
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        });

        function refreshTable() {
            $("#paymentVouchersDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
