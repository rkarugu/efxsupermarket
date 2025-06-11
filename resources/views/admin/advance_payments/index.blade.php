@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h4 class="box-title">Advance Payments</h4>
                    @if (can('add', 'advance-payments'))
                        <a href="#" data-toggle="modal" data-target="#approvedLpos" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                            Initiate Payment
                        </a>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <table class="table" id="advancePaymentsDataTable">
                    <thead>
                        <tr>
                            <th>Date Created</th>
                            <th>LPO No.</th>
                            <th>Supplier</th>
                            <th>Prepared By</th>
                            <th>Status</th>
                            <th>Date Paid</th>
                            <th class="text-right">VAT</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" tabindex="-1" role="dialog" id="approvedLpos">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Approved LPOs</h4>
                </div>
                <form action="{{ route('advance-payments.store') }}" method="POST" class="validate-form">
                    @csrf
                    <div class="modal-body">
                        <table class="table" id="lpoDataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Order Date</th>
                                    <th>LPO No.</th>
                                    <th>Branch</th>
                                    <th>Store Location</th>
                                    <th>Supplier</th>
                                    <th>Initiated By</th>
                                    <th class="text-right">Total Amount</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            &times;
                            Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>
                            Initiate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteAdvanceModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Advance</h4>
                </div>
                <form method="POST" id="advanceForm">
                    @csrf
                    @method('delete')
                    <div class="modal-body">
                        Are you sure you want to delete the Advance?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            &times;
                            Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle-o"></i>
                            Yes, Delete!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".table").on('click', '.destroy', function(e) {
                e.preventDefault();
                let action = $(this).data('url');
                $("#advanceForm").prop('action', action);

                $("#deleteAdvanceModal").modal('show');
            });

            let table = $("#advancePaymentsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('advance-payments.index') !!}',
                    data: function(data) {
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                        data: "created_at",
                        name: "created_at",
                    },
                    {
                        data: "lpo.purchase_no",
                        name: "lpo.purchase_no",
                    },
                    {
                        data: "supplier.name",
                        name: "supplier.name",
                    },
                    {
                        data: "prepared_by.name",
                        name: "preparedBy.name",
                    },
                    {
                        data: "status",
                        name: "status",
                    },
                    {
                        data: "paid_at",
                        name: "paid_at",
                    },
                    {
                        data: "vat_amount",
                        name: "vat_amount",
                        searchable: false,
                        orderable: false,
                        className: "text-right",
                    },
                    {
                        data: "amount",
                        name: "amount",
                        searchable: false,
                        orderable: false,
                        className: "text-right",
                    },
                    {
                        data: "actions",
                        name: "actions",
                        searchable: false,
                        orderable: false,
                        className: "text-center"
                    },
                ]
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $("#lpoDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                ajax: {
                    url: '{!! route('advance-payments.orders') !!}',
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                    },
                    {
                        data: 'purchase_no',
                        name: 'purchase_no',
                    },
                    {
                        data: 'branch.name',
                        name: 'branch.name',
                    },
                    {
                        data: 'store_location.location_name',
                        name: 'storeLocation.location_name',
                    },
                    {
                        data: 'supplier.name',
                        name: 'supplier.name',
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderable: false,
                        searchable: false,
                        className: "text-right"
                    },
                ]
            })
        })
    </script>
@endpush
