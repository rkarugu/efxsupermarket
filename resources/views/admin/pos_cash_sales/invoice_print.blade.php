@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Invoice #{{ $data->sales_no }}</h3>
                    <button onclick="window.print()" class="btn btn-primary">Print</button>
                </div>
            </div>

            <div class="box-body">
                @if(isset($esd_details) && $esd_details && $esd_details->description === 'Invoice signing pending. Please try again later.')
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> {{ $esd_details->description }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h4>Customer Details</h4>
                        <p><strong>Name:</strong> {{ $data->customer }}</p>
                        <p><strong>Phone:</strong> {{ $data->customer_phone_number }}</p>
                        <p><strong>PIN:</strong> {{ $data->customer_pin }}</p>
                    </div>
                    <div class="col-md-6">
                        <h4>Invoice Details</h4>
                        <p><strong>Date:</strong> {{ $data->date }}</p>
                        <p><strong>Time:</strong> {{ $data->time }}</p>
                        <p><strong>Cashier:</strong> {{ $data->user->name }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->items as $item)
                                <tr>
                                    <td>{{ $item->item->title }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ number_format($item->selling_price, 2) }}</td>
                                    <td>{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Subtotal:</th>
                                <th>{{ number_format($data->items->sum('total'), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">VAT:</th>
                                <th>{{ number_format($data->items->sum('vat_amount'), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th>{{ number_format($data->items->sum('total') + $data->items->sum('vat_amount'), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4>Payment Details</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->title }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if(isset($esd_details) && $esd_details && $esd_details->verify_url)
                    <div class="row">
                        <div class="col-md-12">
                            <h4>ESD Details</h4>
                            <p><strong>Status:</strong> {{ $esd_details->description }}</p>
                            <p><strong>URL:</strong> <a href="{{ $esd_details->verify_url }}" target="_blank">Verify</a></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <style>
        @media print {
            .box-header-flex {
                display: none;
            }
            .box-header {
                border-bottom: none;
            }
            .alert {
                display: none;
            }
        }
    </style>
@endsection 