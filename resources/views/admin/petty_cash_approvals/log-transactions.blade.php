@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Petty Cash Log Transactions </h3>
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
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $i => $transaction)
                            <tr>
                                <th>{{ $i + 1 }}</th>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $transaction->document_no }}</td>
                                <td>{{ $transaction->user->name }}</td>
                                <td>{{ $transaction->user->phone_number }}</td>
                                @php
                                    $status = '';

                                    if ($transaction->child) {
                                        if ($transaction->child->call_back_status == 'complete') {
                                            $status = 'Completed';
                                        } else {
                                            $status = 'Failed';
                                        }
                                    } else {
                                        $status = 'Pending';
                                    }
                                @endphp
                                <td>{{ $status }}</td>
                                <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7"></td>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-right">Total</th>
                            <th class="text-right">{{ number_format($transactions->sum('amount') , 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection
