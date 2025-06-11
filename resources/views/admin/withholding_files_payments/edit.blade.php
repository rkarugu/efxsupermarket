@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <h4 class="box-title">Edit Payment</h4>
            </div>
            <form action="{{ route('withholding-tax-payments.update', $voucher) }}" method="POST" class="validate-form">
                @csrf
                @method('put')
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="withholding_account" class="col-sm-4">Account</label>
                                    <div class="col-sm-8">
                                        <select name="withholding_account" id="withholding_account" class="form-control"
                                            data-rule-required="true">
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" @selected($account->id == $voucher->withholding_account_id)>
                                                    {{ $account->account_name }} ({{ $account->account_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="well">
                                <p><strong>File No:</strong></p>
                                <h4 style="margin-bottom: 20px">{{ $voucher->withholdingFile->file_no }}</h4>
                                <p style="margin-bottom: 0"><strong>Amount: </strong></p>
                                <h3 style="margin-top: 0">{{ manageAmountFormat($voucher->withholdingFile->amount) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="bank" class="col-sm-4">Bank</label>
                                    <div class="col-sm-8">
                                        <select name="bank" id="bank" class="form-control"
                                            data-rule-required="true">
                                            <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" @selected($bank->id == $voucher->wa_bank_account_id)>
                                                    {{ $bank->account_name }} ({{ $bank->account_number }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="branch" class="col-sm-4">Branch</label>
                                    <div class="col-sm-8">
                                        <select name="branch" id="branch" class="form-control"
                                            data-rule-required="true">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" @selected($branch->id == $voucher->restaurant_id)>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="date" class="col-sm-4">Date</label>
                                    <div class="col-sm-8">
                                        <input type="date" id="date" name="date" class="form-control" value="{{ $voucher->payment_date->format('Y-m-d') }}"
                                            data-rule-required="true">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="cheque" class="col-sm-4">Cheque No.</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="cheque" name="cheque" class="form-control" value="{{ $voucher->cheque_number }}"
                                            data-rule-required="true">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="memo" class="col-sm-4">Memo</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="memo" name="memo" class="form-control" value="{{ $voucher->memo }}"
                                            data-rule-required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <a href="{{ route('withholding-tax-payments.index') }}" class="btn btn-primary">
                            <i class="fa fa-chevron-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary" name="action" value="print">
                            <i class="fa fa-save"></i> Save & Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("select.form-control").select2()
        });
    </script>
@endpush
