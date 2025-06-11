@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="session-message-container">
                @include('message')
            </div>

            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Vehicle Models </h3>

                    <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-primary"> Add Model </a>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th> Name </th>
                            <th>Vehicle Type</th>
                            <th>Supplier</th>
                            <th>Max Load</th>
                            <th>Fuel Tank</th>
                            <th>Travel Expense</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($vehicleModels as $index => $vehicleModel)
                            <tr>
                                <th scope="row" style="width: 5%;">{{ $index + 1 }}</th>
                                <td> {{ $vehicleModel->name }} </td>
                                <td>{{ $vehicleModel->type?->name }}</td>
                                <td>{{ getVehicleSupplier($vehicleModel->suppliers)->name}}</td>
                                <td>{{$vehicleModel->ma_load_capacity}}T</td>
                                <td>{{$vehicleModel->fuel_tank_capacity}}L</td>
                                <td>{{ format_amount_with_currency($vehicleModel->travel_expense) }}</td>

                                <td><a href="{{ route("$base_route.edit", $vehicleModel->id) }}"><i class="fas fa-pen"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
