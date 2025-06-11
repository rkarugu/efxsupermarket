@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Rejected Petty Cash Deposits </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form action="">
                        {{ @csrf_field() }}

                        <div class="row d-flex" style="align-items: flex-end">
                            {{-- <div class="col-md-2 form-group">
                                <label for="start-date">Start Date</label>
                                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request()->get('start_date') ?? date('Y-m-d') }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="end-date">End Date</label>
                                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request()->get('end_date') ?? date('Y-m-d') }}">
                            </div> --}}

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
                                <label for="">Petty Cash Type</label>
                                <select name="type" class="mlselect form-control">
                                    <option value="" selected disabled></option>
                                    <option value="order-taking" {{ request()->type == 'order-taking' ? 'selected' : '' }}>Order Taking</option>
                                    <option value="delivery" {{ request()->type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.rejected-deposits') !!}">Clear </a>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th> Date</th>
                            <th> Petty Cash Type</th>
                            <th> Branch</th>
                            <th> Route</th>
                            <th> Recipient</th>
                            <th> Phone Number</th>
                            <th style="text-align: right;"> Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejectedDeposits as $record)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ \Carbon\Carbon::parse($record->date)->format('d-m-Y H:i:s') }} </td>
                                <td> {{ $record->role == 4 ? 'Order Taking' : 'Delivery' }} </td>
                                <td> {{ $record->branch }} </td>
                                <td> {{ $record->route }} </td>
                                <td> {{ $record->user }} </td>
                                <td> {{ $record->phone_number }} </td>
                                <td style="text-align: right;"> {{ manageAmountFormat($record->amount) }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td></td>
                            <th style="text-align: right;"> {{ manageAmountFormat($rejectedDeposits->sum('amount')) }} </th>
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
