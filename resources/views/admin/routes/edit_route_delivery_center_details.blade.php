@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="add-route-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Route </h3>

                    <a href="{{ route("manage-route-linked-centers-list", $center->route_id) }}" class="btn btn-outline-primary">
                        << Back to Route List </a>
                </div>
            </div>


            <div class="box-body">
                @include('message')


                <form action="{{ route('update-get-center-update-details') }}" method="post" class="form-horizontal">
                    {{ @csrf_field() }}


                    <div class="route-details-tab">
                        <input name="selected_center_id" type="hidden" id="selected_center_id"
                               value="{{ $center->id}}" required />
                        <div class="form-group">
                            <label for="route_name" class="control-label col-md-2"> Center Name </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="update_center_name" id="route_name"
                                       placeholder="Route name" required
                                       value="{{ $center->name ?? old('update_center_name') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="starting_location_names" class="control-label col-md-2"> Center Location
                                Name </label>
                            <div class="col-md-10">
                                <input type="text" name="center_location_name" id="update_center_location_name"
                                       class="form-control" placeholder="Search location ...."
                                       value="{{ $center->center_location_name ?? old('') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="location-latitude" class="control-label col-md-2"> Latitude </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="update_center_latitude" id="location-latitude"
                                       value="{{ $center->lat }}" placeholder="Latitude">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="location-longitude" class="control-label col-md-2"> Longitude </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="update_center_longitude" id="location-longitude"
                                       value="{{ $center->lng }}" placeholder="Longitude">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="update_preferred_center_radius" class="control-label col-md-2"> Preferred Center
                                Radius
                                (Meters) </label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="update_preferred_center_radius"
                                       id="update_preferred_center_radius"
                                       placeholder="10" min="0" step="0.01"
                                       value="{{ $center->preferred_center_radius ?? old('update_preferred_center_radius') }}">
                            </div>

                        </div>


                    </div>

                    <div class="box-footer" style="margin-top:25px;float: right;">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script>

    <script type="text/javascript">

        function initializeRouteLocationSearch() {
            let StartingLocationInput = document.getElementById('update_center_location_name');
            let locationoptions = {};

            let autocomplete = new google.maps.places.Autocomplete(StartingLocationInput, locationoptions);

            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                let location_place = autocomplete.getPlace();
                let location_lat = location_place.geometry.location.lat();
                let location_lng = location_place.geometry.location.lng();
                $("#location-latitude").val(location_lat);
                $("#location-longitude").val(location_lng);
            });
        }

        google.maps.event.addDomListener(window, 'load', initializeRouteLocationSearch);
    </script>

@endsection
