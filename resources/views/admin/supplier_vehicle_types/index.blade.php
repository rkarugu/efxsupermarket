@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>
                    <a href="{{ route('supplier-vehicle-type.create') }}" class="btn btn-primary">Add New Vehicle Type</a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Vehicle Type</th>
                                <th>Tonnage</th>
                                <th>Offloading Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicleTypes as $vehicleType)
                                <tr>
                                    <td>{{ $vehicleType->name }}</td>
                                    <td>{{ $vehicleType->vehicle_type }}</td>
                                    <td>{{ $vehicleType->tonnage }}</td>
                                    <td>{{ date('H:i',strtotime($vehicleType->offloading_time)) }} hrs</td>
                                    <td>
                                        <a href="{{ route('supplier-vehicle-type.edit', $vehicleType->id) }}" class="btn btn-warning">Edit</a>
                                        <form action="{{ route('supplier-vehicle-type.destroy', $vehicleType->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
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