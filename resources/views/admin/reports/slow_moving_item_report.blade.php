@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Slow Moving tems (Stock at Hand > 0, Sales <= 5) Report</h3>
                </div>
            </div>

            <div class="box-body">
                <form action="{{ route('inventory-reports.slow-moving-items-report.index') }}" method="get">
                    <input type="hidden" id="startDate" name="from">
                    <input type="hidden" id="endDate" name="to">
                    <input type="hidden" id="action" name="action">

                    <div class="row">
                        <div class="col-sm-9">
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
                                <div class="col-sm-2">
                                    <select id="location" name="location" class="form-control">
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" @selected($location->id == request()->location ?? auth()->user()->wa_location_and_store_id)>
                                                {{ $location->location_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" id="sold" name="sold"
                                        placeholder="Maximum items Sold" value="{{ request()->sold }}">
                                </div>

                                @if ($user->role_id != 154)
                                    <div class="col-sm-2">
                                        <select id="user" name="user" class="form-control">
                                            <option value="" selected disabled>Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->user_id }}"
                                                    @if (request()->user == $user->user_id) selected @endif>
                                                    {{ $user->user_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-2">
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
                <table class="table table-bordered" id="slowMovingItemsDataTable">
                    <thead>
                        <tr style="font-size: 12px">
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Current Max Stock</th>
                            <th>Current Re-Order Level</th>
                            <th>Opening Stock</th>
                            <th>Purchases</th>
                            {{-- <th>Transfers In</th>
                            <th>Transfers Out</th> --}}
                            <th>Sales</th>
                            <th>Returns</th>
                            <th>Pack Sales</th>
                            <th>NET SALES</th>
                            <th>STOCK AT HAND</th>
                            <th>Qty to Order</th>
                            <th>LPO Qty</th>
                            <th>LPOs</th>
                            <th>Last LPO Date</th>
                            <th>Last GRN Date</th>
                            <th>Last Sales Date</th>
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
            let startDate = "{{ request()->from }}";
            let endDate = "{{ request()->to }}";
            let start = startDate ? moment(startDate) : moment().subtract(30, 'days');
            let end = endDate ? moment(endDate) : moment();

            $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));

            $("#supplier, #location, #user").select2();

            $("#supplier, #location, #user").change(function() {
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

            $("#slowMovingItemsDataTable").DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollY: "600px",
                fixedColumns: {
                    leftColumns: 2
                },
                order: [
                    [14, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('inventory-reports.slow-moving-items-report.index') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                        data.supplier = $("#supplier").val();
                        data.location = $("#location").val();
                        data.sold = $("#sold").val();
                        data.user = $("#user").val();
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
                        data: 'category',
                        name: 'categories.category_description'
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
                    // {
                    //     data: 'transfers_in_count',
                    //     name: 'transfers_in.transfers_in_count'
                    // },
                    // {
                    //     data: 'transfers_out_count',
                    //     name: 'transfers_out.transfers_out_count'
                    // },
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
                        name: 'qoh.quantity',
                    },
                    {
                        data: 'variance',
                        name: 'variance',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_on_order',
                        name: 'lpo.qty_on_order',
                    },
                    {
                        data: 'purchase_order_numbers',
                        name: 'purchase_order_numbers',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'last_lpo_date',
                        name: 'last_lpo_date',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'last_grn_date',
                        name: 'last_grn_date',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'last_sales_date',
                        name: 'last_sales_date',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'user_names',
                        name: 'suppliers.user_names',
                        searchable: false,
                        orderable: false,
                        width: "300px"
                    },
                    {
                        data: 'supplier_names',
                        name: 'suppliers.supplier_names',
                        searchable: false,
                        orderable: false,
                        width: "300px"
                    },
                ]
            })
        })

        function refreshTable() {
            $("#slowMovingItemsDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
