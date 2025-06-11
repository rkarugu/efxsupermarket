@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Max Stock Report
                    </h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <form action="{{ route('inventory-reports.max-stock-report.index') }}" method="get">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group" style="margin: 0">
                                <div class="row">
                                    <label for="supplier" class="col-sm-4">Supplier</label>
                                    <div class="col-sm-8">
                                        <select name="supplier" id="supplier" class="form-control">
                                            <option value="">Select Option</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
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
                <table class="table table-bordered" id="maxStockDataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ITEM CODE</th>
                            <th>ITEM NAME</th>
                            <th>CATEGORY</th>
                            <th>BIN</th>
                            <th>MAX STOCK</th>
                            <th>RE-ORDER LEVEL</th>
                            <th>QoH</th>
                            <th>Sales Qty(7 Days)</th>
                            <th>Sales Qty(30 Days)</th>
                            <th>SUPPLIERS</th>
                            <th>PROCUREMENT USER</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#supplier").select2();

            $("#supplier").change(function() {
                refreshTable();
            });

            $("#maxStockDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('inventory-reports.max-stock-report.index') !!}',
                    data: function(data) {
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
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
                        data: 'quantity',
                        name: 'qoh.quantity'
                    },
                    {
                        data: 'sales_qty_7',
                        name: 'sales_7.sales_qty_7'
                    },
                    {
                        data: 'sales_qty_30',
                        name: 'sales_30.sales_qty_30'
                    },
                    {
                        data: 'suppliers',
                        name: 'suppliers',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'users',
                        name: 'users',
                        searchable: false,
                        orderable: false,
                    },
                ]
            })
        })

        function refreshTable() {
            $("#maxStockDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
