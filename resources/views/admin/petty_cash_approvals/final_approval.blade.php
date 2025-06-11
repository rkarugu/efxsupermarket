@extends('layouts.admin.admin')

<style>
    .major-detail {
        border: 2px solid;
        border-radius: 15px;
        padding: 10px 15px;
        height: 80px;
        margin-right: 20px;
    }

    .major-detail.border-primary {
        border-color: #0d6efd;
    }

    .major-detail.border-success {
        border-color: #198754;
    }

    .major-detail.border-danger {
        border-color: #dc3545;
    }

    .major-detail.border-info {
        border-color: #0dcaf0;
    }

    .major-detail-icon {
        font-size: 20px;
    }

    .major-detail-title {
        font-size: 18px;
        font-weight: 500;
        margin-left: 12px;
        margin-top: -5px;
    }

    .major-detail-value {
        font-size: 20px;
        font-weight: 600;
    }

    #activity {
        position: relative;
        width: 40%;
    }

    .mt-20 {
        margin-top: 30px !important;
    }
</style>

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Petty Cash Final Approvals </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="d-flex justify-content-end">
                    
                </div>

                <div class="filters">
                    <form action="">
                        {{ @csrf_field() }}

                        <div class="row">
                            <div class="col-md-2 form-group">
                                <input type="date" name="date" id="date" class="form-control" value="{{ request()->get('date') }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <select name="branch" id="branch" class="mlselect form-control">
                                    <option value="" selected disabled>Select branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                                {{ $branch->id == request()->branch ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.initial') !!}">Clear </a>
                            </div>

                            <div class="major-detail d-flex flex-column justify-content-between border-success" style="float: right">
                                <div class="d-flex">
                                    <i class="fas fa-money major-detail-icon"></i>
                                    <span class="major-detail-title"> Paybill Balance </span>
                                </div>
        
                                <span class="major-detail-value" style="text-align: right"> N/A </span>
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
                        <th> Transaction Count</th>
                        <th> Approved By</th>
                        <th> Approval Date</th>
                        <th style="text-align: right;"> Approved Amount</th>
                        <th> Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($pendingApprovals as $record)
                        <tr>
                            <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                            <td> {{ \Carbon\Carbon::parse($record->date_time)->format('d-m-Y') }} </td>
                            <td> {{ ucwords(str_replace('_', ' ', $record->type)) }} </td>
                            <td> {{ $record->count }} </td>
                            <td> {{ $record->approver }} </td>
                            <td> {{ \Carbon\Carbon::parse($record->initial_approval_time)->format('d-m-Y H:i:s') }} </td>
                            <td style="text-align: right;"> {{ manageAmountFormat($record->total_amount) }} </td>
                            <td>
                                <div class="action-button-div">
                                    <a href="{{ route('petty-cash-approvals.final.lines', ['date' => $record->date_time, 'type' => $record->type]) }}" title="View Transactions" target="_blank">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                        <td colspan="6"></td>
                        <th style="text-align: right;"> {{ manageAmountFormat($pendingApprovals->sum('total_amount')) }} </th>
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
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $(".mlselect").select2();
        });
    </script>
@endsection
