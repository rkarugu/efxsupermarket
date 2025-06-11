@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="add-route-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Route </h3>

                    <a href="{{ route("$base_route.index") }}" class="btn btn-outline-primary">
                        << Back to Route List </a>
                </div>
            </div>
            

            
            <div class="box-body">
                @include('message')
                

                <form action="{{ route("$base_route.store") }}" method="post" class="form-horizontal">
                    {{ @csrf_field() }}

                   
                    <div class="route-details-tab">
                        <div class="form-group">
                            <label for="route_name" class="control-label col-md-2"> Route Name </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="route_name" id="route_name"
                                    placeholder="Route name" required value="{{ old('route_name') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="restaurant_id" class="control-label col-md-2"> Branch </label>
                            <div class="col-md-10">
                                <select name="restaurant_id" id="restaurant_id" class="form-control" required>
                                    <option value="" selected disabled> Select a branch</option>
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}"> {{ $restaurant->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_physical_route" class="control-label col-md-2"> Physical Route </label>
                            <div class="col-md-10">
                                <input type="checkbox" id="is_physical_route" v-model="routeIsPhysical">
                                <input type="hidden" name="is_physical_route" :value="routeIsPhysical ? 1 : 0">
                            </div>
                        </div>
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="starting_location_name" class="control-label col-md-2"> Loading Location </label>
                            <div class="col-md-10">
                                <input type="text" name="starting_location_name" id="starting_location_name"
                                    class="form-control google_location" placeholder="Search location ...">
                            </div>
                        </div>
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="location-latitude" class="control-label col-md-2"> Loading Latitude </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="loading_latitude" id="location-latitude"
                                    value="{{ old('loading_latitude') }}" placeholder="Latitude">
                            </div>
                        </div>
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="location-longitude" class="control-label col-md-2"> Loading Longitude </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="loading_longitude" id="location-longitude"
                                    value="{{ old('loading_longitude') }}" placeholder="Longitude">
                            </div>
                        </div>
    
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="tonnage_target" class="control-label col-md-2"> Tonnage Target </label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="tonnage_target" id="tonnage_target"
                                    placeholder="Tonnage target" min="0" step="0.01"
                                    value="{{ old('tonnage_target') }}">
                            </div>
    
                        </div>
    
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="sales_target" class="control-label col-md-2"> Sales Target </label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="sales_target" id="sales_target"
                                    placeholder="Sales target" min="0" value="{{ old('sales_target') }}">
                            </div>
    
                        </div>
    
                        @php
                            $daysOfTheWeek = ['Sundays', 'Mondays', 'Tuesdays', 'Wednesdays', 'Thursdays', 'Fridays', 'Saturdays'];
                        @endphp
    
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="order_taking_days" class="control-label col-md-2"> Order Taking Days </label>
                            <div class="col-md-10">
                                <select name="order_taking_days[]" id="order_taking_days" class="form-control" multiple>
                                    @for ($count = 0; $count < count($daysOfTheWeek); $count++)
                                        <option value="{{ $count }}" @if (old('order_taking_days') == $count + 1) selected @endif>
                                            {{ $daysOfTheWeek[$count] }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
    
    
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="delivery_days" class="control-label col-md-2"> Delivery Days </label>
                            <div class="col-md-10">
                                <select name="delivery_days[]" id="delivery_days" class="form-control" multiple>
                                    @for ($count = 0; $count < count($daysOfTheWeek); $count++)
                                        <option value="{{ $count }}" @if (old('delivery_days') == $count + 1) selected @endif>
                                            {{ $daysOfTheWeek[$count] }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
    
                        </div>
    
    
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="salesman_proximity" class="control-label col-md-2"> Salesman Proximity (Meters)
                            </label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="salesman_proximity" id="salesman_proximity"
                                    placeholder="100" min="0" step="0.01"
                                    value="{{ old('salesman_proximity') ?? 5 }}">
                                <div class="form-text"> The maximum distance that a salesman should be from a shop to take an
                                    order.</div>
                            </div>
    
                        </div>
    
    
                        <div class="form-group" v-if="routeIsPhysical">
                            <label for="route_manager_proximity" class="control-label col-md-2"> Route Manager Proximity
                                (Meters) </label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="route_manager_proximity"
                                    id="route_manager_proximity" placeholder="100" min="0" step="0.01"
                                    value="{{ old('route_manager_proximity') ?? 5 }}">
                                <div class="form-text"> The maximum distance that a route manager should be from a shop to
                                    verify it.</div>
                            </div>
    
                        </div>
                    </div>

                    <div class="box-footer" style="margin-top:25px;float: right;">
                        <button type="submit" class="btn btn-primary" >Submit </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script> 

    <script type="text/javascript">
       
        $(document).ready(function() {
            var max_fields = 5000;
            var addButton = $('.btn_delivery_center');
            var wrapper = $('.delivery_route_centers');
            var centersForm =
                ' <div class="col-12 all_centers_array"> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-6"> <div class="form-groups"> <label for="name" class="control-label"> Center Name </label> <input type="text" class="form-control" name="center_name[]" id="name"  placeholder="Delivery center name"  required> </div> </div> <div class="col-lg-3 col-md-3 col-xs-12 col-sm-6"> <div class="form-groups"> <label for="center_location_name" class="control-label"> Center Location </label>  <input type="text" name="center_location_name[]" id="center_location_name"  class="form-control google_location" required>  </div>  </div> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-4">  <div class="form-groups"> <label for="center-latitude" class="control-label"> Latitude </label>  <input type="text" class="form-control latitude" name="center_latitude[]"  id="center-latitude"   placeholder="Latitude" required> </div> </div> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-4"> <div class="form-groups"> <label for="center-longitude" class="control-label "> Longitude </label> <input type="text" class="form-control longitude" name="center_longitude[]"  id="center-longitude"  placeholder="Longitude" required> </div> </div> <div class="col-lg-2 col-md-2 col-xs-12 col-sm-4"> <div class="form-groups"> <label for="center-longitude" class="control-label "> Preferred Center Radius </label>  <input type="number" class="form-control" name="preferred_center_radius[]"  id="preferred_center_radius" placeholder="100" min="0" step="0.01" > </div> </div>  <div class="col-lg-1 col-md-1 col-xs-12 col-sm-4"> <div class="form-groups add_centers" style="margin-top:25px;"> <button class="btn btn-danger remove_btn_delivery_center" type="button">-</button> </div> </div></div>';

            var x = 1;
            $(addButton).click(function() {
                if (x < max_fields) {
                    x++;
                    let newForm = $(centersForm).clone();
                    wrapper.append(newForm);

                    let locationInput = newForm.find('.google_location');
                    let latitudeInput = newForm.find('.latitude');
                    let longitudeInput = newForm.find('.longitude');

                    initializeLocationSearch(locationInput[0], latitudeInput[0], longitudeInput[0]);
                } else {
                    alert('Maximum number of ' + max_fields + 'is reached');
                }
            });

            $(wrapper).on('click', '.remove_btn_delivery_center', function(e) {
                e.preventDefault();
                $(this).closest('.all_centers_array').remove();;
            });


        });

        function initializeLocationSearchs() {
            let centerLocationInputs = document.getElementById('center_location_names');
            let optionss = {};

            let autocompletes = new google.maps.places.Autocomplete(centerLocationInputs, optionss);
            google.maps.event.addListener(autocompletes, 'place_changed', function() {
                let place = autocompletes.getPlace();
                let lats = place.geometry.location.lat();
                let lngs = place.geometry.location.lng();
                $("#center-latitudes").val(lats);
                $("#center-longitudes").val(lngs);


            });

        }

        function initializeLocationSearch(locationInput, latitudeInput, longitudeInput) {
            let options = {};

            let autocomplete = new google.maps.places.Autocomplete(locationInput, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                let place = autocomplete.getPlace();
                let lat = place.geometry.location.lat();
                let lng = place.geometry.location.lng();
                latitudeInput.value = lat;
                longitudeInput.value = lng;
            });
        }

        function initializeRouteLocationSearch() {
            let StartingLocationInput = document.getElementById('starting_location_name');
            let locationoptions = {};

            let autocomplete = new google.maps.places.Autocomplete(StartingLocationInput, locationoptions);

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                let location_place = autocomplete.getPlace();
                let location_lat = location_place.geometry.location.lat();
                let location_lng = location_place.geometry.location.lng();
                $("#location-latitude").val(location_lat);
                $("#location-longitude").val(location_lng);
            });
        }



        google.maps.event.addDomListener(window, 'load', initializeLocationSearch);
        google.maps.event.addDomListener(window, 'load', initializeLocationSearchs);
        google.maps.event.addDomListener(window, 'load', initializeRouteLocationSearch);
    </script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'
        const app = createApp({
            data() {
                return {
                    routeIsPhysical: true,
                }
            },

            watch: {
                routeIsPhysical(newVal, oldVal) {
                    if (newVal) {
                        this.initSelect2()
                    }
                }
            },

            mounted() {
                this.initSelect2()
            },

            methods: {
                initSelect2() {
                    setTimeout(() => {
                        $("#restaurant_id").select2();
                        $("#order_taking_days").select2();
                        $("#delivery_days").select2();
                        $("#select_route_center").select2();
                    }, 1000)
                },
            },
        })

        app.mount('#add-route-page')
    </script>
@endsection
