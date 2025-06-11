@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Bank Statements </h3>
                    <div class="d-flex">
                        {{-- @if (can('bank-statement-allocate-status', 'reconciliation'))
                            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#allowAllocateModal" style="margin-top:0px; margin-right:10px;">
                                {{ $allocateStatusCount? "Disable": "Allow" }} Allocate
                            </button>
                        @endif --}}
                        @if (can('mpesa-statement-topup-debit', 'reconciliation'))
                        <a href="{{ route('bank-statements.top-up-debit-mpesa') }}" class="btn btn-success btn-sm"
                            style="margin-right:10px;">
                            Mpesa Debit Top Up
                        </a>
                    @endif
                        @if (can('bank-statement-topup-debit', 'reconciliation'))
                            <a href="{{ route('bank-statements.top-up-debit') }}" class="btn btn-success btn-sm"
                                style="margin-right:10px;">
                                Debit Top Up
                            </a>
                        @endif
                        @if (can('bank-statement-topup', 'reconciliation'))
                            <a href="{{ route('bank-statements.top-up') }}" class="btn btn-success btn-sm">
                                Top Up
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="form-group col-sm-2">
                        <label for="">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="@if (request()->session()->has('startDate')) {{ request()->session()->get('startDate') }} @endif">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="@if (request()->session()->has('endDate')) {{ request()->session()->get('endDate') }} @endif">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="channel">Channel</label>
                        <select name="channel" id="channel" class="form-control select2">
                            <option value="">Choose Channel</option>
                            @foreach ($channels as $channel)
                                <option value="{{ $channel }}">{{ $channel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control select2">
                            <option value="all">All Status</option>
                            <option value="Matched">Matched</option>
                            <option value="Not Matched">Not Matched</option>
                            <option value="Duplicate">Duplicate</option>
                        </select>
                    </div>
                    <div style="margin-left:10px;margin-top:25px; display:flex;">
                        <button type="button" class="btn btn-primary" name="action" id="generateExcelBtn"
                            style="height: 35px;">
                            <i class="fa fa-file-alt"></i> Excel
                        </button>
                        <button type="button" class="btn btn-primary" name="action" id="generatePDFBtn"
                            style="margin-left:10px;height: 35px;">
                            <i class="fa fa-file"></i> PDF
                        </button>
                    </div>
                </div>
                <hr>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="statementsDataTable">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Trans Ref.</th>
                                <th>Channel</th>
                                <th>Bank Date</th>
                                <th>Match Reference</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th colspan="5"> TOTAL </th>
                                <th id="totalDebits"></th>
                                <th id="totalCredits"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- @include('admin.Finance.bank_statement.partials.topup_modal') --}}

    <div class="modal fade" id="debtorUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Allocate Statement to Debtors</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="manualUploadForm" action="{{ route('manual-upload-transaction') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="route" class="control-label"> Route </label>
                                    <select name="route" id="route" class="form-control select22" required>
                                        <option value="">Choose Route</option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route['id'] }}">{{ $route['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="" id="bankId" name="bankId">
                            <input type="hidden" name="startDate" id="startDate"
                                value="@if (request()->session()->has('startDate')) {{ date('m/d/y', strtotime(request()->session()->get('startDate'))) }} @endif">
                            <input type="hidden" name="endDate" id="endDate"
                                value="@if (request()->session()->has('endDate')) {{ request()->session()->get('endDate') }} @endif">
                            <button type="submit" id="confirmManualUploadBtn" class="btn btn-primary" data-id="0"
                                data-dismiss="modal">Allocate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Edit Channel Statement</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="editUploadForm" action="{{ route('bank-edit-channel') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="channel">Channel</label>
                                    <select name="channel" id="channelEdit" class="form-control select2">
                                        <option value="">Choose Channel</option>
                                        @foreach ($channels as $channel)
                                            <option value="{{ $channel }}">{{ $channel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="" id="bankIdEdit" name="bankId">
                            <button type="submit" id="confirmEditBtn" class="btn btn-primary" data-id="0"
                                data-dismiss="modal">Edit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="allowAllocateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Allocation Status</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('bank-allocate-status') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <p>Are you sure you want to {{ $allocateStatusCount ? 'Disable' : 'Allow' }} users to Allocate ?
                        </p>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="roles" value="{{ json_encode($allocateStatusRoles) }}">

                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="{{ $allocateStatusCount ? 0 : 1 }}" name="status">
                            <button type="submit" class="btn btn-primary">Update Allocate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bankErrorModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Flag Bank Error Statement</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="bankErrorForm" action="{{ route('bank-statement-error-flag') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-sm-4"><label for="flagRef" class="control-label"> Reference </label>
                                </div>
                                <div class="col-sm-8">
                                    <textarea type="text" id="flagRef" class="form-control" disabled></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-sm-4"><label for="flagChannel" class="control-label"> Channel </label>
                                </div>
                                <div class="col-sm-8"><input type="text" id="flagChannel" class="form-control"
                                        disabled></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-sm-4"><label for="flagAmt" class="control-label"> Amount </label></div>
                                <div class="col-sm-8"><input type="text" id="flagAmt" class="form-control"
                                        disabled></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-sm-4"><label class="control-label"> Flag Reason</label></div>
                                <div class="col-sm-8">
                                    <textarea name="flag_reason" id="flag_reason" class="form-control" required></textarea>
                                    <span class="error-message" style="color:red; display:none;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="" id="bankFlagId" name="bankId">
                            <button type="submit" id="confirmBankErrorBtn" class="btn btn-primary" data-id="0">Flag
                                Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stockDebtorModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Allocate to Stock Debtors</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="allocateStockDebtorForm" action="{{ route('manual-upload-transaction-stock-debtor') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="employee" class="control-label"> Employee </label>
                                <select name="employee" id="employee" class="form-control mtselect" required>
                                    <option value="">Choose Employee</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="" id="stockDebtorId" name="stockDebtorId">
                            <button type="submit" id="confirmStockDebtorBtn" class="btn btn-primary"
                                data-id="0">Allocate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        $('.select22').select2({
            dropdownParent: $('#debtorUploadModal')
        });
        $('.mtselect').select2({
            dropdownParent: $('#stockDebtorModal')
        });
        $("#start_date").val('{{ session('startDate','') }}');
        $("#end_date").val('{{ session('endDate','') }}');
        
        $('#start_date, #end_date, #channel, #status').on('change', function() {
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            $("#startDate").val(start_date);
            $("#endDate").val(end_date);
            $("#statementsDataTable").DataTable().ajax.reload();
        });
        $('#generateExcelBtn').on('click',function(){
            location.href=`/admin/bank-statements-upload?print=excel&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val()+`&channel=`+$("#channel").val()+`&status=`+$("#status").val();
        });
        $('#generatePDFBtn').on('click',function(){
            location.href=`/admin/bank-statements-upload/?print=pdf&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val()+`&channel=`+$("#channel").val()+`&status=`+$("#status").val();
        });
        $("#statementsDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('bank-statements') !!}',
                    data: function(data) {
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                        data.channel = $("#channel").val();
                        data.status = $("#status").val();
                    }
                },
                columns: [{
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'trans_ref',
                        name: 'trans_ref'
                    },
                    {
                        data: 'channel',
                        name: 'channel'
                    },
                    {
                        data: 'bank_date',
                        name: 'bank_date'
                    },
                    {
                        data: 'debtor_reference',
                        name: 'debtor_reference',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'debit',
                        name: 'amount'
                    },
                    {
                        data: 'credit',
                        name: 'amount'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        targets: -4,
                        render: function(data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                if (row.debtor_reference != '--') {
                                    actions +=
                                        `<a href="/admin/payment-reconciliation-verification-list/` +
                                        row.verification_record_id + `" target="_blank">` + row
                                        .debtor_reference + `</a>`;
                                }
                                return actions;
                            }
                            return data;
                        }
                    },
                    {
                        targets: -1,
                        render: function(data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                @if (can('bank-statement-allocate-all', 'reconciliation'))
                                    if (row.debtors == null && row.stock_debtor == null && row
                                        .debtor_reference == '--' && row.type == 'credit') {
                                        actions += `<a onclick="uploadPop(` + row.id +
                                            `)" role="button" title="Allocate"><i class="fa fa-solid fa-upload"></i></a>`;
                                    }
                                @elseif (can('bank-statement-allocate', 'reconciliation'))
                                    if (row.debtors == null && row.stock_debtor == null && row
                                        .debtor_reference == '--' && row.date_difference <= 3 && row
                                        .type == 'credit') {
                                        actions += `<a onclick="uploadPop(` + row.id +
                                            `)" role="button" title="Allocate"><i class="fa fa-solid fa-upload"></i></a>`;
                                    }
                                @endif
                                @if (can('edit-channel', 'reconciliation'))
                                    if (row.debtors == null && row.stock_debtor == null && row
                                        .debtor_reference == '--' && row.type == 'credit') {
                                        actions += `<a onclick="editPopup(` + row.id + `,'` + row
                                            .channel +
                                            `')" role="button" title="Edit" style="margin-left:8px;"><i class="fa fa-solid fa-pencil"></i></a>`;
                                    }
                                @endif
                                @if (can('flag-bank-error', 'reconciliation'))
                                    if (row.verification_status != 'Approved') {
                                        actions += `<a onclick="bankErrorModal(` + row.id + `,'` +
                                            row.channel + `','` + row.reference + `','` + row
                                            .amount +
                                            `')" role="button" title="Flag Bank Error" style="margin-left:8px;color:red;"><i class="fa fa-solid fa-flag"></i></a>`;
                                    }
                                @endif
                                @if (can('allocate-stock-debtor', 'reconciliation'))
                                    if (row.debtors == null && row.stock_debtor == null && row
                                        .debtor_reference == '--' && row.type == 'credit') {
                                        actions += `<a onclick="stockDebtorModal(` + row.id + `,'` +
                                            row.channel + `','` + row.reference + `','` + row
                                            .amount +
                                            `')" role="button" title="Allocate Stock Debtor" style="margin-left:8px;color:green;"><i class="fa-solid fa-user-shield"></i></a>`;
                                    }
                                @endif
                                return actions;
                            }
                            return data;
                        }
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    var json = api.ajax.json();

                    $("#totalDebits").text(json.total_debits);
                    $("#totalCredits").text(json.total_credits);
                    
                }
            });

            $('#confirmManualUploadBtn').on('click', function(e) {
                e.preventDefault();
                var errors = 0;

                if (errors == 0) {
                    $(this).prop("disabled", true);
                    $('#manualUploadForm').get(0).submit();
                }
            });

            $('#confirmEditBtn').on('click', function(e) {
                e.preventDefault();
                var errors = 0;

                if (errors == 0) {
                    $(this).prop("disabled", true);
                    $('#editUploadForm').get(0).submit();
                }
            });

            $('#confirmBankErrorBtn').on('click', function(e) {
                e.preventDefault();

                let reason = $('#flag_reason').val();
                if (reason.trim() === '') {
                    $('#flag_reason').css('border-color', 'red');
                    $('#flag_reason').next('.error-message').text('This field is required.').show();
                    return;
                }
                $(this).prop("disabled", true);
                $('#bankErrorForm').get(0).submit();
            });

            $('#confirmStockDebtorBtn').on('click', function(e) {
                e.preventDefault();
                var errors = 0;

                if (errors == 0) {
                    $(this).prop("disabled", true);
                    $('#allocateStockDebtorForm').get(0).submit();
                }
            });

        });

        function uploadPop(id) {
            $('#bankId').val(id);
            $('#debtorUploadModal').modal('show');
        }

        function editPopup(id, channel) {
            $('#bankIdEdit').val(id);
            $("#channelEdit").val(channel).change();
            $('#editModal').modal('show');
        }

        function bankErrorModal(id, channel, ref, amount) {
            $('#bankFlagId').val(id);
            $("#flagChannel").val(channel);
            $('#flagRef').val(ref);
            $('#flagAmt').val(amount);
            $('#bankErrorModal').modal('show');
        }

        function stockDebtorModal(id, channel, ref, amount) {
            $('#stockDebtorId').val(id);
            $("#stockChannel").val(channel);
            $('#stockRef').val(ref);
            $('#stockAmt').val(amount);
            $('#stockDebtorModal').modal('show');
        }
    </script>
@endsection
