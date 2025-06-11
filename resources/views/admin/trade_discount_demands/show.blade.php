@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Trade Discount</h4>
                    <div class="text-right">
                        @if (!$demand->credit_note_no)
                            <a class="btn btn-primary" href="{{ route('trade-discount-demands.edit', $demand) }}">
                                <i class="fa fa-arrow-right fa-lg"></i> Convert
                            </a>
                        @endif
                        <a class="btn btn-primary"
                            href="{{ route('maintain-suppliers.vendor_centre', $demand->supplier->supplier_code) }}">
                            <i class="fa fa-chevron-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table no-border">
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $demand->supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Demand No</th>
                                <td>{{ $demand->demand_no }}</td>
                            </tr>
                            <tr>
                                <th>Reference</th>
                                <td>{{ $demand->supplier_reference }}</td>
                            </tr>
                            <tr>
                                <th>CU Invoice No</th>
                                <td>{{ $demand->cu_invoice_number }}</td>
                            </tr>
                            <tr>
                                <th>Credit Note No</th>
                                <td>{{ $demand->credit_note_no }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <h4>Items</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demand->items as $item)
                            <tr>
                                <td>{{ $item->discount->id }}</td>
                                <td>{{ $item->discount->supplier_invoice_number }}</td>
                                <td>{{ $item->discount->invoice_date }}</td>
                                <td>{{ $item->discount->description }}</td>
                                <td class="text-right">{{ manageAmountFormat($item->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th class="text-right">{{ manageAmountFormat($demand->items->sum('amount')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection
