@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Fuel Lpos Deliveries </h3>
                    <a href="{{url()->previous()}}" class="btn btn-success">Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <h4>Expected And Delivered Routes</h4>
                <table class="table" id="create_datatable">

                    <thead>
                        <tr>
                            <th scope="col"> #</th>
                            <th scope="col"> Delivery Date</th>
                            <th scope="col"> Shift Date</th>
                            <th scope="col">Delivery NO</th>
                            <th scope="col"> Route</th>
                            <th scope="col"> Tonnage</th>
                            <th scope="col"> Status</th>
                            <th scope="col"> Vehicle</th>
                            <th scope="col">Fuel</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalFuel = $totalAmount = 0;
                        @endphp
                        @foreach ($expectedAndDeliveredRoutes as $delivery)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{\Carbon\Carbon::parse($delivery->actual_delivery_date)->toDateString()}}</td>
                                <td>{{\Carbon\Carbon::parse($delivery->shift?->created_at)->toDateString()}}</td>
                                <td>{{$delivery->delivery_number}}</td>
                                <td>{{$delivery->route?->route_name}}</td>
                                <td>{{number_format($delivery->shift?->shift_tonnage,2)}}</td>
                                <td>{{$delivery->status}}</td>
                                <td>{{$delivery->vehicle?->license_plate_number}}</td>
                                <td style="text-align: center;">{{$delivery->fuel_entry?->actual_fuel_quantity}}</td>
                                <td style="text-align: right;">{{manageAmountFormat($delivery->fuel_entry?->actual_fuel_quantity * $delivery->fuel_entry?->fuel_price)}}</td>

                            </tr>
                            @php
                                $totalFuel +=  $delivery->fuel_entry?->actual_fuel_quantity ?? 0;
                                $totalAmount += $delivery->fuel_entry?->actual_fuel_quantity * $delivery->fuel_entry?->fuel_price?? 0;
                            @endphp
                            
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8">Total</th>
                            <th style="text-align: center;">{{ manageAmountFormat($totalFuel) }} Lts</th>
                            <th style="text-align: right;">{{ manageAmountFormat($totalAmount) }}</th>
                        </tr>
                    </tfoot>

                </table>
                <h4>Delivered and Unexpected</h4>
                <table class="table" id="create_datatable_10">

                    <thead>
                        <tr>
                            <th scope="col"> #</th>
                            <th scope="col">Expected Delivery Date</th>
                            <th scope="col"> Shift Date</th>
                            <th scope="col">Delivery NO</th>
                            <th scope="col"> Route</th>
                            <th scope="col"> Tonnage</th>
                            <th scope="col"> Status</th>
                            <th scope="col"> Vehicle</th>
                            <th scope="col">Fuel</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $totalFuel = $totalAmount = 0;
                    @endphp
                        @foreach ($unexpectedButDelivered as $delivery)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{\Carbon\Carbon::parse($delivery->expected_delivery_date)->toDateString()}}</td>
                                <td>{{\Carbon\Carbon::parse($delivery->shift?->created_at)->toDateString()}}</td>
                                <td>{{$delivery->delivery_number}}</td>
                                <td>{{$delivery->route?->route_name}}</td>
                                <td>{{number_format($delivery->shift?->shift_tonnage, 2)}}</td>
                                <td>{{$delivery->status}}</td>
                                <td>{{$delivery->vehicle?->license_plate_number}}</td>
                                <td style="text-align: center;">{{$delivery->fuel_entry?->actual_fuel_quantity}}</td>
                                <td style="text-align: right;">{{manageAmountFormat($delivery->fuel_entry?->actual_fuel_quantity * $delivery->fuel_entry?->fuel_price)}}</td>
                            </tr>
                            @php
                                $totalFuel +=  $delivery->fuel_entry?->actual_fuel_quantity ?? 0;
                                $totalAmount += $delivery->fuel_entry?->actual_fuel_quantity * $delivery->fuel_entry?->fuel_price?? 0;
                            @endphp
                            
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8">Total</th>
                            <th style="text-align: center;">{{ manageAmountFormat($totalFuel) }} Lts</th>
                            <th style="text-align: right;">{{ manageAmountFormat($totalAmount) }}</th>
                        </tr>
                    </tfoot>

                </table>
                <h4>Undelivered Routes</h4>
                <table class="table" id="create_datatable_10">

                    <thead>
                        <tr>
                            <th scope="col"> #</th>
                            <th scope="col">Expected Delivery Date</th>
                            <th scope="col"> Shift Date</th>
                            <th scope="col">Delivery NO</th>
                            <th scope="col"> Route</th>
                            <th scope="col"> Tonnage</th>
                            <th scope="col"> Status</th>
                            <th scope="col"> Vehicle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($undeliveredDeliveries as $delivery)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{\Carbon\Carbon::parse($delivery->expected_delivery_date)->toDateString()}}</td>
                                <td>{{\Carbon\Carbon::parse($delivery->shift?->created_at)->toDateString()}}</td>
                                <td>{{$delivery->delivery_number}}</td>
                                <td>{{$delivery->route?->route_name}}</td>
                                <td>{{number_format($delivery->shift?->shift_tonnage, 2)}}</td>
                                <td>{{$delivery->status}}</td>
                                <td>{{$delivery->vehicle?->license_plate_number}}</td>

                            </tr>
                            
                        @endforeach
                    </tbody>

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
            // $("body").addClass('sidebar-collapse');
        });
    </script>
@endsection