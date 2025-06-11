@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Processed Requests </h3>
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
                                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request()->get('start_date') ?? date('Y-m-d') }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="end-date">End Date</label>
                                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request()->get('end_date') ?? date('Y-m-d') }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="branch">Branch</label>
                                <select class="form-control" name="branch" id="branch">
                                    <option value="" selected disabled></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request()->get('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="petty-cash-type">Petty Cash Type</label>
                                <select class="form-control" name="type" id="petty-cash-type">
                                    <option value="" selected disabled></option>
                                    @foreach ($pettyCashTypes as $pettyCashType)
                                        <option value="{{ $pettyCashType->slug }}" {{ request()->get('type') == $pettyCashType->slug ? 'selected' : '' }}>{{ $pettyCashType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success">Filter</button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-request.processed') !!}">Clear</a>
                            </div>
                            
                        </div>
                    </form>
                </div>

                <hr>

                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Account</th>
                            <th>Petty Cash No</th>
                            <th>Petty Cash Type</th>
                            <th>Payee Name</th>
                            <th>Payee Phone No</th>
                            <th>Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($requests as $i => $request)
                            <tr>
                                <th style="width: 3%;" scope="row">{{ ++$i }}</th>
                                <td>{{ $request->pettyCashRequest->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $request->pettyCashRequest->restaurant->name }}</td>
                                <td>{{ $request->pettyCashRequest->chartofAccount->account_name }}</td>
                                <td>
                                    <a href="{{ route('petty-cash-request.processed-details', $request->pettyCashRequest->petty_cash_no) }}">
                                        {{ $request->pettyCashRequest->petty_cash_no }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $driverGrnRef = $request->grn?->grn_number ?? $request->transfer?->transfer_no;
                                    @endphp
                                    <span>{{ $request->pettyCashRequest->pettyCashType?->name }}</span>
                                    <span>({{ $driverGrnRef }})</span>
                                </td>
                                <td>{{ $request->payee_name }}</td>
                                <td>{{ $request->payee_phone_no }}</td>
                                <td style="text-align: right">{{ number_format($request->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="8" style="text-align: right">Total</th>
                            <td>{{ number_format($requests->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $('select').select2({
            placeholder: 'Select...'
        })
    </script>
@endpush
