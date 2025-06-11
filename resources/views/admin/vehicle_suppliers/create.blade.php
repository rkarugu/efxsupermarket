@extends('layouts.admin.admin')

@section('content')
@php
$user = getLoggeduserProfile();
@endphp
<script>
window.user = {!! $user !!}
</script>
    <section class="content">
        <div class="box box-primary">
            <div class="session-message-container">
                @include('message')
            </div>

            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Vehicle Suppliers | Add Supplier </h3>

                    <a href="{{ route("$base_route.index") }}" role="button" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <form method="post" action="{{ route("$base_route.store") }}" class="form-horizontal">
                    {{ @csrf_field() }}
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Supplier Name </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Supplier name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Email </label>
                        <div class="col-md-10">
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Email Address">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Phone Number </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="phone" name="phone" required placeholder="Phone Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Physical Address </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="physical_address" name="physical_address" required placeholder="Physical Address">
                        </div>
                    </div>
                    <div class="form-group" v-if="routeIsPhysical">
                        <label for="starting_location_name" class="control-label col-md-2"> Supplier Location </label>
                        <div class="col-md-10">
                            <input type="text" name="starting_location_name" id="starting_location_name"
                                   class="form-control google_location" placeholder="Search location ..." >
                        </div>
                    </div>

                    <div class="form-group" v-if="routeIsPhysical">
                        <label for="location-latitude" class="control-label col-md-2"> Latitude </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="loading_latitude" id="location-latitude" placeholder="Latitude"
                                  >
                        </div>
                    </div>

                    <div class="form-group" v-if="routeIsPhysical">
                        <label for="location-longitude" class="control-label col-md-2"> Longitude </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="loading_longitude" id="location-longitude" placeholder="Longitude"
                                   >
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary"> Add Supplier </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        $(document).ready(function(){
            var input = document.getElementById('starting_location_name');
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                $('#location-latitude').val(place.geometry.location.lat());
                $('#location-longitude').val(place.geometry.location.lng());
            });
        });
    </script>
    
  
    
@endsection


