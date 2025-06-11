@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Payment Verification Summary </h3>
                    <div class="d-flex">
                        {{-- <a href="{{ route('payment-reconciliation.verification.list',[$verification_record->id,'download'=>1,'type'=>'matching-transactions']) }}" class="btn btn-primary" style="margin:0 10px;">Print Matching Transactions</a> --}}
                        {{-- <a href="{{ route('payment-reconciliation.verification.list',[$verification_record->id,'download'=>1,'type'=>'missing-in-system-transactions']) }}" class="btn btn-primary" style="margin:0 10px;">Print Missing Bank Transactions</a>
                        <a href="{{ route('payment-reconciliation.verification.list',[$verification_record->id,'download'=>1,'type'=>'missing-in-bank-transactions']) }}" class="btn btn-primary" style="margin:0 10px;">Print Unknown Bankings Transactions</a> --}}
                        <a href="{{ route('payment-reconciliation.verification') }}" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="form-group col-sm-2">
                        <label for="start_date" class="control-label"> Start Date </label>
                        <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $verification_record->start_date }}" readonly>
                    </div>

                    <div class="form-group col-sm-2">
                        <label for="end_date" class="control-label"> End Date </label>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $verification_record->end_date }}" readonly>
                    </div>

                    <div class="form-group col-sm-2">
                        <label for="branch" class="control-label"> Branch </label>
                        <select name="branch" id="branch" class="form-control mtselect" readonly="">
                            <option value="" selected disabled>--Select Branch--</option>
                            @foreach (getBranchesDropdown() as $key => $branch)
                                <option value="{{ $key }}" @if ($verification_record->branch_id == $key) selected @endif>{{ $branch }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" id="confirmApproveBtn" class="btn btn-primary" data-toggle="modal" data-target="#topUpModal" style="margin-top:25px;">Top Up</button>
                    <button type="button" id="reVerifyBtn" class="btn btn-primary" style="margin-top:25px;">Re-verify</button>


                </div>

                <hr>

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#summary" data-toggle="tab">Summary</a></li>
                    <li><a href="#matching_trans" data-toggle="tab">Matching Transactions</a></li>
                    <li><a href="#missing_bank" data-toggle="tab">Missing In Bank</a></li>
                    <li><a href="#missing_system" data-toggle="tab">Unknown Bankings</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="summary">
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>System Transactions (ST)</th>
                                    <th>Matching (M)</th>
                                    <th>Variance/Missing in Bank (ST - M)</th>
                                    <th>Unknown Bankings</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ date('Y-m-d', strtotime($verification_record->start_date)) }}</td>
                                    <td>{{ date('Y-m-d', strtotime($verification_record->end_date)) }}</td>
                                    <td>{{ number_format($verification_record->total_debtors_count) }}({{ manageAmountFormat($verification_record->total_debtors_amount) }})</td>
                                    <td>{{ number_format($matching_transactions_count) }}({{ manageAmountFormat($matching_transactions_total) }})</td>
                                    <td>{{ number_format($missing_in_bank_count) }}({{ manageAmountFormat($missing_in_bank_total) }})</td>
                                    <td>{{ number_format($unknown_bankings_count) }}({{ manageAmountFormat($unknown_bankings_total) }})</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="matching_trans">
                        @include('admin.Finance.payment_reconciliation.partials.matching.pending_approval')
                    </div>

                    <div class="tab-pane" id="missing_bank">
                        @include('admin.Finance.payment_reconciliation.partials.missing_bank')
                    </div>

                    <div class="tab-pane" id="missing_system">
                        @include('admin.Finance.payment_reconciliation.partials.missing_system')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <span class="btn-loader" style="display:none;z-index:9999;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
    </span>
    @include('admin.Finance.bank_statement.partials.topup_modal')
    {{-- <div class="modal fade" id="topUpModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Top up Statements</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="topUpForm" action="{{ route('payment-reconciliation.verification.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label for="equity_makongeni" class="control-label"> Equity Makongeni </label>
                                    <input type="file" class="form-control" name="equity_makongeni" id="equity_makongeni">
                                    <small class="text-danger" id="equity_makongeni_label" style="height:30px; !important"></small>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="equity_main" class="control-label"> Equity Main </label>
                                    <input type="file" class="form-control" name="equity_main" id="equity_main">
                                    <small class="text-danger" id="equity_main_label"></small>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="vooma" class="control-label"> Vooma </label>
                                    <input type="file" class="form-control" name="vooma" id="vooma">
                                    <small class="text-danger" id="vooma_label"></small>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="kcb_main" class="control-label"> KCB Main </label>
                                    <input type="file" class="form-control" name="kcb_main" id="kcb_main">
                                    <small class="text-danger" id="kcb_main_label"></small>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="mpesa" class="control-label"> Mpesa </label>
                                    <input type="file" class="form-control" name="mpesa" id="mpesa">
                                    <small class="text-danger" id="mpesa_label"></small>
                                </div>
                            </div>

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" name="topup_form" value="true">
                            <input type="hidden" name="verification" value="{{$verification_record->id}}">
                            <button type="submit" id="confirmTopUpBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Top Up</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');

            $('.mtselect').select2();           
            $('#reVerifyBtn').on('click', function () {
                $('.btn-loader').show();
                const data = {
                    verification: '{{$verification_record->id}}',
                    topup_form: 'true',
                    reverify_form: 'true',
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: `{{route('payment-reconciliation.verification.process')}}`,
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                })
                    .done((data) => {
                        $('.btn-loader').hide();
                        location.reload();
                    })
                    .fail((err) => {
                        console.error(err);
                    })
                    .always(() => {
                        console.log('always called');
                    });
            })
        });
    </script>
@endsection