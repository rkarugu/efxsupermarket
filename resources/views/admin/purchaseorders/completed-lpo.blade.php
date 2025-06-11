@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex justify-content-between">
                    <h4 class="box-title">Completed Purchase Orders</h4>
                </div>
            </div>
            <div class="box-header with-border">
                <form>
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group col-sm-3">
                                <label>From</label>
                                <input type="date" class="form-control" name="from" id="from"
                                    value="{{ request()->from }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>To</label>
                                <input type="date" class="form-control" name="to" id="to"
                                    value="{{ request()->to }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Store Location </label>
                                <select name="store" id="store" class="form-control mlselec6t">
                                    <option value="" selected disabled> Select Store Location</option>
                                    @foreach (getStoreLocationDropdown() as $index => $store)
                                        <option value="{{ $index }}"
                                            {{ request()->store == $index ? 'selected' : '' }}>
                                            {{ $store }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Supplier </label>
                                <select name="supplier" id="supplier" class="form-control mlselec6t">
                                    <option value="" selected disabled> Select Supplier</option>
                                    @foreach (getSupplierDropdown() as $index => $supplier)
                                        <option value="{{ $index }}"
                                            {{ request()->supplier == $index ? 'selected' : '' }}>
                                            {{ $supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label style="display:block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" value="Filter">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="purchaseOrdersDataTable">
                        <thead>
                            <tr>
                                <th>Order No</th>
                                <th>Order date</th>
                                <th>Initiated By</th>
                                <th>Supplier</th>
                                <th>Branch</th>
                                <th>LPO Type</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th style="width: 180px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .bg-aqua a {
            color: #fff
        }

        .actions {
            text-align: center
        }

        .actions a {
            margin-left: 5px;
            font-size: 17px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("select.mlselec6t").select2();

            var table = $("#purchaseOrdersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('purchase-orders.orders') !!}',
                    data: function(data) {
                        data.supplier = $("#supplier").val();
                        data.store = $("#store").val();
                        data.status = 'completed';
                        data.from = $("#from").val();
                        data.to = $("#to").val();
                    }
                },
                columns: [{
                        data: "purchase_no",
                        name: "purchase_no"
                    },
                    {
                        data: "purchase_date",
                        name: "purchase_date"
                    },
                    {
                        data: "employee_name",
                        name: "employees.name"
                    },
                    {
                        data: "supplier.name",
                        name: "supplier.name"
                    },
                    {
                        data: "store_location.location_name",
                        name: "storeLocation.location_name"
                    },
                    {
                        data: "lpo_type",
                        name: "lpo_type"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: "total_amount",
                        name: "total_amount",
                        className: "text-right",
                        searchable: false,
                    },
                    {
                        data: "actions",
                        name: "actions",
                        className: "actions",
                        searchable: false,
                        orderable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                }
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        });

        function refreshTable() {
            $("#purchaseOrdersDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
