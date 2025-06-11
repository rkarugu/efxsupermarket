@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Bank Reconciliation </h3>
                    <div class="d-flex">
                        <form action="{{ route('bank-reconciliation.unreconciled') }}" method="post">
                            {{ @csrf_field() }}

                            <input type="hidden" name="records" value="{{ json_encode($notReconciled) }}">
                            <input type="submit" value="Download Unreconciled (Bank)" class="btn btn-primary">
                        </form>

                        <form action="{{ route('bank-reconciliation.doubles') }}" method="post" class="ml-12">
                            {{ @csrf_field() }}

                            <input type="hidden" name="records" value="{{ json_encode($doubles) }}">
                            <input type="submit" value="Download Double Entries" class="btn btn-primary">
                        </form>

                        <form action="{{ route('bank-reconciliation.reconciled') }}" method="post" class="ml-12">
                            {{ @csrf_field() }}

                            <input type="submit" value="Download Reconciled" class="btn btn-primary">
                        </form>

                        <form action="{{ route('bank-reconciliation.system_unreconciled') }}" method="post" class="ml-12">
                            {{ @csrf_field() }}

                            <input type="submit" value="Download Unreconciled (System)" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="post" enctype="multipart/form-data">
                    {{ @csrf_field() }}

                    <div class="row">
                        {{--                        <div class="form-group col-sm-2">--}}
                        {{--                            <label for="bank" class="control-label"> Select Bank </label>--}}
                        {{--                            <select name="bank" id="bank" class="form-control" required>--}}
                        {{--                                <option value="" selected disabled></option>--}}
                        {{--                                @foreach ($banks as $bank)--}}
                        {{--                                    <option value="{{ $bank['value'] }}" @if($selectedBank == $bank['value']) selected @endif>{{ $bank['label'] }}</option>--}}
                        {{--                                @endforeach--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}

                        <div class="form-group col-sm-2">
                            <label for="start_date" class="control-label"> Start Date </label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $selectedStartDate }}">
                        </div>

                        <div class="form-group col-sm-2">
                            <label for="end_date" class="control-label"> End Date </label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $selectedEndDate }}">
                        </div>

                        <div class="form-group col-sm-2">
                            <label for="upload_file" class="control-label"> Equity Bank File </label>
                            <input type="file" class="form-control" name="equity_upload_file" id="equity_upload_file">
                            <label class="custom-file-label" id="equityCustomFile"></label>
                        </div>

                        <div class="form-group col-sm-2">
                            <label for="upload_file" class="control-label"> KCB Bank File </label>
                            <input type="file" class="form-control" name="kcb_upload_file" id="kcb_upload_file">
                            <label class="custom-file-label" id="kcbCustomFile"></label>
                        </div>

                        <div class="form-group col-sm-2">
                            <label for="upload_file" class="control-label"> MPESA Bank File </label>
                            <input type="file" class="form-control" name="mpesa_upload_file" id="mpesa_upload_file">
                            <label class="custom-file-label" id="mpesaCustomFile"></label>
                        </div>
                    </div>

                    <div class="d-flex">
                        <input type="submit" name="intent" value="Process" class="btn btn-primary">
                    </div>
                </form>

                @if($dataProcessed)
                    <hr>

                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#summary" data-toggle="tab"> Summary</a></li>
                        <li><a href="#reconciled_trans" data-toggle="tab">Reconciled Transactions</a></li>
                        <li><a href="#missing_trans" data-toggle="tab">Missing In System</a></li>
                        <li><a href="#hanging_trans" data-toggle="tab">Ignored Transactions</a></li>
                        <li><a href="#mib_trans" data-toggle="tab">Missing In Bank</a></li>
                        <li><a href="#flagged_trans" data-toggle="tab">Flagged Transactions</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="summary">
                            <div class="table-responsive box-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Uploaded Transactions</th>
                                        <th>System Transactions</th>
                                        <th>Variance</th>
                                        <th>Reconciled Transactions</th>
                                        <th>Variance (Against System)</th>
                                        <th>Missing Transactions</th>
                                        <th>Ignored Transactions</th>
                                        <th>Flagged Transactions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <th scope="row">{{ $bankCount }} ({{ manageAmountFormat($bankTotal) }})</th>
                                        <th scope="row">{{ $systemCount }} ({{ manageAmountFormat($systemTotal) }})</th>
                                        <th scope="row">{{ $bankCount - $systemCount }} ({{ manageAmountFormat($bankTotal - $systemTotal) }})</th>
                                        <th scope="row">{{ count($reconciledTransactions) }} ({{ manageAmountFormat($reconciledTotal) }})</th>
                                        <th scope="row">{{ $systemCount - count($reconciledTransactions) }} ({{ manageAmountFormat($systemTotal - $reconciledTotal) }})</th>
                                        <th scope="row">{{ count($missingTransactions) }} ({{ manageAmountFormat($missingTotal) }})</th>
                                        <th scope="row">{{ count($ignoredTransactions) }} ({{ manageAmountFormat($ignoredTotal) }})</th>
                                        <th scope="row">{{ count($flaggedTransactions) }} ({{ manageAmountFormat($flaggedTotal) }})</th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="reconciled_trans">
                            <div class="table-responsive box-body">
                                <table class="table table-bordered table-hover" id="reconciled_trans_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;"></th>
                                        <th>System Trans Date</th>
                                        <th>System Input Date</th>
                                        <th>Bank Date</th>
                                        <th>Route</th>
                                        <th>Reference</th>
                                        <th>Document No</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($reconciledTransactions as $tran)
                                        <tr>
                                            <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                            <td>{{ $tran->trans_date }}</td>
                                            <td>{{ $tran->input_date }}</td>
                                            <td>{{ $tran->bank_date }}</td>
                                            <td>{{ $tran->customer_name }}</td>
                                            <td>{{ $tran->reference }}</td>
                                            <td>{{ $tran->document_no }}</td>
                                            <td>{{ manageAmountFormat($tran->amount) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th scope="row" colspan="7">RECONCILED TOTAL</th>
                                        <th scope="row" colspan="1">{{ manageAmountFormat($reconciledTotal) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="missing_trans">
                            <div class="table-responsive box-body">
                                <table class="table table-bordered table-hover" id="missing_trans_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;"></th>
                                        <th>Bank Date</th>
                                        <th>Reference</th>
                                        <th>Route</th>
                                        <th>Comments</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($missingTransactions as $tran)
                                        <tr>
                                            <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                            <td>{{ $tran['bank_date'] }}</td>
                                            <td>{{ $tran['bank_ref'] }}</td>
                                            <td>{{ $tran['route'] }}</td>
                                            <td>{{ $tran['comments'] }}</td>
                                            <td>{{ manageAmountFormat($tran['amount']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th scope="row" colspan="5">MISSING TOTAL</th>
                                        <th scope="row" colspan="1">{{ manageAmountFormat($missingTotal) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="hanging_trans">
                            <div class="table-responsive box-body">
                                <table class="table table-bordered table-hover" id="ignored_trans_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;"></th>
                                        <th>Bank Date</th>
                                        <th>Reference</th>
                                        <th>Route</th>
                                        <th>Comments</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($ignoredTransactions as $tran)
                                        <tr>
                                            <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                            <td>{{ $tran['bank_date'] }}</td>
                                            <td>{{ $tran['bank_ref'] }}</td>
                                            <td>{{ $tran['route'] }}</td>
                                            <td>{{ $tran['comments'] }}</td>
                                            <td>{{ manageAmountFormat($tran['amount']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th scope="row" colspan="5">IGNORED TOTAL</th>
                                        <th scope="row" colspan="1">{{ manageAmountFormat($ignoredTotal) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="mib_trans">
                            ...
                        </div>

                        <div class="tab-pane" id="flagged_trans">
                            <div class="table-responsive box-body">
                                <table class="table table-bordered table-hover" id="flagged_trans_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;"></th>
                                        <th>Bank Date</th>
                                        <th>Reference</th>
                                        <th>Route</th>
                                        <th>Comments</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($flaggedTransactions as $tran)
                                        <tr>
                                            <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                            <td>{{ $tran['bank_date'] }}</td>
                                            <td>{{ $tran['bank_ref'] }}</td>
                                            <td>{{ $tran['route'] }}</td>
                                            <td>{{ $tran['comments'] }}</td>
                                            <td>{{ manageAmountFormat($tran['amount']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th scope="row" colspan="5">FLAGGED TOTAL</th>
                                        <th scope="row" colspan="1">{{ manageAmountFormat($flaggedTotal) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
            $("#bank").select2();

            $('#reconciled_trans_table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'initComplete': function (settings, json) {
                    let info = this.api().page.info();
                    let total_record = info.recordsTotal;
                    if (total_record < 101) {
                        // $('.dataTables_paginate').hide();
                    }
                },
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });

            $('#missing_trans_table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'initComplete': function (settings, json) {
                    let info = this.api().page.info();
                    let total_record = info.recordsTotal;
                    if (total_record < 101) {
                        // $('.dataTables_paginate').hide();
                    }
                },
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });

            $('#ignored_trans_table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'initComplete': function (settings, json) {
                    let info = this.api().page.info();
                    let total_record = info.recordsTotal;
                    // if (total_record < 11) {
                    //     $('.dataTables_paginate').hide();
                    // }
                },
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
        });

        $('#equity_upload_file').on('change', function () {
            let fileName = $(this).val();
            $(this).next('#equityCustomFile').text(fileName);
        })

        $('#kcb_upload_file').on('change', function () {
            let fileName = $(this).val();
            $(this).next('#kcbCustomFile').text(fileName);
        })

        $('#mpesa_upload_file').on('change', function () {
            let fileName = $(this).val();
            $(this).next('#mpesaCustomFile').text(fileName);
        })
    </script>
@endsection