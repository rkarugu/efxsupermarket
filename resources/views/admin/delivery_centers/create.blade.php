@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Delivery Center </h3>

                    <a href="{{ route("$base_route.index") }}" class="btn btn-outline-primary"> << Back to Delivery Centers </a>
                </div>
            </div>

            <div class="box-body">
                @include('message')

                <form action="{{ route("$base_route.store") }}" method="post" class="form-horizontal">
                    {{ @csrf_field() }}

                    @php
                        $routeIdValue = old('route_id');
                        if (request()->query('route_id')) {
                            $routeIdValue = request()->query('route_id');
                        }
                    @endphp

                    <div class="form-group">
                        <label for="route_id" class="control-label col-md-2"> Route </label>
                        <div class="col-md-10">
                            {!!Form::select('route_id', $routes, $routeIdValue, ['placeholder'=>'Select Route', 'class' => 'form-control', 'id' => 'search-routes', 'required'=>true ])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="control-label col-md-2"> Center Name </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Delivery center name" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="control-label col-md-2"> Center Location </label>
                        <div class="col-md-10">
                            <input type="text" id="center-location" name="center_location_name" class="form-control" placeholder="Search center to prefill coordinates"
                                   value="{{ old('center_location_name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lat" class="control-label col-md-2"> Latitude </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="lat" id="lat" value="{{ old('lat') }}" placeholder="Latitude">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lat" class="control-label col-md-2"> Longitude </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="lng" id="lng" value="{{ old('lng') }}" placeholder="Longitude">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="preferred_center_radius" class="control-label col-md-2"> Preferred Center Radius (Meters) </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" name="preferred_center_radius" id="preferred_center_radius"
                                   placeholder="100" min="0" step="0.01"
                                   value="{{ old('preferred_center_radius') ?? 1000 }}">
                            <div class="form-text"> Determines how big the center will be drawn in its route plan map. </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script async src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}&callback=initMap"
            defer></script>

    <script type="text/javascript">
        $("#search-routes").select2();

        // Center location search
        let map;

        async function initMap() {
            const input = document.getElementById("center-location");
            const options = {
                componentRestrictions: {country: "ke"},
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