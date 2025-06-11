@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Till Direct Banking Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <form action="{{ route("sales-and-receivables-reports.$model") }}" method="get">
                    <input type="hidden" id="startDate" name="from">
                    <input type="hidden" id="endDate" name="to">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="row">
                                    <label for="" class="col-sm-4">Select Dates</label>
                                    <div id="reportRange" class="col-sm-8 reportRange">
                                        <i class="fa fa-calendar" style="padding:8px"></i>
                                        <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                        <i class="fa fa-caret-down" style="padding:8px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="row">
                                    <label for="channel" class="col-sm-4">Channel</label>
                                    <div class="col-sm-8">
                                        <select name="channel" id="channel" class="form-control select2">
                                            <option value="">Select Option</option>
                                            @foreach($channels as $channel)
                                                <option value="{{ $channel->title }}" {{ request()->channel == $channel->title ? 'selected' : '' }}>
                                                    {{ $channel->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="row">
                                    <label for="branch" class="col-sm-4">Branch</label>
                                    <div class="col-sm-8">
                                        <select name="branch" id="branch" class="form-control select2">
                                            <option value="">Select Branch</option>
                                            @foreach (getBranchesDropdown() as $key => $branch)
                                                <option value="{{ $key }}" {{ request()->branch == $key ? 'selected' : '' }}>{{ $branch }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary" name="action" value="pdf">
                                Print Pdf
                            </button>
                            <button type="submit" class="btn btn-primary" name="action" value="excel">
                                Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <table class="table table-striped" id="tenderEntriesDataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Channel</th>
                            <th>Customer No</th>
                            <th>Customer Name</th>
                            <th>User</th>
                            <th>Reference</th>
                            <th>Additional Info</th>
                            <th>Paid By</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="9">Total</th>
                            <th class="text-right" id="total"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
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
            $('body').addClass('sidebar-collapse');
            $(".select2").select2()

            $("#channel,#branch").change(function() {
                refreshTable();
            })

            let start = moment().subtract(30, 'days');
            let end = moment();

            $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $("#startDate").val(start.format('YYYY-MM-DD'));
            $("#endDate").val(end.format('YYYY-MM-DD'));

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

                    $("#tenderEntriesDataTable").DataTable().ajax.reload();
            });

            $("#tenderEntriesDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route("sales-and-receivables-reports.$model") !!}',
                data: function(data) {
                    data.from = $("#startDate").val();
                    data.to = $("#endDate").val();
                    data.branch = $("#branch").val();
                    data.channel = $("#channel").val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                }, {
                    data: 'trans_date',
                    name: 'trans_date',
                }, {
                    data: 'channel',
                    name: 'channel',
                },
                {
                    data: 'customer.customer_code',
                    name: 'customer.customer_code',
                },
                {
                    data: 'customer.customer_name',
                    name: 'customer.customer_name',
                },
                {
                    data: 'cashier.name',
                    name: 'cashier.name',
                },
                {
                    data: 'reference',
                    name: 'reference',
                },
                {
                    data: 'additional_info',
                    name: 'additional_info',
                },
                {
                    data: 'paid_by',
                    name: 'paid_by',
                },
                {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-right',
                },
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();

                $("#total").html(Number(json.total).formatMoney());
            }
        });
        })      


        function refreshTable() {
            $("#tenderEntriesDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
