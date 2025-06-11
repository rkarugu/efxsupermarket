@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Request Transactions </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Document No.</th>
                            <th>Recipient</th>
                            <th>Phone No.</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requestItems as $i => $requestItem)
                            <tr>
                                <th>{{ $i + 1 }}</th>
                                <td>{{ $requestItem->latestWithdrawal?->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $requestItem->latestWithdrawal?->document_no }}</td>
                                <td>{{ $requestItem->payee_name }}</td>
                                <td>{{ $requestItem->payee_phone_no }}</td>
                                <td>{{ number_format($requestItem->amount, 2) }}</td>
                                @php
                                    $status = '';

                                    if ($requestItem->latestWithdrawal) {
                                        if ($requestItem->latestWithdrawal->call_back_status == 'complete') {
                                            $status = 'Successful';
                                        } else {
                                            $status = 'Failed';
                                        }
                                    } else {
                                        $status = 'Pending';
                                    }
                                @endphp
                                <td>{{ $status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
