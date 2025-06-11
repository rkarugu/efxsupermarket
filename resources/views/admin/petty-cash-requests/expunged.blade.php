@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Expunged Petty Cash Requests </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form action="">
                        {{ @csrf_field() }}

                        <div class="row d-flex" style="align-items: flex-end">
                            <div class="col-md-2 form-group">
                                <label for="start-date">Start Date</label>
                                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request()->get('start_date')}}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="end-date">End Date</label>
                                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request()->get('end_date')}}">
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="branch" class="mlselect form-control">
                                    <option value="" selected disabled></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                                {{ $branch->id == request()->branch ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="petty-cash-type">Petty Cash Type</label>
                                <select class="mlselect form-control" name="type" id="petty-cash-type">
                                    <option value="" selected disabled></option>
                                    @foreach ($pettyCashTypes as $pettyCashType)
                                        <option value="{{ $pettyCashType->slug }}" {{ request()->get('type') == $pettyCashType->slug ? 'selected' : '' }}>{{ $pettyCashType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-request.expunged') !!}">Clear </a>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Expunged Date</th>
                            <th>Branch</th>
                            <th>Account</th>
                            <th>Petty Cash No</th>
                            <th>Petty Cash Type</th>
                            <th>Payee Name</th>
                            <th>Payee Phone No</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pettyCashRequestItems as $i => $requestItem)
                            <tr>
                                <th style="width: 3%;" scope="row">{{ ++$i }}</th>
                                <td>{{ $requestItem->expunged_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $requestItem->pettyCashRequest->restaurant->name }}</td>
                                <td>{{ $requestItem->pettyCashRequest->chartofAccount->account_name }}</td>
                                <td>{{ $requestItem->pettyCashRequest->petty_cash_no }}</td>
                                <td>
                                    @php
                                        $driverGrnRef = $requestItem->grn?->grn_number ?? $requestItem->transfer?->transfer_no;
                                    @endphp
                                    <span>{{ $requestItem->pettyCashRequest->pettyCashType?->name }}</span>
                                    @if ($driverGrnRef)
                                        <span>({{ $driverGrnRef }})</span>
                                    @endif
                                </td>
                                <td>{{ $requestItem->payee_name }}</td>
                                <td>{{ $requestItem->payee_phone_no }}</td>
                                <td class="text-right">{{ number_format($requestItem->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="8" style="text-align: right">Total</th>
                            <td>{{ number_format($pettyCashRequestItems->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
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
            $(".mlselect").select2({
                placeholder: 'Select...'
            });
        });
    </script>
@endsection
