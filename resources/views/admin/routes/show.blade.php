@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $route_plan_data['route_name'] }} -Route Sections </h3>

                    <a href="{{ route("$base_route.index") }}" class="btn btn-outline-primary"> << Back to Route List </a>
                </div>
            </div>

            <div class="box-body">
{{--                <div id="map" style="width: 100%; height: 600px;"></div>--}}

                <div id="route-plan">
{{--                    <div class="box-header with-border">--}}
{{--                        <h3 class="box-title" style="font-weight: 700;"> Route Details </h3>--}}
{{--                    </div>--}}

                    @if(!$route_plan_data['starting_location_name'])
                        <div class="box-body">
                            <p> Route sections not available. Please <a href="{{ route("$base_route.edit", $route_plan_data['route_id']) }}">edit the route</a>
                                to add a starting point. </p>
                        </div>

                    @elseif(count($route_plan_data['sections']) == 0)
                        <div class="box-body">
                            <p> Route sections not available. Please add centers and route customers to this route to show sections. </p>
                        </div>
                    @else
                        <div class="box-body">
                            <div style="margin-bottom: 10px;">
                                @include('message')
                            </div>

                            <form action="{{ route("$base_route.sections.update", $route_plan_data['route_id']) }}"
                                  method="post">

                                {{ csrf_field() }}

                                @foreach($route_plan_data['sections'] as  $index=>$section)
                                    <input type="hidden" name="start_shop_id-{{ $section['id'] }}"
                                           value="{{ $section->start_shop_id }}">

                                    <input type="hidden" name="end_shop_id-{{ $section['id'] }}"
                                           value="{{ $section->end_shop_id }}">

                                    {{-- <input type="hidden" name="start_point_is_plan_start_point-{{ $section['id'] }}"
                                           value="{{ $section['starting_point']['start_point_is_plan_start_point'] }}"> --}}
                                    @php

                            $startshop = App\Model\WaRouteCustomer::where('id',$section->start_shop_id)->first();
                            $endshop = App\Model\WaRouteCustomer::where('id', $section->end_shop_id)->first();
                            
                                    @endphp
                                   <div class="box-header with-border">
                                        <h3 class="box-title"> 
                                           
                                            Section {{ ++ $index  }} - {{ $index == 1 ? $route_plan_data['starting_location_name'] : $startshop->bussiness_name ?? '' }} to {{ $endshop->bussiness_name }}
                                        </h3>
                                    </div>

                                    <div class="box-body">
                                        <h5><strong>Normal Weather Conditions</strong></h5>
                                        <div class="row">
                                            <div class="col-md-2 form-group">
                                                <label for="distance_estimate" class="control-label"> Dist. Estimate (kms) </label>
                                                <input type="text" class="form-control" name="distance_estimate-{{ $section['id'] }}"
                                                       id="distance_estimate" value="{{ number_format($section['distance_estimate'] / 1000, 2) }}">
                                            </div>

                                            <div class="col-md-2 form-group">
                                                <label for="time_estimate" class="control-label"> Time Estimate (mins) </label>
                                                <input type="text" class="form-control" name="time_estimate-{{ $section['id'] }}"
                                                       id="time_estimate" value="{{ ceil($section['time_estimate'] / 60) }}">
                                            </div>

                                            <div class="col-md-2 form-group">
                                                <label for="fuel_estimate" class="control-label"> Fuel Estimate (litres) </label>
                                                <input type="text" class="form-control" name="fuel_estimate-{{ $section['id'] }}"
                                                       id="fuel_estimate" value="{{ $section['fuel_estimate'] }}">
                                            </div>

                                            <div class="col-md-2 form-group">
                                                <label for="road_type" class="control-label"> Road Type </label>
                                                <input type="text" class="form-control" name="road_type-{{ $section['id'] }}"
                                                       id="road_type" value="{{ $section['road_type'] }}">
                                            </div>

                                            <div class="col-md-2 form-group">
                                                <label for="road_condition" class="control-label"> Road Condition </label>
                                                <input type="text" class="form-control" name="road_condition-{{ $section['id'] }}"
                                                       id="road_condition" value="{{ $section['road_condition'] }}">
                                            </div>
                                        </div>
                                        <br>
                                        <fieldset>
                                            <legend><h5><strong>Rainy Weather Conditions</strong></h5></legend>
                                            <div class="row">
                                                <div class="col-md-2 form-group">
                                                    <label for="rainy_distance_estimate" class="control-label"> Dist. Estimate (kms) </label>
                                                    <input type="text" class="form-control" name="rainy_distance_estimate-{{ $section['id'] }}"
                                                           id="rainy_distance_estimate" value="{{ number_format($section['rainy_distance_estimate'] / 1000, 2) }}">
                                                </div>
    
                                                <div class="col-md-2 form-group">
                                                    <label for="rainy_time_estimate" class="control-label"> Time Estimate (mins) </label>
                                                    <input type="text" class="form-control" name="rainy_time_estimate-{{ $section['id'] }}"
                                                           id="rainy_time_estimate" value="{{ ceil($section['rainy_time_estimate'] / 60) }}">
                                                </div>
    
                                                <div class="col-md-2 form-group">
                                                    <label for="rainy_fuel_estimate" class="control-label"> Fuel Estimate (litres) </label>
                                                    <input type="text" class="form-control" name="rainy_fuel_estimate-{{ $section['id'] }}"
                                                           id="rainy_fuel_estimate" value="{{ $section['rainy_fuel_estimate'] }}">
                                                </div>
    
                                                <div class="col-md-2 form-group">
                                                    <label for="rainy_road_type" class="control-label"> Road Type </label>
                                                    <input type="text" class="form-control" name="rainy_road_type-{{ $section['id'] }}"
                                                           id="rainy_road_type" value="{{ $section['rainy_road_type'] }}">
                                                </div>
    
                                                <div class="col-md-2 form-group">
                                                    <label for="rainy_road_condition" class="control-label"> Road Condition </label>
                                                    <input type="text" class="form-control" name="rainy_road_condition-{{ $section['id'] }}"
                                                           id="rainy_road_condition" value="{{ $section['rainy_road_condition'] }}">
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                       
                                    </div>
                                   
                                     
                                @endforeach

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"> Save Changes</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script async src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap"></script>
    {{--    <script async src="https://maps.googleapis.com/maps/api/js?libraries=maps,routes,marker&key={{ $googleMapsApiKey }}&callback=initMap" defer></script>--}}

    <script type="text/javascript">
        let map;

        async function initMap() {
            const {Map, InfoWindow} = await google.maps.importLibrary("maps");
            const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary("marker");
            const {LatLng} = await google.maps.importLibrary("core");

            // Set map center to route starting point
            let mapCenter = {lat: -1.287006, lng: 36.767287}
            let routeStartLat = JSON.parse('{!! $route_plan_data['starting_lat'] !!}');
            let routeStartLng = JSON.parse('{!! $route_plan_data['starting_lng'] !!}');
            let routeStartName = '{!! $route_plan_data['starting_location_name'] !!}';
            if (routeStartLat && routeStartLng) {
                mapCenter = {lat: routeStartLat, lng: routeStartLng}
            }

            // Init Map
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: mapCenter,
                mapId: "8a023462a9950e01",
            });

            // Draw delivery centers
            drawDeliveryCenters(map, PinElement, AdvancedMarkerElement, InfoWindow)

            // Add marker to starting point, only when it's set
            if (routeStartName) {
                const icon = document.createElement("div");
                icon.innerHTML = '<i class="fa fa-home fa-2x"></i>';
                const pinScaled = new PinElement({
                    scale: 2.0,
                    background: "#FBBC04",
                    borderColor: "#137333",
                    glyph: icon,
                });

                const marker = new AdvancedMarkerElement({
                    map,
                    position: new google.maps.LatLng(mapCenter.lat, mapCenter.lng),
                    content: pinScaled.element,
                    title: routeStartName,
                });

                const infoWindow = new InfoWindow();
                marker.addListener("click", ({domEvent, latLng}) => {
                    const {target} = domEvent;
                    infoWindow.close();
                    infoWindow.setContent(marker.title);
                    infoWindow.open(marker.map, marker);
                });

                // Route sections
                let routeEndLat = JSON.parse('{!! $route_plan_data['end_lat'] !!}');
                let routeEndLng = JSON.parse('{!! $route_plan_data['end_lng'] !!}');
                drawRouteSections(map, routeStartLat, routeStartLng, routeEndLat, routeEndLng)
            } else {
                alert("The starting point for this route is not set.\n To display the route plan correctly, please edit this route and set the starting point.");
            }
        }

        function drawRouteSections(map, startLat, startLng, endLat, endLng) {
            let sections = JSON.parse('{!! json_encode($route_plan_data['sections']) !!}')
            let directionsService = new google.maps.DirectionsService();
            let directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(map);

            let wayPoints = [];
            sections.forEach(section => {
                wayPoints.push({
                    location: new google.maps.LatLng(section.end_lat, section.end_lng),
                    stopover: true,
                })
            })

            directionsService.route({
                origin: new google.maps.LatLng(startLat, startLng),
                destination: new google.maps.LatLng(endLat, endLng),
                avoidTolls: false,
                avoidHighways: false,
                travelMode: google.maps.TravelMode.DRIVING,
                waypoints: wayPoints,
                optimizeWaypoints: true,
            }, function (response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                } else {
                    // window.alert('Directions request failed due to ' + status);
                }
            });
        }

        function drawDeliveryCenters(map, PinElement, AdvancedMarkerElement, InfoWindow) {
            let centers = JSON.parse('{!! $route_plan_data['route_centers'] !!}')
            centers.forEach((center) => {
                new google.maps.Circle({
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                    map,
                    center: {lat: center.lat, lng: center.lng},
                    radius: center.preferred_radius,
                });

                center.shops.forEach(shop => {
                    const icon = document.createElement("div");
                    icon.innerHTML = '<i class="fa fa-user-md fa-lg"></i>';

                    const pinScaled = new PinElement({
                        background: "#000",
                        glyph: icon,
                        glyphColor: "#fff",
                    });

                    const marker = new AdvancedMarkerElement({
                        map,
                        position: new google.maps.LatLng(shop.lat, shop.lng),
                        content: pinScaled.element,
                        title: shop.name,
                    });

                    const infoWindow = new InfoWindow();
                    marker.addListener("click", ({domEvent, latLng}) => {
                        const {target} = domEvent;
                        infoWindow.close();
                        infoWindow.setContent(marker.title);
                        infoWindow.open(marker.map, marker);
                    });
                })
            })
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
            directionsService.route({
                origin: pointA,
                destination: pointB,
                avoidTolls: true,
                avoidHighways: false,
                travelMode: google.maps.TravelMode.DRIVING
            }, function (response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                } else {
                    // window.alert('Directions request failed due to ' + status);
                }
            });
        }

        function initLocationSearch() {
            google.maps.event.addDomListener(window, 'load', function () {
                let input = document.getElementById('starting_location');
                let options = {};

                let autocomplete = new google.maps.places.Autocomplete(input, options);
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    let place = autocomplete.getPlace();
                    let lat = place.geometry.location.lat();
                    let lng = place.geometry.location.lng();
                    $("#starting_lat").val(lat);
                    $("#starting_lng").val(lng);
                });
            });
        }

        window.initMap = initMap;
    </script>
@endsection

@section('uniquepagestyle')
    <style>
        #route-plan {
            margin-top: 15px;
        }
    </style>
@endsection