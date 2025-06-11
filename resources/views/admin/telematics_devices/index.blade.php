@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Telematics Devices </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable">
                        <thead>
                        <tr>
                            <th scope="col"> #</th>
                            <th scope="col"> Device ID </th>
                            <th scope="col"> Device S/No </th>
                            <th scope="col"> Device Name </th>
                            <th scope="col"> Assigned Vehicle </th>
                            <th scope="col"> Actions </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($devices as $index => $device)
                            <tr>
                                <th scope="row"> {{ $index + 1 }}</th>
                                <td> {{ $device['device_id'] }} </td>
                                <td> {{ $device['device_imei'] }} </td>
                                <td> {{ $device['device_name'] }} </td>
                                <td> {{ $device['vehicle'] ?? '-' }} </td>
                                <td>
                                    <div class="action-button-div">
                                        @if(!$device['vehicle'])
                                            <a href="{{ route("$base_route.index", $device['db_id']) }}" title="Allocate Vehicle">
                                                <i class="fa fa-truck text-primary fa-lg" aria-hidden="true"></i>
                                            </a>
                                        @endif
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
