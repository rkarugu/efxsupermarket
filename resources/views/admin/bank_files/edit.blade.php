@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <form action="{{ route('bank-files.update', $bankFile) }}" method="POST" id="bankFileForm">
            @csrf
            @method('put')
            <div class="box box-primary">
                <div class="box-header">
                    @include('message')
                    <div class="row">
                        <div class="col-sm-3">
                            <h4 style="margin-bottom: 15px" class="flex-grow-1 box-title">Edit Bank File - {{ $bankFile->file_no }}</h4>
                            <div class="form-group">
                                <label for="account">Account</label>
                                <select name="account" id="account" class="form-control" data-rule-required="true">
                                    <option value="">Select Option</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ $account->id == $bankFile->wa_bank_account_id ? 'selected' : '' }}>
                                            {{ $account->account_name }} ({{ $account->account_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <h4>Amount</h4>
                                        <h1 id="totalAmount">{{ manageAmountFormat($bankFile->items->sum('amount')) }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Select Items to Remove</h4>
                </div>
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
                            @forelse ($bankFile->items as $item)
                                <tr>
                                    <td>{{ $item->voucher->number }}</td>
                                    <td>{{ $item->voucher->supplier->name }}</td>
                                    <td>{{ $item->voucher->paymentMode->mode }}</td>
                                    <td>{{ $item->voucher->preparedBy->name }}</td>
                                    <td>{{ manageAmountFormat($item->amount) }}</td>
                                    <td class="text-center">
                                        <a href="{{ $item->printUrl }}" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="vouchers[]" value="{{ $item->voucher->id }}"
                                            data-amount="{{ $item->voucher->amount }}" class="itemCheckbox" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">No account selected</td>
                                </tr>
                            @endforelse
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

            $('#selectAll').trigger('change')
        });

        function calculateAmount() {
            let amount = 0;

            $('.itemCheckbox:checked').each(function(index, item) {
                amount += Number($(this).data('amount'));
            })

            $("#totalAmount").text(amount.formatMoney());
        }
    </script>
@endpush
