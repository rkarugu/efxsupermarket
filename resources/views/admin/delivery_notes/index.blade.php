@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <h4 class="box-title">Purchase Orders</h4>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped" id="ordersDataTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>LPO No.</th>
                            <th>Branch</th>
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
                    url: '{!! route('delivery-notes.index') !!}',
                },
                columns: [{
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
                        data: 'vat_amount',
                        name: 'vat_amount',
                        orderable: false,
                        searchable: false,
                        className: "text-right"
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
                    },
                ]
            })
        });
    </script>
@endpush
