@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Petty Cash Summary Log </h3>
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
                                <a 
                                    href="{{ route(
                                        'petty-cash-approvals.summary-log-print', 
                                        [
                                            'start_date' => request()->start_date,
                                            'end_date' => request()->end_date,
                                            'type' => request()->type,
                                        ]
                                    ) }}" 
                                    target="_blank" 
                                    class="btn btn-primary ml-12"
                                >
                                    Export To PDF
                                </a>
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
                                <th>Approved Date</th>
                                <th>Petty Cash Type</th>
                                <th>Total Transactions</th>
                                <th>Approved Amount</th>
                                <th>Disbursed Amount</th>
                                <th>Failed Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedLogs as $i => $groupedLog)
                                <tr>
                                    <th>{{ $i + 1 }}</th>
                                    <td>{{ $groupedLog->approved_date }}</td>
                                    @php
                                        $type = '';

                                        if ($groupedLog->petty_cash_type == 'travel-order-taking') {
                                            $type = 'Order Taking';
                                        } else if ($groupedLog->petty_cash_type == 'travel-delivery') {
                                            $type = 'Travel Delivery';
                                        }
                                    @endphp
                                    <td>{{ $type }}</td>
                                    <td>{{ $groupedLog->approved_transactions }}</td>
                                    <td class="text-right">{{ number_format($groupedLog->approved_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($groupedLog->disbursed_amount ?? 0, 2) }}</td>
                                    <td class="text-right">{{ number_format($groupedLog->failed_amount ?? 0, 2) }}</td>
                                    <td style="text-align: center">
                                        @if ($groupedLog->approved_date)
                                            <div class="action-button-div">
                                                <a href="{{ route('petty-cash-approvals.summary-log-transactions', ['type' => $groupedLog->petty_cash_type, 'date' => $groupedLog->approved_date]) }}" title="View Transactions" target="_blank">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>                                            
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Totals
                                </th>
                                <th class="text-right">{{ number_format($groupedLogs->sum('approved_amount') , 2) }}</th>
                                <th class="text-right">{{ number_format($groupedLogs->sum('disbursed_amount') , 2) }}</th>
                                <th class="text-right">{{ number_format($groupedLogs->sum('failed_amount') , 2) }}</th>
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
            $("#type-select").select2({
                placeholder: 'Select Petty Cash Type...'
            });
        });
    </script>
@endsection
