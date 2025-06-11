@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <form action="{{ route('bank-files.store') }}" method="POST" id="bankFileForm">
            @csrf
            <div class="box box-primary">
                <div class="box-header">
                    @include('message')
                    <div class="row">
                        <div class="col-sm-6">
                            <h4 class="flex-grow-1">Create Bank File</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="account">Account</label>
                                        <select name="account" id="account" class="form-control"
                                            data-rule-required="true">
                                            <option value="">Select Option</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->account_name }} ({{ $account->account_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="paymentMethod">Payment Method</label>
                                    <select name="paymentMethod" id="paymentMethod" class="form-control"
                                        data-rule-required="true">
                                        <option value="transfer" selected>Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <h4>Amount</h4>
                                        <h1 id="totalAmount">0.00</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="vouchersTable">
                        <thead>
                            <tr>
                                <th>Voucher No</th>
                                <th>Supplier</th>
                                <th>Payment Mode</th>
                                <th>Prepared By</th>
                                <th>Amount</th>
                                <th>Print</th>
                                <th class="text-center"><input type="checkbox" id="selectAll"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7">No account selected</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <a href="{{ route('bank-files.index') }}" class="btn btn-primary">
                            <i class="fa fa-chevron-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#account").select2();

            $('#selectAll').change(function() {
                if ($(this).is(":checked")) {
                    $('.itemCheckbox').each(function(index, item) {
                        $(this).prop('checked', true);
                    });
                } else {
                    $('.itemCheckbox').each(function(index, item) {
                        $(this).prop('checked', false);
                    });
                }

                calculateAmount()
            });

            $('#vouchersTable tbody').on('change', '.itemCheckbox', function() {
                if ($('.itemCheckbox:checked').length === $('.itemCheckbox').length) {
                    $('#selectAll').prop('checked', true);

                } else {
                    $('#selectAll').prop('checked', false);
                }

                calculateAmount()
            });

            $("#account, #paymentMethod").change(function() {
                let account = $("#account").val();
                let payment_method = $("#paymentMethod").val();

                $.ajax({
                    url: "{{ route('bank-files.show') }}",
                    data: {
                        account: account,
                        payment_method: payment_method,
                    },
                    success: function(data) {
                        if (data.vouchers.length > 0) {
                            let records = '';
                            data.vouchers.forEach(function(item) {
                                records += itemHtml(item)
                            });

                            $("#vouchersTable tbody").html(records);
                        } else {
                            $("#vouchersTable tbody").html(`
                                <tr>
                                    <td colspan="7">No vouchers found</td>
                                </tr>
                            `)
                        }
                    }
                })
            });
        });

        function calculateAmount() {
            let amount = 0;

            $('.itemCheckbox:checked').each(function(index, item) {
                amount += Number($(this).data('amount'));
            })

            $("#totalAmount").text(amount.formatMoney());
        }

        function itemHtml(item) {
            return `<tr>
                        <td>` + item.number + `</td>
                        <td>` + item.supplier.name + `</td>
                        <td>` + item.payment_mode.mode + `</td>
                        <td>` + item.prepared_by.name + `</td>
                        <td>` + Number(item.amount).formatMoney() + `</td>
                        <td class="text-center"><a href="` + item.printUrl + `" target="_blank"><i class="fa fa-print"></i></a></td>
                        <td class="text-center">
                            <input type="checkbox" name="vouchers[]"  value="` + item.id + `" 
                                data-amount="` + item.amount + `" class="itemCheckbox" />
                        </td>
                    </tr>`;
        }
    </script>
@endpush
