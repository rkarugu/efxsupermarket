@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header" style="border-bottom: 1px solid #eee">
                @include('message')
                <div class="d-flex justify-content-between">
                    <h4>Vendor Information</h4>
                    <div class="text-right" style="margin: 5px 0">
                        <a href={{ route('maintain-suppliers.index') }} class="btn btn-primary">
                            <i class="fa fa-undo"></i>
                            Back
                        </a>
                        <a href={{ route('maintain-suppliers.payment_vouchers.create', [
                            'code' => $supplier->supplier_code,
                            'type' => 'advance',
                        ]) }}
                            class="btn btn-primary">
                            <i class="fa fa-coins"></i>
                            Pay Advance
                        </a>
                        <a href={{ route('maintain-suppliers.payment_vouchers.create', [
                            'code' => $supplier->supplier_code,
                            'type' => 'invoice',
                        ]) }}
                            class="btn btn-primary">
                            <i class="fa fa-file"></i>
                            Pay Invoice
                        </a>
                        <a href={{ route('maintain-suppliers.payment_vouchers.create', [
                            'code' => $supplier->supplier_code,
                            'type' => 'bill',
                        ]) }}
                            class="btn btn-primary">
                            <i class="fa fa-money-check-dollar"></i>
                            Pay Bill
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>Vendor</th>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>
                                    Address
                                </th>
                                <td>
                                    {{ $supplier->address }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Telephone
                                </th>
                                <td>
                                    {{ $supplier->telephone }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Email
                                </th>
                                <td>
                                    {{ $supplier->email }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-right">
                            <p class="d-flex justify-content-between"><strong>Supplier Balance:</strong>
                                <strong>{{ manageAmountFormat($balance = $supplier->balance) }}</strong>
                            </p>
                            <p class="d-flex justify-content-between"><strong>Pending GRNs:</strong>
                                <a href="{{ route('maintain-suppliers.vendor_centre', $supplier->supplier_code) }}#grn"
                                    target="_blank">
                                    <strong>{{ manageAmountFormat($grnsValue = $supplier->grnsValue('46')) }}</strong>
                                </a>
                            </p>
                            <p class="d-flex justify-content-between"><strong>Stock Value:</strong>
                                <a href="{{ route('maintain-suppliers.vendor_centre', $supplier->supplier_code) }}#stockBalance"
                                    target="_blank">
                                    (<strong>{{ manageAmountFormat($stockValue = $supplier->stockValue('46')) }}</strong>)
                                </a>
                            </p>
                            <p class="d-flex justify-content-between">
                                <strong>Payable Amount:</strong>
                                @php $payableAmount = $balance + $grnsValue - $stockValue @endphp
                                <strong
                                    style="border-top:1px solid #aaa; border-bottom:2px double #aaa; padding: 5px 0">{{ manageAmountFormat($payableAmount > 0 ? $payableAmount : 0) }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <ul class="nav nav-tabs" id="vendorCentreTabs">
                <li class="active"><a href="#payables" data-toggle="tab">Invoices</a></li>
                <li><a href="#grn" data-toggle="tab">GRNs</a></li>
                <li><a href="#payments" data-toggle="tab">Payments</a></li>
                <li><a href="#statement" data-toggle="tab">Statement</a></li>
                <li><a href="#price_list" data-toggle="tab">Price List</a></li>
                <li><a href="#stockBalance" data-toggle="tab">Stock Balances</a></li>
                <li><a href="#return-demands" data-toggle="tab">Return To Supplier</a></li>
                <li><a href="#price-demands" data-toggle="tab">Price Drop CN</a></li>
                <li><a href="#turnover-purchases" data-toggle="tab">Turnover Purchases</a></li>
                <li><a href="#turnover-sales" data-toggle="tab">Turnover Sales</a></li>
                <li><a href="#trade-discounts" data-toggle="tab">Trade Discounts</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="payables">
                    @include('admin.vendor_centre.partials.payables')
                </div>
                <div class="tab-pane" id="grn" style="padding: 10px;">
                    @include('admin.vendor_centre.partials.grn')
                </div>
                <div class="tab-pane" id="payments">
                    @include('admin.vendor_centre.partials.payments')
                </div>
                <div class="tab-pane" id="statement">
                    @include('admin.vendor_centre.partials.statement')
                </div>
                <div class="tab-pane" id="price_list">
                    @include('admin.vendor_centre.partials.price_list')
                </div>
                <div class="tab-pane" id="stockBalance">
                    @include('admin.vendor_centre.partials.stockBalance')
                </div>
                <div class="tab-pane" id="return-demands">
                    @include('admin.vendor_centre.partials.return-demands')
                </div>
                <div class="tab-pane" id="price-demands">
                    @include('admin.vendor_centre.partials.price-demands')
                </div>
                <div class="tab-pane" id="turnover-purchases">
                    @include('admin.vendor_centre.partials.turnover-purchases')
                </div>
                <div class="tab-pane" id="turnover-sales">
                    @include('admin.vendor_centre.partials.turnover-sales')
                </div>
                <div class="tab-pane" id="trade-discounts">
                    <x-vendor-centre.trade-discounts supplier-id="{{ $supplier->id }}" />
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }

        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endpush

@section('uniquepagescript')
    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ? 'https://unpkg.com/vue@3/dist/vue.esm-browser.js' : 'https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js' }}"
            }
        }
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <script>
        $("body").addClass('sidebar-collapse');

        $('.mtselect, .mselect').select2();

        $(document).ready(function() {
            const hash = window.location.hash;
            if (hash) {
                $('#vendorCentreTabs a[href="' + hash + '"]').tab('show');
            }
        });

        function refreshTable(table) {
            table.DataTable().ajax.reload();
        }
    </script>
@endpush
