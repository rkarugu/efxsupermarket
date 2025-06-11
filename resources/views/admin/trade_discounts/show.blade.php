@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Trade Discount</h4>
                    <a class="btn btn-primary"
                        href="{{ route('maintain-suppliers.vendor_centre', $discount->supplier->supplier_code) }}">
                        <i class="fa fa-chevron-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table no-border">
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $discount->supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Invoice Number</th>
                                <td>{{ $discount->supplier_invoice_number }}</td>
                            </tr>
                            <tr>
                                <th>Invoice Date</th>
                                <td>{{ $discount->invoice_date }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $discount->description }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <h4>Items</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Cost</th>
                            <th>Discount Type</th>
                            <th>Discount Value</th>
                            <th>Item Quantity</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($discount->items as $item)
                            <tr>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ $item->item_cost }}</td>
                                <td>{{ $item->discount_type }}</td>
                                <td>{{ $item->discount_value }}</td>
                                <td>{{ $item->item_quantity }}</td>
                                <td class="text-right">{{ manageAmountFormat($item->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total</th>
                            <th class="text-right">{{ manageAmountFormat($discount->items->sum('amount')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection
