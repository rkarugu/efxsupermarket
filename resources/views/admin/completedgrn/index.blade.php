@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <h3 class="box-title">Completed GRNs</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('completed-grn.index') }}">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <select name="location" id="location" class="form-control" @disabled(!can('view-per-branch', 'maintain-items'))>
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected($location->id == auth()->user()->wa_location_and_store_id)>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <select name="supplier" id="supplier" class="form-control">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </form>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-hover" id="completedGrnsDataTable">
                    <thead>
                        <tr>
                            <th>GRN No</th>
                            <th>Date Received</th>
                            <th>Order No</th>
                            <th>Received By</th>
                            <th>Supplier</th>
                            <th>Store Location</th>
                            {{-- <th>Bin Location</th> --}}
                            <th>Supplier Invoice No</th>
                            <th>CU Invoice No</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="modal fade" id="invoicePreviewModal" tabindex="-1" role="dialog" aria-labelledby="invoicePreviewModal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="invoicePreviewModalLabel" style="font-size: 14px;font-weight:bold"></h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <iframe src="" width="100%" height="600px"></iframe>
                        <p class="text-danger">
                            <strong>Note:</strong> By clicking on the confirm button you acknowledge to have sent the
                            correct supplier physical documents to Accounts Payable
                        </p>
                    </div>
                    <form id="invoicePreviewForm" method="post">
                        @csrf
                        @method('put')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                Confirm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <x-document-view />
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .row-action {
            display: inline-block;
            margin-left: 5px;
        }

        #loadingIndicator {
            margin-left: 10px;
            color: #007bff;
            font-weight: bold;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 5px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            $("#location, #supplier").select2();
            $("#location, #supplier").change(function() {
                reloadTable()
            });

            var table = $("#completedGrnsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('completed-grn.index') !!}',
                    data: function(data) {
                        data.location = $("#location").val();
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                    data: 'grn_number',
                    name: 'grn_number'
                }, {
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
                    data: 'total_amount',
                    name: 'total_amount',
                    className: 'text-right',
                    searchable: false,
                    orderable: false,
                }, {
                    data: 'is_printed',
                    name: 'is_printed',
                    searchable: false,
                    orderable: false,
                }, {
                    data: 'actions',
                    name: 'actions',
                    searchable: false,
                    orderable: false,
                    width: "125px",
                    className: "text-center"
                }, ]
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $("body").on('click', '[data-toggle="invoice"]', function(e) {
                e.preventDefault()
                $('#invoicePreviewModal').find('iframe').attr('src', '');
                let docUrl = $(this).data('url');
                let docTitle = $(this).data('title');
                let action = $(this).data('action');

                $("#invoicePreviewForm").prop('action', action);

                $('#invoicePreviewModal').find('iframe').attr('src', docUrl);
                $('#invoicePreviewModalLabel').text(docTitle);
                $('#invoicePreviewModal').modal('show');
            });
        });

        function reloadTable() {
            $("#completedGrnsDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
