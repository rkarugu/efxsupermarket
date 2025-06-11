@extends('layouts.admin.admin')

<style>
    .bg-red {
        background-color: red;
    }
</style>

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> My Suppliers </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="d-flex justify-content-end" style="margin-bottom: 10px">
                    <a href="{{ route('suppliers-overview.suppliers-print') }}" class="btn btn-primary btn-sm">Export Excel</a>
                </div>
                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Supplier Code</th>
                            <th>Supplier Name</th>
                            <th>Payment Terms</th>
                            <th>Stock Value</th>
                            <th>Payable Amount</th>
                            <th>Credit Limit</th>
                            <th >Total Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $i => $supplier)   
                            <tr @if ($supplier->payable_amount > 0) class="bg-red" @endif>
                                <th>{{ ++$i }}</th>
                                <td>{{ $supplier->supplier_code }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->paymentTerm?->term_description }}</td>
                                <td>{{ number_format($supplier->stock_value, 2) }}</td>
                                <td>{{ number_format($supplier->payable_amount, 2) }}</td>
                                <td>{{ number_format($supplier->credit_limit, 2) }}</td>
                                <td>{{ number_format($supplier->supp_trans_sum_total_amount_inc_vat, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("body").addClass('sidebar-collapse');
    </script>
@endpush
