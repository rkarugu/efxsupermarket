@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Fuel Entry Details - {{$fuel_entry->lpo_number}} </h3>
                </div>
            </div>

            <div class="box-body">
                
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <h5><strong>Route:</strong></h5>
                            <p>{{$fuel_entry->getRelatedShift?->route?->route_name}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Delivery Schedule:</strong></h5>
                            <p>{{$fuel_entry->getRelatedShift?->deliveryNumber}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Vehicle:</strong></h5>
                            <p>{{$fuel_entry->getRelatedVehicle?->license_plate_number}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Driver:</strong></h5>
                            <p>{{$fuel_entry->getRelatedShift?->driver?->name}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Receipt #:</strong></h5>
                            <p>{{$fuel_entry->receipt_number}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Status:</strong></h5>
                            <p>{{$fuel_entry->entry_status}}</p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <h5><strong>Standard Distance:</strong></h5>
                            <p>{{$fuel_entry->getRelatedShift?->route?->manual_distance_estimate . ' kms'}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Actual Distance:</strong></h5>
                            <p>{{$fuel_entry->manual_distance_covered . ' kms'}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Distance Variance:</strong></h5>
                            <p>{{($fuel_entry->getRelatedShift?->route?->manual_distance_estimate - $fuel_entry->manual_distance_covered) . ' kms'}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Standard Fuel:</strong></h5>
                            <p>{{$fuel_entry->getRelatedShift?->route?->manual_fuel_estimate . ' lts'}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Actual Fuel:</strong></h5>
                            <p>{{$fuel_entry->actual_fuel_quantity . ' lts'}}</p>
                        </div>
                        <div class="col-md-2">
                            <h5><strong>Fuel Variance:</strong></h5>
                            <p>{{($fuel_entry->getRelatedShift?->route?->manual_fuel_estimate - $fuel_entry->actual_fuel_quantity) . ' lts'}}</p>
                        </div>

                    </div>
                </div>
            </div>
            
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box-header with-border">
                            <div class="box-header-flex">
                                <h3 class="box-title"> DashBoard Photo </h3>
                            </div>
                        </div>
                        <div class="box-body">
                            <img src="{{ asset('uploads/dashboard_photos/' . $fuel_entry->dashboard_photo) }}" width="500px" height="300px"alt="image">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box-header with-border">
                            <div class="box-header-flex">
                                <h3 class="box-title"> Receipt Photo </h3>
                            </div>
                        </div>
                        <div class="box-body">
                            <img src="{{ asset('uploads/dashboard_photos/' . $fuel_entry->receipt_photo) }}" width="500px" height="300px"alt="image">
                        </div>
                        
                    </div>

                </div>
                
            </div>
            
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .table{
            overflow-x: auto;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@endsection
