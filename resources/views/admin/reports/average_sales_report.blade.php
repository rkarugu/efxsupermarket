@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Average Sales VS Max Stock Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            {{-- <div class="box-header with-border">
                <h3 class="box-title">Average Sales VS Max Stock </h3>
            </div> --}}
            <div class="box-body">
                <form action="{{ route('inventory-reports.average-sales-report.index') }}" method="get">
                    <input type="hidden" id="startDate" name="from">
                    <input type="hidden" id="endDate" name="to">
                    <input type="hidden" id="action" name="action">

                    <div class="row">
                        <div class="col-sm-3">
                            <div id="reportRange" class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select id="supplier" name="supplier" class="form-control">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select id="location" name="location" class="form-control">
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" @selected($location->id == 46)>
                                        {{ $location->location_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" name="action" value="excel">
                                    <i class="fa fa-file-alt"></i> Excel
                                </button>

                                <button type="submit" class="btn btn-primary" name="action" value="download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered" id="averageSalesDataTable">
                    <thead>
                        <tr style="font-size: 12px">
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Bin</th>
                            <th>Current Max Stock</th>
                            <th>Current Re-Order Level</th>
                            <th>Opening Stock</th>
                            <th>Purchases</th>
                            <th>Transfers In</th>
                            <th>Transfers Out</th>
                            <th>Sales</th>
                            <th>Returns</th>
                            <th>Pack Sales</th>
                            <th>NET SALES</th>
                            <th>STOCK AT HAND</th>
                            <th>LPO Qty</th>
                            <th>Over Stock</th>
                            <th>Suggested Max Stock</th>
                            <th>Suggested Reorder Level</th>
                            <th>Users</th>
                            <th>Suppliers</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet"
        href="https://datatables.net/release-datatables/extensions/FixedColumns/css/fixedColumns.bootstrap.css">
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
    <script src="https://datatables.net/release-datatables/extensions/FixedColumns/js/dataTables.fixedColumns.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            let start = moment().subtract(30, 'days');
            let end = moment();
            $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));

            $("#supplier, #location").select2();

            $("#supplier, #location").change(function() {
                refreshTable();
            })

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

                refreshTable();
            });

            $("#averageSalesDataTable").DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollY: "600px",
                fixedColumns: {
                    leftColumns: 4
                },
                order: [
                    [14, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('inventory-reports.average-sales-report.index') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                        data.supplier = $("#supplier").val();
                        data.location = $("#location").val();
                    }
                },
                columns: [{
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'category.category_description',
                        name: 'category.category_description'
                    },
                    {
                        data: 'bin_title',
                        name: 'bin.bin_title'
                    },
                    {
                        data: 'max_stock',
                        name: 'max_stocks.max_stock'
                    },
                    {
                        data: 're_order_level',
                        name: 'max_stocks.re_order_level'
                    },
                    {
                        data: 'opening_stock_count',
                        name: 'opening_stocks.opening_stock_count'
                    },
                    {
                        data: 'purchases_count',
                        name: 'purchases.purchases_count'
                    },
                    {
                        data: 'transfers_in_count',
                        name: 'transfers_in.transfers_in_count'
                    },
                    {
                        data: 'transfers_out_count',
                        name: 'transfers_out.transfers_out_count'
                    },
                    {
                        data: 'excl_total_sales',
                        name: 'excl_sales.excl_total_sales'
                    },
                    {
                        data: 'returns_count',
                        name: 'returns.returns_count'
                    },
                    {
                        data: 'pack_sales',
                        name: 'packs.pack_sales'
                    },
                    {
                        data: 'total_sales',
                        name: 'sales.total_sales'
                    },
                    {
                        data: 'qoh',
                        name: 'moves.quantity',
                    },
                    {
                        data: 'qty_on_order',
                        name: 'lpo.qty_on_order',
                    },
                    {
                        data: 'variance',
                        name: 'variance',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'suggested_max_stock',
                        name: 'suggested_max_stock',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'suggested_reorder',
                        name: 'suggested_reorder',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'users',
                        name: 'users',
                        searchable: false,
                        orderable: false,
                        width: "300px"
                    },
                    {
                        data: 'suppliers',
                        name: 'suppliers',
                        searchable: false,
                        orderable: false,
                        width: "300px"
                    },
                ]
            })
        })

        function refreshTable() {
            $("#averageSalesDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
