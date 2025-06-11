@extends('layouts.report')

@section('title', 'PROCESSED INVOICES')

@section('content')
    <table class="table table-bordered table-hover" id="processedInvoicesDataTable">
        <thead>
            <th>Posting Date</th>
            <th>LPO No.</th>
            <th>GRN No.</th>
            <th>GRN Date</th>
            <th>Invoice No.</th>
            <th>Supplier</th>
            <th>Supplier Invoice No</th>
            <th>Supplier Invoice Date</th>
            <th>CU Invoice No</th>
            <th>Prepared By</th>
            <th>Vat Amount</th>
            <th>Total Amount</th>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->created_at }}</td>
                    <td>{{ $invoice->lpo->purchase_no }}</td>
                    <td>{{ $invoice->grn_number }}</td>
                    <td>{{ $invoice->grn_date }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->supplier->name }}</td>
                    <td>{{ $invoice->supplier_invoice_number }}</td>
                    <td>{{ $invoice->supplier_invoice_date }}</td>
                    <td>{{ $invoice->cu_invoice_number }}</td>
                    <td>{{ $invoice->user->name }}</td>
                    <td class="text-right">{{ manageAmountFormat($invoice->vat_amount) }}</td>
                    <td class="text-right">{{ manageAmountFormat($invoice->amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
