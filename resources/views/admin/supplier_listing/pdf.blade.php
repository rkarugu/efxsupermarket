@extends('layouts.report')

@section('title', 'SUPPLIER LISTING')

@section('content')
    <table class="table table-bordered table-hover" id="suppliersDataTable">
        <thead>
            <tr>
                <th>Supplier Code</th>
                <th>Supplier Name</th>
                <th>Address</th>
                <th>Telephone</th>
                <th>Email</th>
                <th>Supllier Since</th>
                <th>Payment Terms</th>
                <th class="text-right">Total Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->supplier_code }}</td>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->address }}</td>
                    <td>{{ $supplier->telephone }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->supplier_since }}</td>
                    <td>{{ $supplier->paymentTerm?->term_description }}</td>
                    <td class="text-right">{{ manageAmountFormat($supplier->supplier_balance) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="7">Grand Total </th>
                <th class="text-right" id="total">{{ manageAmountFormat($suppliers->sum('supplier_balance')) }}</th>
            </tr>
        </tfoot>
    </table>
@endsection
