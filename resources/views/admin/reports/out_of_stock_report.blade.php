@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <form action="{{ route('inventory-reports.out-of-stock-report') }}" method="get">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <select name="location" id="location" class="form-control">
                                    <option value="">Select All</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" {{ $location->id == 46 ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="user">User</label>
                                <select name="user" id="user" class="form-control">
                                    <option value="">Select All</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label style="display: block">&nbsp;</label>
                                <button class="btn btn-primary" type="submit" name="action" value="excel">
                                    <i class="fa fa-document"></i> Excel
                                </button>
                                <button class="btn btn-primary" type="submit" name="action" value="pdf">
                                    <i class="fa fa-document"></i> Print PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <table class="table table-striped" id="outOfStockDataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ITEM CODE</th>
                            <th>ITEM NAME</th>
                            <th>CATEGORY</th>
                            <th>MAX STOCK</th>
                            <th>RE-ORDER LEVEL</th>
                            <th>QoH</th>
                            <th>QOO</th>
                            <th>Qty to Order</th>
                            <th>Sales Qty(7 Days)</th>
                            <th>Sales Qty(30 Days)</th>
                            <th>Sales Qty(30 - 180 Days)</th>
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
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            $("#location, #user").select2();

            $("#location, #user").change(function() {
                refreshTable();
            });

            $("#outOfStockDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('inventory-reports.out-of-stock-report') !!}',
                    data: function(data) {
                        data.location = $("#location").val();
                        data.user = $("#user").val();
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
                        data: 'max_stock',
                        name: 'max_stocks.max_stock'
                    },
                    {
                        data: 're_order_level',
                        name: 'max_stocks.re_order_level'
                    },
                    {
                        data: 'qty_on_hand',
                        name: 'qty_on_hand',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_on_order',
                        name: 'qty_on_order',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_to_order',
                        name: 'qty_to_order',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'sales_7_days',
                        name: 'sales_7_days',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'sales_30_days',
                        name: 'sales_30_days',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'sales_180_days',
                        name: 'sales_180_days',
                        searchable: false,
                        orderable: false,
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
            });
        });


        function refreshTable() {
            $("#outOfStockDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
