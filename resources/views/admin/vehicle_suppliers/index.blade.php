@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="session-message-container">
                @include('message')
            </div>

            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Vehicle Suppliers </h3>

                    <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-primary"> Add Supplier </a>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th> Name </th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Physical Adddress</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($suppliers as $index => $supplier)
                            <tr>
                                <th scope="row" style="width: 5%;">{{ $index + 1 }}</th>
                                <td> {{ $supplier->name }} </td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{$supplier->physical_address}}</td>
                                <td><a href="{{ route("$base_route.edit", $supplier->id) }}"><i class="fas fa-pen"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
