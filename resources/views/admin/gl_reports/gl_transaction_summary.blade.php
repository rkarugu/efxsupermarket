@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Transaction Summary Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to General Ledger Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    <div class="card-header">
                        <i class="fa fa-filter"></i> Filter
                    </div>
                    <br>
                    <form action="" method="get">
                        <div>
                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="start-date" style="width:100% !important">Start Date:</label>
                                        <input type="date" id="start-date" class="form-control" name="start-date"
                                            value="{{ @request()->get('start-date') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="end-date" style="width:100% !important">End Date:</label>
                                        <input type="date" id="end-date" class="form-control" name="end-date"
                                            value="{{ @request()->get('end-date') }}">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success" name="manage-request"
                                        value="filter">Filter</button>
                                    <button type="submit" class="btn btn-success" name="manage-request"
                                        value="excel">Excel</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <br>
                @include('message')

                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Transaction Date</th>
                                <th>Posting Date</th>
                                <th>Transaction No</th>
                                <th>Transaction Type</th>
                                <th>User</th>
                                <th>Transaction Count</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($lists as $trans)
                                <tr>
                                    <td>
                                        {{ $trans->trans_date ? date('d/M/Y', strtotime($trans->trans_date)) : $trans->trans_date }}
                                    </td>
                                    <td>
                                        {{ date('d/M/Y', strtotime($trans->created_at)) }}
                                    </td>
                                    <td>{{ $trans->transaction_no }}</td>
                                    <td>{{ $trans->transaction_type }}</td>
                                    <td>{{ @$trans->user->name }}</td>
                                    <td>{{ $trans->count_transaction }}</td>
                                    <td>{{ manageAmountFormat($trans->sum_debit) }}</td>
                                    <td>{{ manageAmountFormat($trans->sum_credit) }}</td>
                                    <td>{{ manageAmountFormat($trans->sum_am) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    Total
                                </td>
                                <td>{{ manageAmountFormat(count($lists) > 0 ? @$lists->sum('sum_debit') : 0) }}</td>
                                <td>{{ manageAmountFormat(count($lists) > 0 ? @$lists->sum('sum_credit') : 0) }}</td>
                                <td>{{ manageAmountFormat(count($lists) > 0 ? @$lists->sum('sum_am') : 0) }}</td>
                            </tr>
                        </tfoot>

                    </table>

                </div>

            </div>
        </div>
    </section>
@endsection
