@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $station->name }} | Edit </h3>

                    <a href="{{ route("$base_route.index") }}" class="btn btn-success">
                        << Back to Stations List </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route("$base_route.update", $station->id) }}" method="post" class="form-horizontal">
                    <input type="hidden" name="_method" value="PUT">
                    {{ @csrf_field() }}

                    <div class="form-group">
                        <label for="name" class="control-label col-md-2"> Station Name </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="name" id="name"
                                   placeholder="Fuel station name" required value="{{ $station->name }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="branch_id" class="control-label col-md-2"> Branch </label>
                        <div class="col-md-10">
                            <select name="branch_id" id="branch_id" class="form-control" required>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" @if($station->branch_id == $restaurant->id) selected @endif>
                                        {{ $restaurant->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location_name" class="control-label col-md-2"> Location </label>
                        <div class="col-md-10">
                            <input type="text" id="location_name" name="location_name" class="form-control"
                                   placeholder="Search location" value="{{ $station->location_name }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lat" class="control-label col-md-2"> Latitude </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="lat" id="lat"
                                   value="{{ $station->lat }}" placeholder="Latitude">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lat" class="control-label col-md-2"> Longitude </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="lng" id="lng"
                                   value="{{ $station->lng }}" placeholder="Longitude">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fuel_price" class="control-label col-md-2"> Diesel Price </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" name="fuel_price" id="fuel_price"
                                   value="{{ $station->fuel_price }}" placeholder="Diesel price" min="0" step="any">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="supplier" class="control-label col-md-2"> Supplier </label>
                        <div class="col-md-10">
                            <select name="supplier" id="supplier" class="form-control" required>
                                <option value="" selected disabled> Select a Supplier </option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{$supplier->id == $station->fuel_supplier_id ? 'selected': ''}}> {{ $supplier->supplierDetails?->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div> 

                    <div class="box-footer">
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script async src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}&callback=initMap" defer></script>

    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        $("#branch_id").select2();
        $("#supplier").select2();

    </script>

    <script type="text/javascript">
        let map;

        async function initMap() {
            const input = document.getElementById("location_name");
            const options = {
                componentRestrictions: {
                    country: "ke"
                },
                fields: ["geometry", "name"],
                strictBounds: false,
            };
            const autocomplete = new google.maps.places.Autocomplete(input, options);

            // let autocomplete = new google.maps.places.Autocomplete(input, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                let place = autocomplete.getPlace();
                let lat = place.geometry.location.lat();
                let lng = place.geometry.location.lng();
                $("#lat").val(lat);
                $("#lng").val(lng);
            });
        }

        window.initMap = initMap;
    </script>
@endsection
