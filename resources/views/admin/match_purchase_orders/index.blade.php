@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h4 class="box-title">Local Purchase Orders</h4>
                </div>
            </div>
            <div class="box-body">
                <table class="table" id="advancePaymentsDataTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date Created</th>
                            <th>LPO No.</th>
                            <th>Supplier</th>
                            <th>Prepared By</th>
                            <th class="text-right">VAT</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" tabindex="-1" role="dialog" id="matchPurchaseOrders">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Match Purchase Orders</h4>
                </div>
                <form action="{{ route('match-purchase-orders.store') }}" method="POST" class="validate-form">
                    @csrf
                    <input type="hidden" name="mother_lpo" id="motherLpo">
                    <div class="modal-body">
                        <table class="table" id="lpoDataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Order Date</th>
                                    <th>LPO No.</th>
                                    <th>Branch</th>
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
                            Match
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".table").on('click', '.match-lpo', function(e) {
                e.preventDefault();

                let motherLPO = $(this).data('mother-lpo');
                $("#motherLpo").val(motherLPO);
                $("#matchPurchaseOrders").modal("show");
            });

            let table = $("#advancePaymentsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('match-purchase-orders.index') !!}',
                    data: function(data) {

                    }
                },
                columns: [{
                        className: 'details-control',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '<i class="fa fa-plus-circle" style="cursor: pointer; font-size: 16px;"></i>',
                        title: '',
                        width: '20px',
                    }, {
                        data: "created_at",
                        name: "created_at",
                    },
                    {
                        data: "purchase_no",
                        name: "purchase_no",
                    },
                    {
                        data: "supplier.name",
                        name: "supplier.name",
                    },
                    {
                        data: "user.name",
                        name: "user.name",
                    },
                    {
                        data: "vat_amount",
                        name: "vat_amount",
                        searchable: false,
                        orderable: false,
                        className: "text-right",
                    },
                    {
                        data: "total_amount",
                        name: "total_amount",
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

            $('#advancePaymentsDataTable tbody').on('click', 'td.details-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);
                let icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    rowDetails(row.data()).then(table => {row.child(table).show()})                    
                    tr.addClass('shown');
                    icon.addClass('fa-minus-circle').removeClass('fa-plus-circle');
                }
            });

            $("#lpoDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                ajax: {
                    url: '{!! route('match-purchase-orders.orders') !!}',
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
        });

        async function rowDetails(data) {
            let table = `<table class="table table-bordered table-striped" style="margin-top:10px">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>LPO No.</th>
                            <th>Branch</th>
                            <th>Prepared By</th>
                            <th class="text-right">VAT</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>`;

            try {
                const response = await $.ajax({
                    url: "{{ route('match-purchase-orders.children') }}",
                    data: {
                        mother: data.id
                    }
                });

                response.orders.forEach(item => {
                    table += `<tr>
                        <td>${moment(item.created_at).format('YYYY-MM-DD')}</td>
                        <td>${item.purchase_no}</td>
                        <td>${item.branch.name}</td>
                        <td>${item.user.name}</td>
                        <td class="text-right">${Number(item.vat_amount).formatMoney()}</td>
                        <td class="text-right">${Number(item.total_amount).formatMoney()}</td>
                    </tr>`;
                });
            } catch (error) {
                console.error(error);
                return `<p>Error fetching data.</p>`;
            }

            table += `</tbody>
              </table>`;

            return table;
        }
    </script>
@endpush
