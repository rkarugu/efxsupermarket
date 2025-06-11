@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <h4 class="box-title">Delivery Notes</h4>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped" id="ordersDataTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>LPO No.</th>
                            <th>GRN No.</th>
                            <th>Supplire Invoice No.</th>
                            <th>CU Invoice No.</th>
                            <th>Location</th>
                            <th>Supplier</th>
                            <th>Prepared By</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <x-document-view />
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#ordersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                ajax: {
                    url: '{!! route('delivery-notes-invoices.index') !!}',
                },
                columns: [{
                        data: 'delivery_date',
                        name: 'delivery_date',
                    },
                    {
                        data: 'purchase_no',
                        name: 'orders.purchase_no',
                    },
                    {
                        data: 'grn_number',
                        name: 'grn_number',
                    },
                    {
                        data: 'supplier_invoice_no',
                        name: 'supplier_invoice_no',
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number',
                    },
                    {
                        data: 'location_name',
                        name: 'locations.location_name',
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name',
                    },
                    {
                        data: 'received_by',
                        name: 'received_by',
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderable: false,
                        searchable: false,
                        className: "text-right"
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ]
            })
        });
    </script>
@endpush
