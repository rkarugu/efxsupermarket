@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Petty Cash Detailed Log </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form>
                        @csrf

                        <div class="row d-flex" style="align-items: flex-end">
                            <div class="col-md-2 form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $dates[0] }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $dates[1] }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <select name="type" id="type-select">
                                    <option value="" selected disabled></option>
                                    <option value="travel-order-taking" {{ request()->type == 'travel-order-taking' ? 'selected' : '' }}>Order Taking</option>
                                    <option value="travel-delivery" {{ request()->type == 'travel-delivery' ? 'selected' : '' }}>Travel Delivery</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success">Filter</button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.summary-log') !!}">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order/Delivery Date</th>
                                <th>Petty Cash Type</th>
                                <th>Initiated By</th>
                                <th>Initiated Date</th>
                                <th>Approved By</th>
                                <th>Approved Date</th>
                                <th>Approved Transactions</th>
                                <th>Approved Amount</th>
                                <th>Failed Transactions</th>
                                <th>Failed Amount</th>
                                <th>Successful Transactions</th>
                                <th>Disbursed Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $i => $log)
                                <tr>
                                    <th>{{ $i + 1 }}</th>
                                    <td>{{ $log->order_delivery_date }}</td>
                                    <td>{{ $log->formatted_petty_cash_type }}</td>
                                    <td>{{ $log->initiatedBy->name }}</td>
                                    <td>{{ $log->initiated_time->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $log->approvedBy?->name }}</td>
                                    <td>{{ $log->approved_time?->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $log->approved_transactions }}</td>
                                    <td class="text-right">{{ number_format($log->approved_amount, 2) }}</td>
                                    <td>{{ $log->failed_transactions }}</td>
                                    <td class="text-right">{{ number_format($log->pending_amount ?? 0, 2) }}</td>
                                    <td>{{ $log->successful_transactions }}</td>
                                    <td class="text-right">{{ number_format($log->disbursed_amount ?? 0, 2) }}</td>
                                    <td style="text-align: center">
                                        <div class="action-button-div">
                                            <a href="{{ route('petty-cash-approvals.log-transactions', ['id' => $log->id]) }}" title="View Transactions" target="_blank">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="14"></td>
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <th class="text-right">Totals</th>
                                <th class="text-right">{{ number_format($logs->sum('approved_amount'), 2) }}</th>
                                <td colspan="3"></td>
                                <th class="text-right">{{ number_format($logs->sum('disbursed_amount'), 2) }}</th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        .text-right {
            text-align: right;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');

            $("#type-select").select2({
                placeholder: 'Select Petty Cash Type...'
            });
        });
    </script>
@endsection
