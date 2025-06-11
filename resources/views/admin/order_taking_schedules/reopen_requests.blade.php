@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Salesman Shift Re-open Requests </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable">
                        <thead>
                        <tr>
                            <th scope="col" width="5%"> # </th>
                            <th scope="col"> Salesman </th>
                            <th scope="col"> Phone Number </th>
                            <th scope="col"> Route </th>
                            <th scope="col"> Reason </th>
                            <th scope="col"> Actions </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($requests as $index => $reopenRequest)
                            <tr>
                                <th scope="row" width="5%"> {{ $index + 1 }}</th>
                                <td> {{ $reopenRequest->getShift?->salesman?->name }} </td>
                                <td>{{ $reopenRequest->getShift?->salesman?->phone_number }}</td>
                                <td>{{ $reopenRequest->getShift?->route }}</td>
                                <td>{{ $reopenRequest->reason }}</td>
                                <td>
                                    <div class="action-button-div">
                                        <form action="{{ route('salesman-shift.reopen-requests.approve', $reopenRequest->id) }}"
                                              method="post"
                                              title="Approve request" style="display: inline-block;">
                                            {{ @csrf_field() }}

                                            <button type="submit"><i class="fa fa-check-square text-success fa-lg"></i></button>
                                        </form>

                                        <form action="{{ route('salesman-shift.reopen-requests.decline', $reopenRequest->id) }}"
                                              method="post"
                                              title="Decline request" style="display: inline-block;">
                                            {{ @csrf_field() }}

                                            <button type="submit"><i class="fa fa-times-rectangle text-danger fa-lg"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
