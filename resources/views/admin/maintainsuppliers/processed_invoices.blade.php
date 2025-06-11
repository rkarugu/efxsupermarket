@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border  no-padding-h-b">
                <h3 class="box-title">Processed Invoices</h3>
                @include('message')
            </div>
            <div class="box-body">
                <form method="GET" action="{{ route('maintain-suppliers.processed_invoices') }}">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" id="startDate" name="from">
                            <input type="hidden" id="endDate" name="to">
                            <div class="form-group">
                                <label for="reportRange">Dates</label>
                                <div id="reportRange" class="reportRange">
                                    <i class="fa fa-calendar" style="padding:8px"></i>
                                    <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                    <i class="fa fa-caret-down" style="padding:8px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <select class="form-control" name="supplier" id="supplier">
                                    <option value="">Select Option</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_code }}">
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <select class="form-control" name="location" id="location">
                                    <option value="">Select Option</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">
                                            {{ $location->location_name }} ({{ $location->location_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-hover" id="processedInvoicesDataTable">
                    <thead>
                        <th>S.No.</th>
                        <th>Date Processed</th>
                        <th>Order No</th>
                        <th>Supplier</th>
                        <th>Store Location</th>
                        <th>Bin Location</th>
                        <th>Supplier Invoice No</th>
                        <th>CU Invoice No</th>
                        <th>Processed By</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </thead>
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
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#supplier, #location").select2()

            $("#supplier, #location").change(function() {
                refreshTable();
            })

            let start = moment().subtract(59, 'days');
            let end = moment();

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
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
        })

        $("#processedInvoicesDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('maintain-suppliers.processed_invoices') !!}',
                data: function(data) {
                    data.from = $("#startDate").val();
                    data.to = $("#endDate").val();
                    data.supplier_code = $("#supplier").val();
                    data.location = $("#location").val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                }, 
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'purchase_order.purchase_no',
                    name: 'purchaseOrder.purchase_no',
                },
                {
                    data: 'supplier.name',
                    name: 'supplier.name',
                },
                {
                    data: 'purchase_order.store_location.location_name',
                    name: 'purchaseOrder.storeLocation.location_name',
                },
                {
                    data: 'purchase_order.uom.title',
                    name: 'purchaseOrder.uom.title',
                },
                {
                    data: 'document_no',
                    name: 'document_no',
                },
                {
                    data: 'cu_invoice_number',
                    name: 'cu_invoice_number',
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                },
                {
                    data: 'total_amount_inc_vat',
                    name: 'total_amount_inc_vat',
                    className: 'text-right',
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    searchable: false,
                    orderable: false,
                },
            ]
        });

        function refreshTable() {
            $("#processedInvoicesDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
