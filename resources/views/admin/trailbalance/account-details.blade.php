@extends('layouts.admin.admin')

@section('content')
    <div id="searchResults"></div>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Trial Balance :: Account Data</h3>
                    <div align="right">
                        <a href="{!! route('trial-balance.account.excel', [
                            $accountInfo->account_code,
                            'start-date' => request()->get('start-date'),
                            'end-date' => request()->get('end-date'),
                            'branch' => request()->get('branch'),
                            'transaction_type' => request()->get('transaction_type'),
                        ]) !!}" class="btn btn-primary">Excel</a>
                        <a href="{!! route('trial-balance.account.group-transaction', [
                            $accountInfo->account_code,
                            'start-date' => request()->get('start-date'),
                            'end-date' => request()->get('end-date'),
                            'branch' => request()->get('branch'),
                            'transaction_type' => request()->get('transaction_type'),
                        ]) !!}" class="btn btn-primary">Group By Transaction</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div style="margin-top:10px;">
                            <div>
                                <b>Name:</b> {{ $accountInfo->account_name }}
                            </div>
                            <div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <b>Code:</b> {{@$accountInfo->account_code }}
                                    </div>
                                    <div class="col-sm-6">
                                        <b>Group:</b> {{@$accountInfo->getRelatedGroup->group_name }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <b>Section:</b> {{@$accountInfo->getSubAccountSection->getAccountSection->section_name}}
                                    </div>
                                    <div class="col-sm-6">
                                        <b>Sub-Section:</b> {{@$accountInfo->getSubAccountSection->section_name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <form action="{{ route('trial-balance.account', $accountInfo->account_code) }}" method="get">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-11">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="start-date">From:</label>
                                    <input type="text" class="form-control datepicker" name="start-date" id="startDate"
                                           value="{{ request()->get('start-date') }}">
                                </div>
                                <div class="col-sm-3">
                                    <label for="end-date">To:</label>
                                    <input type="text" class="form-control datepicker" name="end-date" id="endDate"
                                           value="{{ request()->get('end-date') }}">
                                </div>
                                <div class="col-sm-3">
                                    <label for="branch">Branch:</label>
                                    <select name="branch" id="branch" class="form-control mlselec6t">
                                        <option value="">All Branches</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @selected($branch->id == request()->branch)>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label for="transaction_type">Transaction Type:</label>
                                    <select name="transaction_type" id="transaction_type" class="form-control mlselec6t">
                                        <option value="">Choose Transaction Type</option>
                                        @foreach ($transactionTypes as $item)
                                            <option value="{{ $item }}" @selected(request()->transaction_type == $item)>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <label style="display: block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-hover" id="trialBalance">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th>Transaction Type</th>
                            <th>Transaction No</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                        </thead>
                        {{-- <tbody id="trialBalance">
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                    <td>
                                        @if ($item->branch_name)
                                            {{ $item->branch_name }}
                                        @endif
                                    </td>
                                    <td>{{ $item->narrative }}</td>
                                    <td>{{ $item->reference }}</td>
                                    <td>{{ $item->transaction_type }}</td>
                                    <td>{{ $item->transaction_no }}</td>
                                    <td>{{ $item->amount > 0 ? manageAmountFormat($item->amount) : '' }}</td>
                                    <td>{{ $item->amount < 0 ? manageAmountFormat($item->amount) : '' }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                                <td>{{ manageAmountFormat($debit) }}</td>
                                <td>{{ manageAmountFormat($credit) }}</td>
                            </tr>
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total Balance</td>
                                <td>{{ manageAmountFormat($debit + $credit) }}</td>
                            </tr>
                        </tbody> --}}
                        <tfoot>
                        <tr style="font-weight: bold;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td id="debitTotal"></td>
                            <td id="creditTotal"></td>
                        </tr>
                        {{-- <tr style="font-weight: bold;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total Balance</td>
                            <td>{{ manageAmountFormat($debit + $credit) }}</td>
                        </tr> --}}

                        </tfoot>
                    </table>
                    <table></table>
                    {{-- <div class="d-flex">
                        {!! $data->links() !!}
                    </div> --}}
                </div>
            </form>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>

    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            $('body').addClass('sidebar-collapse');
            $(".mlselec6t").select2();
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
        });

        $(document).ready(function () {
            $("#trialBalance").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('trial-balance.account', $accountInfo->account_code) !!}',
                    data: function (data) {
                        data['start-date'] = $("#startDate").val();
                        data['end-date'] = $("#endDate").val();
                        data.branch = $("#branch").val();
                        data.transaction_type = $("#transaction_type").val();
                    }
                },
                columns: [{
                    data: 'created_at',
                    name: 'wa_gl_trans.created_at',
                },
                    {
                        data: 'branch.name',
                        name: 'branch.name'
                    },
                    {
                        data: 'narrative',
                        name: 'narrative'
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'transaction_no',
                        name: 'transaction_no'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#debitTotal").text(json.debitCreditTotal.debit);
                    $("#creditTotal").text(json.debitCreditTotal.credit);
                }
            });
        });
    </script>
@endsection
