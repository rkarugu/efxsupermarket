@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">

                    <h3 class="box-title text-danger">Duplicate Shop Approvals</h3>
                    <div>
                        <a href="{{ route('duplicate-route-customers') }}" class="btn btn-outline-primary">Back</a>
                        <a href="{{ route('duplicate-route-customers.reject', $shopdetails->id) }}" class="btn btn-outline-primary"> Reject </a>
                        <a href="{{ route('duplicate-route-customers.approve', $shopdetails->id) }}"
                            class="btn btn-outline-primary"> Approve</a>

                    </div>




                </div>
            </div>
            <div class="box-body">
                <div id="map" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            Existing Route Customer Details
                        </div>
                    </div>
                    <div class="box-body">
                        <img src="{{ asset('uploads/shops/' . $duplicateShopDetails->image_url) }}" alt=""
                            style="height:20vh;width:12vw;">

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Shop Name</td>
                                        <td>{{ $duplicateShopDetails->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                    <tr>
                                        <td>Phone Number</td>
                                        <td>{{ $duplicateShopDetails->phone ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Business Name</td>
                                        <td>{{ $duplicateShopDetails->bussiness_name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Town</td>
                                        <td>{{ $duplicateShopDetails->town ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Latitude</td>
                                        <td>{{ $duplicateShopDetails->lat ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Longitude</td>
                                        <td>{{ $duplicateShopDetails->lng ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $duplicateShopDetails->status ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td>{{ $duplicateShopDetails->gender ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>KRA PIN</td>
                                        <td>{{ $duplicateShopDetails->kra_pin ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Date Created</td>
                                        <td>{{ $duplicateShopDetails->created_at != null ? $duplicateShopDetails->created_at->format('M, d Y, h:i A') : '' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            New Route Customer Details
                        </div>
                    </div>
                    <div class="box-body">
                        <img src="{{ asset('uploads/shops/' . $shopdetails->image_url) }}" alt=""
                            style="height:20vh;width:12vw;">

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Shop Name</td>
                                        <td>{{ $shopdetails->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                    <tr>
                                        <td>Phone Number</td>
                                        <td>{{ $shopdetails->phone ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Business Name</td>
                                        <td>{{ $shopdetails->bussiness_name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Town</td>
                                        <td>{{ $shopdetails->town ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Latitude</td>
                                        <td>{{ $shopdetails->lat ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Longitude</td>
                                        <td>{{ $shopdetails->lng ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $shopdetails->status ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td>{{ $shopdetails->gender ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>KRA PIN</td>
                                        <td>{{ $shopdetails->kra_pin ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Date Created</td>
                                        <td>{{ $shopdetails->created_at != null ? $shopdetails->created_at->format('M, d Y, h:i A') : '' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            Existing Shop Route Information
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Route Name</td>
                                        <td>{{ $duplicateShopDetails->route->route_name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Starts From</td>
                                        <td>{{ $duplicateShopDetails->route->starting_location_name ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Tonnage Target</td>
                                        <td>{{ $duplicateShopDetails->route->tonnage_target ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Sales Target</td>
                                        <td>{{ $duplicateShopDetails->route->sales_target ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Days</td>
                                        <td>Day(s) - {{ $duplicateShopDetails->route->delivery_days ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Taking Days </td>
                                        <td>Day(s) - {{ $duplicateShopDetails->route->order_taking_days ?? '' }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            New Shop Route Information
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Route Name</td>
                                        <td>{{ $shopdetails->route->route_name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Starts From</td>
                                        <td>{{ $shopdetails->route->starting_location_name ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Tonnage Target</td>
                                        <td>{{ $shopdetails->route->tonnage_target ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Sales Target</td>
                                        <td>{{ $shopdetails->route->sales_target ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Days</td>
                                        <td>Day(s) - {{ $shopdetails->route->delivery_days ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Taking Days </td>
                                        <td>Day(s) - {{ $shopdetails->route->order_taking_days ?? '' }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            Existing Shop Delivery Center Information
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Center Name</td>
                                        <td>{{ $duplicateShopDetails->center->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Center Location Name</td>
                                        <td>{{ $duplicateShopDetails->center->center_location_name ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Cordinates</td>
                                        <td>({{ $duplicateShopDetails->center->lat ?? '' }} ,
                                            {{ $shopdetails->center->lng ?? '' }})</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            New Shop Delivery Center Information
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Center Name</td>
                                        <td>{{ $shopdetails->center->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Center Location Name</td>
                                        <td>{{ $shopdetails->center->center_location_name ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Cordinates</td>
                                        <td>({{ $shopdetails->center->lat ?? '' }} ,
                                            {{ $shopdetails->center->lng ?? '' }})</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>


        </div>

    </section>
@endsection

@section('uniquepagescript')
    <script async src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap"></script>

    <script type="text/javascript">
        let map;

        async function initMap() {
            const {
                Map,
                InfoWindow,
                ExistingInfoWindow
            } = await google.maps.importLibrary("maps");
            const {
                AdvancedMarkerElement,
                PinElement
            } = await google.maps.importLibrary("marker");

            let shopLat = {{ $shopdetails->lat }};
            let shopLng = {{ $shopdetails->lng }};
            let shopName = "{{ $shopdetails->name }}";
            let existingShopLat = {{ $duplicateShopDetails->lat }};
            let existingShopLng = {{ $duplicateShopDetails->lng }};
            let existingShopName = "{{ $duplicateShopDetails->name }}";

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: {
                    lat: shopLat,
                    lng: shopLng
                },
                mapId: "8a023462a9950e01",
            });

            const icon = document.createElement("div");
            const existingIcon = document.createElement("div");
            icon.innerHTML = '<i class="fa fa-shopping-bag fa-2x"></i>';
            existingIcon.innerHTML = '<i class="fa fa-shopping-bag fa-2x"></i>';

            const pinScaled = new PinElement({
                scale: 2.0,
                background: "#FBBC04",
                borderColor: "#137333",
                glyph: icon,
            });
            const existingPinScaled = new PinElement({
                scale: 2.0,
                background: "#eb4034",
                borderColor: "#000000",
                glyph: existingIcon,
            });

            const marker = new AdvancedMarkerElement({
                map,
                position: new google.maps.LatLng(shopLat, shopLng),
                content: pinScaled.element,
                title: shopName,
            });
            const existingMarker = new AdvancedMarkerElement({
                map,
                position: new google.maps.LatLng(existingShopLat, existingShopLng),
                content: existingPinScaled.element,
                title: existingShopName,
            });

            const infoWindow = new InfoWindow();
            const existingInfoWindow = new InfoWindow();
            marker.addListener("click", ({
                domEvent,
                latLng
            }) => {
                const {
                    target
                } = domEvent;
                infoWindow.close();
                infoWindow.setContent(marker.title);
                infoWindow.open(marker.map, marker);
            });
            existingMarker.addListener("click", ({
                domEvent,
                latLng
            }) => {
                const {
                    target
                } = domEvent;
                infoWindow.close();
                infoWindow.setContent(existingMarker.title);
                infoWindow.open(existingMarker.map, existingMarker);
            });
        }
    </script>
@endsection

@section('uniquepagestyle')
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
@endsection
