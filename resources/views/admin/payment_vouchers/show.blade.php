@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h4 class="box-title flex-groow-1">Payment Voucher Details</h4>
                    <div class="text-right">
                        <a href="{{ route('payment-vouchers.index') }}" class="btn btn-primary">
                            <i class="fa fa-chevron-left"></i> Back to Vouchers
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-header with-border">
                @include('message')
                <div class="row">
                    <div class="col-sm-9">
                        <p>
                            <strong>Voucher No: {{ $voucher->number }}</strong>
                        </p>
                        <p>
                            <strong>Supplier: {{ $voucher->supplier->supplier_code }} -
                                {{ $voucher->supplier->name }}</strong>
                        </p>
                        <p>
                            <strong>Account: {{ $voucher->account->account_code }} -
                                {{ $voucher->account->account_name }}</strong>
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-right">
                            <p class="d-flex justify-content-between"><strong>Supplier Balance:</strong>
                                <strong>{{ manageAmountFormat($balance = $voucher->supplier->balance()) }}</strong>
                            </p>
                            <p class="d-flex justify-content-between"><strong>Pending GRNs:</strong>
                                <a href="{{ route('maintain-suppliers.vendor_centre', $voucher->supplier->supplier_code) }}#grn"
                                    target="_blank">
                                    <strong>{{ manageAmountFormat($grnsValue = $voucher->supplier->grnsValue('46')) }}</strong>
                                </a>
                            </p>
                            <p class="d-flex justify-content-between"><strong>Stock Value:</strong>
                                <a href="{{ route('maintain-suppliers.vendor_centre', $voucher->supplier->supplier_code) }}#stockBalance"
                                    target="_blank">
                                    <strong>{{ manageAmountFormat($stockValue = $voucher->supplier->stockValue('46')) }}</strong>
                                </a>
                            </p>
                            <p class="d-flex justify-content-between"><strong>Payable Amount:</strong>
                                <strong>{{ manageAmountFormat($balance + $grnsValue - $stockValue) }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('payment-vouchers.confirm', $voucher->id) }}" method="POST" class="validate-form">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-hover dataTable">
                                <tbody>
                                    <tr>
                                        <th colspan="3">Cheques</th>
                                    </tr>
                                    @foreach ($voucher->cheques as $cheque)
                                        <tr>
                                            <td>{{ $cheque->number }}</td>
                                            <td>{{ $cheque->created_at->format('d/m/Y') }}</td>
                                            <td class="text-right">{{ manageAmountFormat($cheque->amount) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"></td>
                                        <th class="text-right">{{ manageAmountFormat($voucher->cheques->sum('amount')) }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-center">
                                Confirmed Amount
                                <h3 id="confirmedAmount">0.00</h3>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover" id="voucherItemsTable">
                        <tbody>
                            <tr>
                                <th colspan="11">Voucher Items</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Invoice Date</th>
                                <th>LPO No.</th>
                                <th>Reference</th>
                                <th>Cu Invoice No.</th>
                                <th>GRN No.</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">W/Hold Tax</th>
                                <th class="text-right">Credit Note</th>
                                <th class="text-right">Paid</th>
                                <th></th>
                            </tr>
                            @foreach ($voucher->voucherItems as $voucherItem)
                                @if ($voucherItem->payable_type == 'invoice')
                                    @include('admin.payment_vouchers.partials.invoices')
                                @elseif($voucherItem->payable_type == 'advance')
                                    @include('admin.payment_vouchers.partials.advance')
                                @elseif($voucherItem->payable_type == 'bill')
                                    @include('admin.payment_vouchers.partials.bill')
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="7"></td>
                                <th class="text-right"></th>
                                <th class="text-right"></th>
                                <th class="text-right">{{ manageAmountFormat($voucher->voucherItems->sum('amount')) }}
                                </th>
                                <th class="text-right"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        @if (can('approve-voucher', 'payment-vouchers') &&
                                !is_null($voucher->confirmed_by) &&
                                !is_null($voucher->confirmation_approval_by))
                            <button class="btn btn-primary" type="button" data-toggle="vouchers"
                                data-target="#approve{{ $voucher->id }}" data-action="approve">
                                <span class="span-action" data-toggle="tooltip" title="Approve" style="cursor: pointer;">
                                    <i class="fa fa-check-circle"></i> Approve Voucher
                                </span>
                            </button>
                        @endif
                        @if (is_null($voucher->confirmed_by) || is_null($voucher->confirmation_approval_by))
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i>
                                Confirm Details
                            </button>
                        @endif
                    </div>
                </div>
            </form>
            <form id="approve{{ $voucher->id }}" action="{{ route('payment-vouchers.approve', $voucher->id) }}"
                style="display: none" method="post">
                @csrf()
            </form>
        </div>
        @if (!$voucher->isAdvancePayment() && !$voucher->isBillPayment())
            <div class="box box-primary">
                <div class="box-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#overstockTab" data-toggle="tab">Overstock</a></li>
                        <li><a href="#missingStockTab" data-toggle="tab">Missing Items</a></li>
                        <li><a href="#slowstockTab" data-toggle="tab">Slow Moving Stock</a></li>
                        <li><a href="#deadstockTab" data-toggle="tab">Dead Stock</a></li>
                        <li><a href="#stockMovementTab" data-toggle="tab">Stock Movements</a></li>
                        <li><a href="#salesTab" data-toggle="tab">Sales Movement</a></li>
                        <li><a href="#pendingRTSTab" data-toggle="tab">Pending RTS</a></li>
                        <li><a href="#priceDropDemandsTab" data-toggle="tab">Price Drop Demands</a></li>
                        <li><a href="#pendingDiscTab" data-toggle="tab">Pending Trade Discounts</a></li>
                        <li><a href="#grnInvoiceVarianceTab" data-toggle="tab">GRN/Invoice Variance</a></li>
                        <li><a href="#invoiceAgingTab" data-toggle="tab">Invoice Ageing</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="overstockTab" class="tab-pane active">
                            @include('admin.payment_vouchers.partials.overstocks', ['items' => $items])
                        </div>
                        <div id="missingStockTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.missingstocks')
                        </div>
                        <div id="slowstockTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.slowstocks', ['items' => $items])
                        </div>
                        <div id="deadstockTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.deadstocks', ['items' => $items])
                        </div>
                        <div id="stockMovementTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.stock_movements', ['items' => $grns])
                        </div>
                        <div id="salesTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.sales', ['items' => $items])
                        </div>
                        <div id="pendingRTSTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.pending_returns', [
                                'items' => $items,
                            ])
                        </div>
                        <div id="priceDropDemandsTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.pricedrop_demands', [
                                'items' => $invoices,
                            ])
                        </div>
                        <div id="pendingDiscTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.pending_discounts', [
                                'items' => $invoices,
                            ])
                        </div>
                        <div id="grnInvoiceVarianceTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.grn_invoice_variance', [
                                'items' => $invoices,
                            ])
                        </div>
                        <div id="invoiceAgingTab" class="tab-pane">
                            @include('admin.payment_vouchers.partials.invoice_aging', [
                                'items' => $invoices,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <x-document-view />
    </section>
@endsection
@push('styles')
@endpush
@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .loadingIndicator {
            margin-left: 10px;
            color: #007bff;
            font-weight: bold;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 5px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $('#voucherItemsTable tbody').on('click', 'td.details-control', function() {
                let tr = $(this).closest('tr');
                let row = tr.next('tr');
                let icon = $(this).find('i');

                if (row.is(':visible')) {
                    row.hide();
                    icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    row.show();
                    icon.addClass('fa-minus-circle').removeClass('fa-plus-circle');
                }
            });

            $('[data-toggle="vouchers"]').on('click', function() {
                let action = $(this).data('action');
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to ' + action + ' voucher?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, I Confirm',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            });

            $(".items").change(function() {
                calculateConfirmed()
            });

            @if (!is_null($voucher->confirmed_by) && !is_null($voucher->confirmation_approval_by))
                calculateConfirmed(true);
            @endif
        });

        function calculateConfirmed(disabled = false) {
            let confirmedAmount = 0;

            $(disabled ? ".items:checked" : ".items:checked:not(:disabled)").each(function(index, item) {
                confirmedAmount += Number($(item).val());
            });

            $("#confirmedAmount").text(confirmedAmount.formatMoney());
        }
    </script>
@endpush
