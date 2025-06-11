@extends('layouts.admin.admin')

@php
    $startDate = request()->get('start_date') ?? date('Y-m-d');
    $endDate = request()->get('end_date') ?? date('Y-m-d');
    $branchId = request()->get('branch');
    $type = request()->get('type');
@endphp

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Failed Requests </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form>
                        @csrf

                        <div class="row d-flex" style="align-items: flex-end">
                            <div class="col-md-2">
                                <label for="start-date">Start Date</label>
                                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ $startDate }}">
                            </div>

                            <div class="col-md-2">
                                <label for="end-date">End Date</label>
                                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ $endDate }}">
                            </div>

                            <div class="col-md-2">
                                <label for="branch">Branch</label>
                                <select class="form-control" name="branch" id="branch">
                                    <option value="" selected disabled></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="petty-cash-type">Petty Cash Type</label>
                                <select class="form-control" name="type" id="petty-cash-type">
                                    <option value="" selected disabled></option>
                                    @foreach ($pettyCashTypes as $pettyCashType)
                                        <option value="{{ $pettyCashType->slug }}" {{ $type == $pettyCashType->slug ? 'selected' : '' }}>{{ $pettyCashType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <div>
                                    <button type="submit" class="btn btn-success">Filter</button>
                                    <a class="btn btn-success ml-12" href="{!! route('petty-cash-request.failed') !!}">Clear</a>
                                </div>
                            </div>
                            
                        </div>
                    </form>
                </div>

                <hr style="margin-top: 10px">

                <form action="{{ route('petty-cash-request.failed-batch-action') }}" method="post">
                    @csrf

                    <input type="hidden" name="ids" value="{{ json_encode($failedRequestItems->pluck('id')->toArray()) }}">

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
                                <th class="text-right">Amount</th>
                                @if ((isset($user->permissions['petty-cash-requests-failed___resend']) || $user->role_id == '1') && $failedRequestItems->count())
                                    <th class="noneedtoshort">
                                        <input type="checkbox" name="resend_all" id="failed-deposists-resend-all-checkbox" checked>
                                    </th>
                                @endif
                            </tr>
                        </thead>
    
                        <tbody>
                            @foreach($failedRequestItems as $i => $requestItem)
                                <tr>
                                    <th style="width: 3%;" scope="row">{{ ++$i }}</th>
                                    <td>{{ $requestItem->pettyCashRequest->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $requestItem->pettyCashRequest->restaurant->name }}</td>
                                    <td>{{ $requestItem->pettyCashRequest->chartofAccount->account_name }}</td>
                                    <td>{{ $requestItem->pettyCashRequest->petty_cash_no }}</td>
                                    <td>
                                        @php
                                            $driverGrnRef = $requestItem->grn?->grn_number ?? $requestItem->transfer?->transfer_no;
                                        @endphp
                                        <span>{{ $requestItem->pettyCashRequest->pettyCashType?->name }}</span>
                                        <span>({{ $driverGrnRef }})</span>
                                    </td>
                                    <td>{{ $requestItem->payee_name }}</td>
                                    <td>{{ $requestItem->payee_phone_no }}</td>
                                    <td class="text-right">{{ number_format($requestItem->amount, 2) }}</td>
                                    @if (isset($user->permissions['petty-cash-requests-failed___resend']) || $user->role_id == '1')
                                        <td>
                                            <input type="checkbox" name="resend_{{ $requestItem->id }}" class="failed-deposits-resend-checkbox" checked>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="8" style="text-align: right">Total</th>
                                <td>{{ number_format($failedRequestItems->sum('amount'), 2) }}</td>
                                @if (isset($user->permissions['petty-cash-requests-failed___resend']))
                                    <td></td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>

                    @if (count($failedRequestItems) > 0 && ($user->role_id == 1 || isset($user->permissions['petty-cash-requests-failed___resend'])))
                        <div style="text-align: right;">
                            <input type="submit" class="btn btn-warning" name="expunge" value="Expunge">
                            <input type="submit" class="btn btn-primary" value="Recheck & Resend">
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        .text-right {
            text-align: right;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $("#failed-deposists-resend-all-checkbox").change(function () {
            if ($(this).prop('checked')) {
                $(".failed-deposits-resend-checkbox").attr('checked', true);
            } else {
                $(".failed-deposits-resend-checkbox").attr('checked', false);
            }
        });
        
        $('select').select2({
            placeholder: 'Select...'
        })
    </script>
@endpush

