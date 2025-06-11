@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <h3 class="box-title">Pending GRNs</h3>
            </div>
            <div class="box-header with-border">
                <form>
                    <div class="row">
                        <div class="col-sm-3 ">
                            <div class="form-group">
                                <select class="form-control mlselec6t" name="supplier" id="supplier">
                                    <option selected value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected($supplier->id == request()->supplier)>
                                            {{ $supplier->name }} ({{ $supplier->supplier_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <select name="store" id="store" class="form-control mlselec6t">
                                    <option value="" selected>Select Store</option>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" @selected($store->id == request()->store)>
                                            {{ $store->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for=""></label>
                                <button type="submit" value="excel" name="download" class="btn btn-primary">
                                    <i class="fa fa-file excel"></i>
                                    Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-hover" id="pendingGrnsDataTable">
                    <thead>
                        <th>GRN No</th>
                        <th>Date Received</th>
                        <th>Order No</th>
                        <th>Received By</th>
                        <th>Supplier</th>
                        <th>Store Location</th>
                        <th>Supplier Invoice No.</th>
                        <th>CU Invoice No.</th>
                        <th class="text-right">Vat</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-center">Action</th>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-right">Sub Total</th>
                            <th class="text-right" id="pageTotal"></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="9" class="text-right">Grand Total</th>
                            <th class="text-right" id="grandTotal"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            $("#store, #supplier").select2();
            $("#store, #supplier").change(function() {
                refreshTable()
            });

            let table = $("#pendingGrnsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('pending-grns.index') !!}',
                    data: function(data) {
                        data.store = $("#store").val();
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    }, {
                        data: 'purchase_no',
                        name: 'orders.purchase_no'
                    }, {
                        data: 'received_by',
                        name: 'users.name'
                    }, {
                        data: 'supplier_name',
                        name: 'suppliers.name'
                    }, {
                        data: 'location_name',
                        name: 'locations.location_name'
                    }, {
                        data: 'supplier_invoice_no',
                        name: 'supplier_invoice_no'
                    }, {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    }, {
                        data: 'vat_amount',
                        name: 'vat_amount',
                        className: 'text-right',
                        searchable: false,
                        searchable: false,
                    }, {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: 'text-right',
                        searchable: false,
                        searchable: false,
                    }, {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false,
                        width: "80px",
                        className: "text-center"
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    var pageTotal = api.column(9, {page: 'current'}).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    $("#pageTotal").html(Number(pageTotal).formatMoney());
                    $("#grandTotal").html(json.grand_total);
                }
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        });

        function refreshTable() {
            $("#pendingGrnsDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
