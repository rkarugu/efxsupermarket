@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Fuel Stations </h3>
                    <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-success"> Add Fuel Station </a>
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
                            <th scope="col"> Station Name</th>
                            <th scope="col"> Branch </th>
                            <th scope="col"> Diesel Price </th>
                            <th scope="col">Suplier</th>
                            <th scope="col"> Actions</th>

                        </tr>
                        </thead>

                        <tbody>
                        @foreach($stations as $index => $station)
                            <tr>
                                <th scope="row"> {{ $index + 1 }}</th>
                                <td> {{ $station->name }} </td>
                                <td> {{ $station->branch->name }} </td>
                                <td> {{ $station->display_diesel_price }} </td>
                                <td> {{ $station->fuelSupplier?->supplierDetails?->name }} </td>
                                <td>
                                    <div class="action-button-div">
                                        <a href="{{ route("$base_route.edit", $station->id) }}" title="Edit Station">
                                            <i class="fa fa-pencil-square text-primary fa-lg" aria-hidden="true"></i>
                                        </a>

{{--                                        <a href="javascript:void(0);" title="Remove Station" onclick="confirmStationDeletion()">--}}
{{--                                            <i class="fa fa-trash text-danger fa-lg" aria-hidden="true"></i>--}}
{{--                                            <form action="{{ route("$base_route.destroy", $station->id) }}" method="post" id="delete-station-form"--}}
{{--                                                  style="display: inline-block;">--}}
{{--                                                <input type="hidden" name="_method" value="DELETE">--}}
{{--                                                {{ @csrf_field() }}--}}
{{--                                            </form>--}}
{{--                                        </a>--}}
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