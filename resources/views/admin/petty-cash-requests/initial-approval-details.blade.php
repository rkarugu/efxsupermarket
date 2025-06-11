@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Requests Details </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Payee Name</th>
                            <th>Payee Phone No.</th>
                            <th>Amount</th>
                            <th>Payment Reason</th>
                            @if ($pettyCashRequest->type == 'travel-delivery')
                                <th>Delivery Schedule</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pettyCashRequestItems as $i => $requestItem)
                            <tr>
                                <th style="width: 3%;" scope="row">{{ ++$i }}</th>
                                <td>{{ $requestItem->payee_name }}</td>
                                <td>{{ $requestItem->payee_phone_no }}</td>
                                <td>{{ number_format($requestItem->amount, 2) }}</td>
                                <td>{{ $requestItem->payment_reason }}</td>
                                @if ($pettyCashRequest->type == 'travel-delivery')
                                    <td>{{ $requestItem->deliverySchedule->delivery_number }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
