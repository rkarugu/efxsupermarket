@extends('layouts.admin.admin')

@section('content')
<div class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between">
                <h3 class="box-title with-border">Invoice Balancing Report</h3>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('sales-and-receivables-reports.invoice-balancing-report') }}" method="get">
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
                        <button type="submit" class="btn btn-primary" name="action" value="excel">
                            <i class="fa fa-file-alt"></i> Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <table class="table table-bordered" id="invoiceBalancingReportDataTable">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th class="text-right">Invoice Amount</th>
                        <th class="text-right">Stocks Amount</th>
                        <th class="text-right">Debtors Amount</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<style>
    .reportRange {
        display: flex;
        align-content: center;
        justify-content: stretch;
        border: 1px solid #eee;
        cursor: pointer;
        height: 35px;
    }

    .text-danger {
        color: #f80202;
    }
</style>
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
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

        $("#invoiceBalancingReportDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('
                sales - and - receivables - reports.invoice - balancing - report ') !!}',
                data: function(data) {
                    data.from = $("#startDate").val();
                    data.to = $("#endDate").val();
                }
            },
            columns: [{
                    data: 'requisition_no',
                    name: 'requisition_no'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'invoice_amount',
                    name: 'invoice_amount',
                    searchable: false,
                    orderable: false,
                    className: "text-right",
                },
                {
                    data: 'stocks_amount',
                    name: 'stocks_amount',
                    searchable: false,
                    orderable: false,
                    className: "text-right",
                },
                {
                    data: 'debtors_amount',
                    name: 'debtors_amount',
                    searchable: false,
                    orderable: false,
                    className: "text-right",
                },
            ]
        })
    })

    function refreshTable() {
        $("#invoiceBalancingReportDataTable").DataTable().ajax.reload();
    }
</script>
@endpush