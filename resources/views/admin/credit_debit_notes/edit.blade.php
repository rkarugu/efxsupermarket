@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <form action="{{ route('credit-debit-notes.update', $note) }}" id="financialNoteForm" method="post"
            enctype="multipart/form-data">
            @csrf()
            @method('PUT')
            <div class="box box-primary">
                <div class="box-header" style="border-bottom: 1px solid #eeee">
                    <h4>Edit Debit/Credit Note - {{ $note->note_no }}</h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="supplier" class="col-sm-3">Supplier</label>
                                    <div class="col-sm-9">
                                        <select name="supplier" id="supplier" class="form-control"
                                            data-rule-required="true">
                                            <option value="">Select Option</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" @selected($supplier->id == $note->supplier_id)
                                                    data-details="{{ json_encode($supplier) }}">
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="supplierDetails"
                                style="border: 1px solid #ddd; min-height: 100px; padding:8px; margin-bottom:15px"></div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="supplier_invoice_number" class="col-sm-3">Supplier Invoice Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="supplier_invoice_number"
                                            data-rule-required="true" name="supplier_invoice_number"
                                            value="{{ $note->supplier_invoice_number }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="cu_invoice_number" class="col-sm-3">Cu Invoice Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="cu_invoice_number"
                                            value="{{ $note->cu_invoice_number }}" name="cu_invoice_number"
                                            data-rule-required="true">
                                    </div>
                                </div>
                                @error('cu_invoice_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="type" class="col-sm-3">Control Amount</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="controlAmount" id="controlAmount"
                                            data-rule-required="true"
                                            value="{{ number_format($note->amount, 2, '.', '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="type" class="col-sm-3">Type</label>
                                    <div class="col-sm-9">
                                        <select type="text" class="form-control" id="type" name="type"
                                            data-rule-required="true">
                                            <option value="">Slect Option</option>
                                            <option value="CREDIT" @selected($note->type == 'CREDIT')>Credit Note (Reduce Supplier
                                                Account)</option>
                                            <option value="DEBIT" @selected($note->type == 'DEBIT')>Debit Note (Increase
                                                Supplier Account)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="note_date" class="col-sm-3">Note Date</label>
                                    <div class="col-sm-9">
                                        <input type="date" class="form-control" id="note_date" name="note_date"
                                            value="{{ $note->note_date }}" data-rule-required="true">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="location" class="col-sm-3">Branch</label>
                                    <div class="col-sm-9">
                                        <select name="location" id="location" class="form-control"
                                            data-rule-required="true">
                                            <option value="">Select Option</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" @selected($branch->id == $note->location_id)>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="memo" class="col-sm-3">Memo</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="memo"
                                            name="memo"data-rule-required="true" value="{{ $note->memo }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="file" class="col-sm-3">File</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control" id="file" name="file">
                                    </div>
                                </div>
                                @error('file')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary" type="button" id="btnAddRecord">
                        <i class="fa fa-plus"></i>
                        Add Record
                    </button>
                    <hr>
                    <div class="p-3">
                        <table class="table table-bordered table-striped" id="recordsTable">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Memo</th>
                                    <th>Tax</th>
                                    <th>Excl. Amount</th>
                                    <th>Tax Amount</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($note->items as $key => $item)
                                    <tr class="items">
                                        <td width="30%">
                                            <div class="form-group">
                                                <select name="accounts[{{ $key }}][id]"
                                                    class="form-control account" data-rule-required="true">
                                                    <option value="">Select Option</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}" @selected($account->id == $item->account_id)>
                                                            {{ $account->account_name }} ({{ $account->account_code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <div class="form-group">
                                                <input type="text" name="accounts[{{ $key }}][memo]"
                                                    id="memo" class="form-control" value="{{ $item->memo }}">
                                            </div>
                                        </td>
                                        <td width="10%">
                                            <div class="form-group">
                                                <select name="accounts[{{ $key }}][tax_manager]" id="tax"
                                                    class="form-control vat_taxes" data-rule-required="true">
                                                    <option value="">Select Option</option>
                                                    @foreach ($vat_taxes as $vat_tax)
                                                        <option value="{{ $vat_tax->id }}" @selected($vat_tax->tax_value == $item->tax_rate)
                                                            data-rate="{{ $vat_tax->tax_value }}">
                                                            {{ $vat_tax->title }} ({{ $vat_tax->tax_value }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <div class="form-group">
                                                <input type="number" name="accounts[{{ $key }}][amount]"
                                                    class="form-control amount" data-rule-required="true"
                                                    value="{{ $item->amount }}">
                                            </div>
                                        </td>
                                        <td width="10%" class="text-right">
                                            <span class="tax_amount">0.00</span>
                                        </td>
                                        <td width="10%" class="text-right">
                                            <span class="total_amount">0.00</span>
                                        </td>
                                        <td width="5%">
                                            <button type="button" class="btn btn-primary btn-sm removeBtn">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-records">
                                        <td colspan="7">No records added</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Totals:</th>
                                    <th id="amountTotal" class="text-right">0.00</th>
                                    <th id="taxTotal" class="text-right">0.00</th>
                                    <th id="grandTotal" class="text-right">0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="transactions" id="transactions">
                        <input type="hidden" name="grandTotalAmount" id="grandTotalAmount">
                        <div id="transactionsError"></div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <a href="{{ route('credit-debit-notes.index') }}" class="btn btn-primary">
                            <i class="fa fa-close"></i>
                            Cancel
                        </a>
                        <button class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
        $(document).ready(function() {
            $("select.form-control").select2();

            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#supplier').on('change', function(e) {
                let details = $(e.currentTarget).find(':selected').data('details');

                $("#supplierDetails").html(`
                    <p>` + details.name + `</p>
                    <p>` + details.address + `</p>
                    <p>` + details.telephone + `</p>
                    <p>` + details.email + `</p>
                `)
            });

            $("#btnAddRecord").click(function() {
                let records = $("#recordsTable tbody tr.no-records").length;
                if (records > 0) {
                    $("#recordsTable tbody").html(addRecord);
                } else {
                    $("#recordsTable tbody").append(addRecord);
                }

                $(".account, .vat_taxes").select2();
                $("#transactions").val($("#recordsTable tbody tr").length).valid();
            });

            $(".table tbody").on('click', '.removeBtn', function() {
                $(this).parents('tr').remove();
                let records = $("#recordsTable tbody tr").length;
                if (records == 0) {
                    $("#transactions").val('').valid();
                    $("#recordsTable tbody").append(emptyRecord);
                }
            });

            $("#financialNoteForm").validate({
                ignore: [],
                rules: {
                    transactions: {
                        required: function() {
                            return !$("#recordsTable tbody tr.items").length > 0;
                        }
                    },
                    grandTotalAmount: {
                        equalTo: "#controlAmount"
                    }
                },
                errorPlacement: function(error, element) {
                    if ($(element).attr('id') == 'transactions') {
                        $("#transactionsError").html(error);
                    }
                    if ($(element).attr('id') == 'grandTotalAmount') {
                        $("#transactionsError").html(error);
                    } else {
                        $(element).closest('div').append(error);
                    }
                },
                messages: {
                    transactions: "Please add at least one transaction",
                    grandTotalAmount: "The grand total must be equal to the control amount",
                }
            })

            $("#financialNoteForm").on('change', ".amount", function() {
                let amount = Number($(this).val());
                let rate = Number($(this).parents('tr').find('.vat_taxes option:selected').data('rate'));
                let tax_amount = calculateTax(amount, rate);

                showRowTotals($(this), amount, tax_amount);
                showColumnTotals();
            });

            $('#financialNoteForm').on('change', ".vat_taxes", function(e) {
                let amount = Number($(this).parents('tr').find('.amount').val());
                let rate = Number($(this).find(':selected').data('rate'));
                let tax_amount = calculateTax(amount, rate);

                showRowTotals($(this), amount, tax_amount);
                showColumnTotals();
            });

            $(".amount, .vat_taxes, #supplier").trigger('change');
        });

        function showRowTotals(element, amount, tax_amount) {
            let total = amount + tax_amount;
            element.parents('tr').find('.tax_amount').text(tax_amount.formatMoney());
            element.parents('tr').find('.total_amount').text(total.formatMoney());
        }

        function showColumnTotals() {
            let amountTotal = 0;
            let taxTotal = 0;

            $(".amount").each(function(index, element) {
                let rate = Number($(element).parents('tr').find('.vat_taxes option:selected').data('rate'));
                amountTotal += amount = Number($(element).val());
                taxTotal += amount * rate / 100;
            });

            let grandTotal = amountTotal + taxTotal

            $("#amountTotal").text(amountTotal.formatMoney());
            $("#taxTotal").text(taxTotal.formatMoney());
            $("#grandTotal").text(grandTotal.formatMoney());

            $("#grandTotalAmount").val(parseFloat(grandTotal).toFixed(2)).valid();
        }

        function emptyRecord() {
            return `
                <tr class="no-records">
                    <td colspan="7">No records added</td>
                </tr>
            `;
        }

        function addRecord() {
            let index = 'account' + moment().format('HHmmSS');
            return `
                <tr class="items"> 
                    <td width="30%">
                        <div class="form-group">
                            <select name="accounts[` + index + `][id]" class="form-control account"  data-rule-required="true">
                                <option value="">Select Option</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_name }} ({{ $account->account_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>                    
                    <td width="20%">
                        <div class="form-group">
                            <input type="text" name="accounts[` + index + `][memo]" id="memo" class="form-control">
                        </div>
                    </td>
                    <td width="10%">
                        <div class="form-group">
                            <select name="accounts[` + index + `][tax_manager]" id="tax" class="form-control vat_taxes" data-rule-required="true">
                                <option value="">Select Option</option>
                                @foreach ($vat_taxes as $vat_tax)
                                    <option value="{{ $vat_tax->id }}" data-rate="{{ $vat_tax->tax_value }}">
                                        {{ $vat_tax->title }} ({{ $vat_tax->tax_value }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td width="20%">
                        <div class="form-group">
                            <input type="number" name="accounts[` + index + `][amount]" class="form-control amount" data-rule-required="true">
                        </div>
                    </td>
                    <td width="10%" class="text-right">
                        <span class="tax_amount">0.00</span>
                    </td>  
                    <td width="10%" class="text-right">
                        <span class="total_amount">0.00</span>
                    </td>                    
                    <td width="5%">
                        <button type="button" class="btn btn-primary btn-sm removeBtn">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>            
                </tr>                   
            `;
        }

        function calculateTax(amount, rate) {
            return amount * rate / 100;
        }
    </script>
@endpush
