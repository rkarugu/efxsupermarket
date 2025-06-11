@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <form id="paymentVoucherForm"
            action="{{ route('maintain-suppliers.payment_vouchers.store', $supplier->supplier_code) }}" method="post">
            @csrf()
            <input type="hidden" name="type" value="advance">
            <input type="hidden" id="transactions" name="transactions">
            <input type="hidden" id="cheques" name="cheques">
            <div class="box box-primary">
                <div class="box-header">
                    @include('message')
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="supplier">Supplier</label>
                            <input type="text" class="form-control" readonly id="supplier"
                                value="{{ $supplier->name }} ({{ $supplier->bank_name }} - {{ $supplier->bank_account_no }})">
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="account">Bank Account</label>
                                <select name="account" data-rule-required="true" id="account" class="form-control">
                                    <option value="">Select Option</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ $account->id == old('account') ? 'selected' : '' }}>
                                            {{ $account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="payment_mode">Payment Mode</label>
                                <select name="payment_mode" data-rule-required="true" id="payment_mode"
                                    class="form-control">
                                    <option value="">Select Option</option>
                                    @foreach ($paymentModes as $paymentMode)
                                        <option value="{{ $paymentMode->id }}"
                                            {{ $paymentMode->id == old('payment_mode') ? 'selected' : '' }}>
                                            {{ $paymentMode->mode }} ({{ $paymentMode->description }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="narration">Narration</label>
                        <input type="text" id="narration" name="narration" class="form-control">
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="box-header border-bottom">
                    <div class="d-flex">
                        <h4 class="flex-grow-1">Advance Payments</h4>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addInvoicesModal">
                            <i class="fa fa-plus"></i>
                            Add Advance
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="selectedInvoicesTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>LPO</th>
                                <th>Amount</th>
                                <th>Tax</th>
                                <th>Withholding Tax</th>
                                <th>Amt to Pay</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8">No advance payments selected</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="transactionsError"></div>
                    @error('transactions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal fade" role="dialog" id="addInvoicesModal">
                <div class="modal-dialog modal-lg" role="document" style="@media (min-width:1170px) {width: 1170px}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add Invoices</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered" id="pendingInvoicesTable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Date</th>
                                        <th>LPO</th>
                                        <th class="text-right">VAT</th>
                                        <th class="text-right">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="box">
                        <div class="box-header">
                            <h4>Allocate Cheques</h4>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="form-group col-sm-4" style="padding-right: 0">
                                            <label for="cheque">Cheque</label>
                                            <input type="text" class="form-control" id="cheqNumber" readonly
                                                value="{{ $chqSeries }}">
                                        </div>
                                        <div class="form-group col-sm-4" style="padding-right: 0">
                                            <label for="date">Date</label>
                                            <input type="text" class="form-control datepicker" id="cheqDate" disabled
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="form-group col-sm-4" style="padding-right: 0">
                                            <label for="amount">Amount</label>
                                            <input type="number" class="form-control" id="cheqAmount" disabled>
                                        </div>
                                        <div id="unalloctedError" class="form-group col-sm-12" style="color: #dc3545">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label style="display:block">&nbsp;</label>
                                    <button type="button" class="btn btn-primary" id="cheqBtn" disabled>
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped" id="chequesList">
                                <thead>
                                    <tr>
                                        <th>Cheque</th>
                                        <th>Date</th>
                                        <th class="text-right">Amount</th>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4">Add Cheques</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div id="chequesError"></div>
                            @error('cheques')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="box">
                        <div class="box-header">
                            <h4>View Payments</h4>
                        </div>
                        <div class="box-body" style="min-height: 170px;max-height: 300px;overflow-y: auto;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Remit #</th>
                                        <th>Voucher No.</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paymentVouchers as $paymentVoucher)
                                        <tr>
                                            <td>{{ $paymentVoucher->id }}</td>
                                            <td>{{ $paymentVoucher->number }}</td>
                                            <td>{{ $paymentVoucher->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $paymentVoucher->status == 0 ? 'PENDING' : 'APPROVED' }}</td>
                                            <td class="text-right">{{ manageAmountFormat($paymentVoucher->amount) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('payment-vouchers.print_pdf', $paymentVoucher->id) }}"
                                                    target="_blank">
                                                    <span class="span-action" data-toggle="tooltip" title="Print"
                                                        style="cursor: pointer; margin-left: 10px">
                                                        <i class="fa fa-print"></i>
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">Add Payments</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <h4>Allocated: <span id="allocated" style="font-weight: bold">0.00</span></h4>
                            <h4 class="text-right">Balance to Allocate:
                                <spann id="unallocated" style="font-weight: bold">0.00</span>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('maintain-suppliers.vendor_centre', $supplier->supplier_code) }}"
                            class="btn btn-primary" data-dismiss="modal">
                            <i class="fa fa-chevron-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary" name="action" value="print">
                            <i class="fa fa-save"></i> Save & Print
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endpush
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        window.invoices = {!! json_encode($payments) !!};
        window.selectedInvoices = [];

        window.transactions = [];
        window.allocations = [];
        window.chqSeries = "{{ $chqSeries }}";

        $(document).ready(function() {
            $("#addInvoicesModal").on('show.bs.modal', function() {
                $('#selectAll').prop('checked', false);
                let unselectedInvoices = window.invoices.filter((invoice) => !selectedInvoices.includes(
                    invoice))

                $("#pendingInvoicesTable tbody").html(renderPendingInvoices(unselectedInvoices))
            });

            $('#selectAll').change(function() {
                $('.itemCheckbox').prop('checked', $(this).prop('checked'));
            });

            $('#addInvoicesModal').on('change', '.itemCheckbox', function() {
                if ($('.itemCheckbox:checked').length === $('.itemCheckbox').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            });

            $("#addInvoicesModal").on('hide.bs.modal', function() {
                let ids = [];

                $('.itemCheckbox:checked').each((index, element) => {
                    ids.push(parseInt($(element).val()))
                });

                if (ids.length) {
                    let selection = window.invoices.filter((invoice) => ids.includes(invoice.id));
                    window.selectedInvoices = [...window.selectedInvoices, ...selection];

                    $("#selectedInvoicesTable tbody").html(renderSelectedInvoices(window.selectedInvoices));

                    updateUnallocatedHtml()
                }
            });

            $("#selectedInvoicesTable").on('click', '[data-toggle="invoices"]', function() {
                let id = $(this).data('invoice');

                window.selectedInvoices = window.selectedInvoices.filter(function(invoice) {
                    return invoice.id != id;
                });

                transactions = transactions.filter(function(transaction) {
                    return transaction != id;
                });

                $("#transactions").val(transactions.length > 0 ? JSON.stringify(transactions) : '').valid();

                $(this).parents('tr').remove();

                $("#selectedInvoicesTable tbody").html(renderSelectedInvoices(window.selectedInvoices));

                updateUnallocatedHtml();
            });
        })

        function renderPendingInvoices(items) {
            if (items.length == 0) {
                return `<tr><td colspan="8">No advances to be selected</td></tr>`;
            }

            let records = '';

            items.forEach(item => {
                records += `<tr>
                                <td><input type="radio" name="advance" value="` + item.id + `" class="itemCheckbox"></td>
                                <td>` + moment(item.created_at).format('YYYY-MM-DD') + `</td> 
                                <td>` + item.lpo.purchase_no + `</td> 
                                <td class="text-right">` + Number(item.vat_amount).formatMoney() + `</td> 
                                <td class="text-right">` + Number(item.amount).formatMoney() + `</td> 
                            </tr>`
            });

            return records;
        }

        function renderSelectedInvoices(items) {

            if (items.length == 0) {
                return `<tr><td colspan="10">No invoices to be selected</td></tr>`;
            }

            let records = '';

            items.forEach(item => {
                records += `<tr id="invoce_` + item.id + `">
                                <td>` + moment(item.created_at).format('YYYY-MM-DD') + `</td> 
                                <td>ADVANCE</td> 
                                <td>` + item.lpo.purchase_no + `</td> 
                                <td class="text-right">` + Number(item.amount).formatMoney() + `</td> 
                                <td class="text-right">` + Number(item.vat_amount).formatMoney() + `</td> 
                                <td class="text-right">` + Number(item.withholding_tax).formatMoney() + `</td> 
                                <td class="text-right">` + Number(item.amount - item.withholding_tax).formatMoney() +
                    `</td> 
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="invoices" data-invoice="` +
                    item.id + `">
                                        &times;
                                    </button>  
                                </td>
                            </tr>`;

            });

            return records;
        }
    </script>
    <script>
        $("body").addClass('sidebar-collapse')

        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });

            $("#account, #payment_mode").select2();

            $("#payment_mode").change(function() {
                if ($(this).val() == 2) {
                    $("#cheqNumber").prop('readonly', false).val('');
                } else {
                    $("#cheqNumber").prop('readonly', true).val(window.chqSeries);
                }
            });

            $("#paymentVoucherForm").validate({
                ignore: [],
                rules: {
                    transactions: {
                        required: true
                    },
                    cheques: {
                        required: true
                    }
                },
                errorPlacement: function(error, element) {
                    if ($(element).attr('id') == 'transactions') {
                        $("#transactionsError").html(error);
                    } else if ($(element).attr('id') == 'cheques') {
                        $("#chequesError").html(error);
                    } else {
                        $(element).closest('div').append(error);
                    }
                },
                messages: {
                    transactions: "Please select at least one item",
                    cheques: "Please add at least one cheque",
                }
            });

            $("#cheqBtn").click(function() {
                let number = $("#cheqNumber").val();
                let date = $("#cheqDate").val();
                let amount = $("#cheqAmount").val();

                if (date.length == 0) {
                    $("#cheqDate").parent().addClass('has-error');
                    return;
                } else {
                    $("#cheqDate").parent().removeClass('has-error');
                }

                if (amount.length == 0) {
                    $("#cheqAmount").parent().addClass('has-error');
                    return;
                } else {
                    $("#cheqAmount").parent().removeClass('has-error');
                }

                let unallocatedAmount = calculateToAllocate();
                let allocatedAmount = calculateAllocated();
                amount = Number(amount);

                // We convert to decimal as floating points can sometimes give 
                // false values hence the check fails and return balance has exceeed
                // error

                balance = (unallocatedAmount * 100 - allocatedAmount * 100 - amount * 100) / 100;

                if (balance == 0) {
                    $("#unalloctedError").html('');

                    $.ajax({
                        url: "{{ route('maintain-suppliers.payment_vouchers.cheques.store', $supplier->supplier_code) }}",
                        method: "post",
                        data: {
                            cheq_number: number,
                            cheq_date: date,
                            cheq_amount: amount,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            window.allocations.push({
                                cheq_number: number,
                                cheq_date: date,
                                cheq_amount: amount,
                            });

                            $("#cheques").val(JSON.stringify(window.allocations)).valid();

                            $("#cheqNumber").val(data.cheq_number);
                            window.chqSeries = data.cheq_number;

                            updateCheqsHtml();
                            if (balance > 0) {
                                enableCheques();
                            } else {
                                disableCheques()
                            }
                            updateBalancesHtml(balance, allocatedAmount + amount)
                            resetCheqForm();
                        }
                    });
                } else {
                    resetCheqForm();
                    $("#unalloctedError").text('Amount amount to allocate must be equal to allocated');
                }
            });

            $(".amountToPay").blur(function() {
                $('#item' + $(this).data('item')).data('amount', $(this).val());

                updateUnallocatedHtml();
            })
        });

        function calculateToAllocate() {
            let amount = 0;
            let transactions = [];
            window.selectedInvoices.forEach(function(invoice) {
                balance = Number(invoice.amount) - Number(invoice.withholding_tax);

                amount += parseFloat(balance.toFixed(2));
                transactions.push({
                    id: invoice.id,
                    amount: parseFloat(balance.toFixed(2))
                });

                $("#transactions").val(JSON.stringify(transactions)).valid();
            });

            return amount;
        }

        function calculateAllocated() {
            let amount = 0;
            if (window.allocations.length > 0) {
                $(window.allocations).each(function(index, allocation) {
                    amount += allocation.cheq_amount;
                });
            }

            return parseFloat(amount.toFixed());
        }

        function updateUnallocatedHtml() {
            let toAllocateAmount = calculateToAllocate();
            let allocatedAmount = calculateAllocated();

            let balance = toAllocateAmount - allocatedAmount;

            if (balance > 0) {
                enableCheques();
            } else {
                disableCheques()
            }

            updateBalancesHtml(balance, allocatedAmount);
        }

        function updateBalancesHtml(balance, allocatedAmount) {
            console.log(balance);
            $("#unallocated").text(balance.formatMoney());
            $("#allocated").text(allocatedAmount.formatMoney());
        }

        function updateCheqsHtml() {
            let cheqHtml = '';
            if (window.allocations.length == 0) {
                $("#chequesList tbody").html(`<tr><td colspan="4">Add Cheques</td></tr>`);
                return;
            }

            $(window.allocations).each(function(index, allocation) {
                cheqHtml += printCheqItem(allocation);
                $("#chequesList tbody").html(cheqHtml);
            });
        }

        function enableCheques() {
            $("#cheqDate").prop('disabled', false);
            $("#cheqAmount").prop('disabled', false);
            $("#cheqBtn").prop('disabled', false);
        }

        function disableCheques() {
            $("#cheqDate").prop('disabled', true);
            $("#cheqAmount").prop('disabled', true);
            $("#cheqBtn").prop('disabled', true);
        }

        function resetCheqForm() {
            $("#cheqDate").val(moment().format('YYYY-MM-DD'));
            $("#cheqAmount").val('');
        }

        function printCheqItem(allocation) {
            return `<tr id="` + allocation.cheq_number + `">
                        <td>` + allocation.cheq_number + `</td>
                        <td>` + allocation.cheq_date + `</td>
                        <td class="text-right">` + Number(allocation.cheq_amount).formatMoney() + `</td>
                        <td style="width:50px">
                            <button type="button" onclick="removeCheq('` + allocation.cheq_number + `')" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>
                        </td>
                    </tr>`
        }

        function removeCheq(cheq_number) {
            $("#" + cheq_number).remove();
            window.allocations = window.allocations.filter(function(allocation) {
                return allocation.cheq_number !== cheq_number
            });

            $.ajax({
                url: "{{ route('maintain-suppliers.payment_vouchers.cheques.destroy', $supplier->supplier_code) }}",
                method: "delete",
                data: {
                    cheq_number: cheq_number,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $("#cheqNumber").val(data.cheq_number);
                    updateCheqsHtml();
                    updateUnallocatedHtml();
                }
            });
        }
    </script>
@endpush
