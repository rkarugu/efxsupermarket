@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <form id="invoice-form" action="{{ route('maintain-suppliers.processed_invoices.update', $invoice->id) }}" method="POST">
            @csrf
            <div class="box box-primary">
                <div class="box-header with-border  no-padding-h-b">
                    <h3 class="box-title">{{ $invoice->supplier->name }}</h3>
                    @include('message')
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Invoice No.</th>
                            <td>{{ $invoice->invoice_number }}</td>
                            <th>LPO No.</th>
                            <td>{{ $invoice->lpo->purchase_no }}</td>
                            <th>LPO Date</th>
                            <td>{{ $invoice->lpo->created_at->format('Y-m-d') }}</td>
                            <th>GRN No.</th>
                            <td>{{ $invoice->grn_number }}</td>
                            <th>GRN Date</th>
                            <td>{{ $invoice->grn_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>Supplier Invoice No.</th>
                            <td>
                                @if (can('edit', 'suppliers-invoice'))
                                    <input type="text" class="form-control" id="supplier_invoice_number"
                                        name="supplier_invoice_number" value="{{ $invoice->supplier_invoice_number }}">
                                @else
                                    {{ $invoice->supplier_invoice_number }}
                                @endif
                            </td>
                            <th>CU Invoice No.</th>
                            <td>
                                @if (can('edit', 'suppliers-invoice'))
                                    <input type="text" class="form-control" id="cu_invoice_number"
                                        name="cu_invoice_number" value="{{ $invoice->cu_invoice_number }}">
                                @else
                                    {{ $invoice->cu_invoice_number }}
                                @endif
                            </td>
                            <th>Supplier Invoice Date</th>
                            <td>
                                @if (can('edit', 'suppliers-invoice'))
                                    <input type="text" class="form-control" id="supplier_invoice_date"
                                        name="supplier_invoice_date"
                                        value="{{ $invoice->supplier_invoice_date->format('Y-m-d') }}">
                                @else
                                    {{ $invoice->supplier_invoice_date->format('Y-m-d') }}
                                @endif
                            </td>
                            <th>Date Processed</th>
                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                            <th>Processed By</th>
                            <td colspan="2">{{ $invoice->user->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>Quanity</th>
                                <th>Standard Cost</th>
                                <th>VAT</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->quantity) }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->standart_cost_unit) }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->vat_amount) }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->amount) }}</td>
                                </tr>
                            @endforeach
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">VAT</th>
                                <th class="text-right">{{ manageAmountFormat($invoice->vat_amount) }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Total</th>
                                <th class="text-right">{{ manageAmountFormat($invoice->amount) }}</th>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
            </div>
            @if (can('edit', 'suppliers-invoice'))
                <div class="box">
                    <div class="box-footer text-right">
                        <a class="btn btn-primary" href="{{ route('maintain-suppliers.processed_invoices.index') }}" id="cancel-invoice">
                            <i class="fa fa-close"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="update-invoice-details">
                            <i class="fa fa-save"></i>
                            Save
                        </button>
                    </div>
                </div>
            @endif
        </form>
    </section>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#supplier_invoice_date").datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#invoice-form').submit(function(e) {
                e.preventDefault();

                let submitForm = $(this);
                let submitBtn = submitForm.find('#update-invoice-details');
                let cancelLink = submitForm.find('#cancel-invoice');
                let originalSubmitText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                cancelLink.addClass('disabled').off('click');

                $.ajax({
                    type: "POST",
                    url: submitForm.attr('action'),
                    data: submitForm.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.result === 1) {
                            form.successMessage(response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        let errorMessage = '';
                        if (xhr?.responseJSON?.message) {
                            $.each(xhr.responseJSON.message, function(key, value) {
                                errorMessage += value.join('<br>') + '<br>';
                            });
                        }else {
                            errorMessage = 'Something went wrong.'
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage,
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalSubmitText);
                        cancelLink.removeClass('disabled');
                    }
                });
            });
        })
    </script>
@endpush
