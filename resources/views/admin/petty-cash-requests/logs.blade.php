@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Request Logs </h3>
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
                                <label for="start-date">Start Date</label>
                                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ $dates[0] }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="end-date">End Date</label>
                                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ $dates[1] }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="petty-cash-type">Petty Cash Type</label>
                                <select class="form-control" id="petty-cash-type" name="type">
                                    <option value="" selected disabled></option>
                                    @foreach ($pettyCashTypes as $type)
                                        <option value="{{ $type->slug }}" {{ request()->type == $type->slug ? 'selected' : '' }} >{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <button type="submit" class="btn btn-success">Filter</button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-request.logs') !!}">Clear</a>
                            </div>

                            <div class="col-md-4 form-group">
                                <input type="submit" class="btn btn-success pull-right" value="Export to Excel" name="export">
                            </div>
                            
                        </div>
                    </form>
                </div>

                <hr>

                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Petty Cash Type</th>
                            <th>Initiated Date</th>
                            <th>Initiated By</th>
                            <th>Approved Date</th>
                            <th>Approved By</th>
                            <th>Total Payees</th>
                            <th class="text-right">Total Amount</th>
                            <th>Failed Payments</th>
                            <th class="text-right">Failed Amount</th>
                            <th>Successful Payments</th>
                            <th class="text-right">Disbursed Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($requests as $i => $request)
                            <tr>
                                <th style="width: 3%;" scope="row">{{ ++$i }}</th>
                                <td>{{ $request->pettyCashType?->name }}</td>
                                <td>{{ $request->initial_approval_date }}</td>
                                <td>{{ $request->initialApprover?->name }}</td>
                                <td>{{ $request->final_approval_date }}</td>
                                <td>{{ $request->finalApprover?->name }}</td>
                                <td>{{ $request->request_items_count }}</td>
                                <td class="text-right">{{ number_format($request->total_amount, 2) }}</td>
                                <td>{{ $request->failed_payments }}</td>
                                <td class="text-right">{{ number_format($request->failed_amount, 2) }}</td>
                                <td>{{ $request->successful_payments }}</td>
                                <td class="text-right">{{ number_format($request->successful_amount, 2) }}</td>
                                <td style="text-align: center">
                                    <div class="action-button-div">
                                        <a href="{{ route('petty-cash-request.log-transactions', ['id' => $request->id]) }}" title="View Transactions" target="_blank">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="13"></td>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-right">Totals</th>
                            <th class="text-right">{{ number_format($requests->sum('total_amount'), 2) }}</th>
                            <td colspan="3"></td>
                            <th class="text-right">{{ number_format($requests->sum('successful_amount'), 2) }}</th>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        .text-right {
            text-align: right
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');

            $("#petty-cash-type").select2({
                placeholder: 'Select Petty Cash Type...'
            });
        });
    </script>
@endsection
