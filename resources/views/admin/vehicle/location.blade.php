@extends('layouts.admin.admin')

@section('content')
    <style type="text/css">
        /* Style the tab */
        .tab {
            overflow: hidden;
            /*  border: 1px solid #ccc;
            */
            background-color: #c1ccd1;
        }

        /* Style the buttons inside the tab */
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 7px 16px;
            transition: 0.3s;
            font-size: 14px;
        }

        /* Change background color of buttons on hover */
        .tab button:hover {
            background-color: #ddd;
        }

        /* Create an active/current tablink class */
        .tab button.active {
            background-color: white;
            border: 1px solid gainsboro;
        }

        /* Style the tab content */
        .tabcontent {
            display: none;
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-top: none;
        }

        .ctable {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .ctable, td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header"></div>
            <div class="data-description">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom:2rem">
                            @include('admin/vehicle/add_tabs')

                            <div id="map" class="col-md-12" style="width: 100%; height: 700px;"></div>
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
            const {Map, InfoWindow} = await google.maps.importLibrary("maps");
            const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary("marker");
            const {LatLng} = await google.maps.importLibrary("core");

            let mapCenter = {lat: -1.287006, lng: 36.767287}
            let deviceLat = JSON.parse('{!! $device['lat'] !!}');
            let deviceLng = JSON.parse('{!! $device['lng'] !!}');
            let deviceName = '{!! $device['name'] !!}';

            if (deviceLat && deviceLng) {
                mapCenter = {lat: deviceLat, lng: deviceLng}
            }

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 14,
                center: mapCenter,
                mapId: "8a023462a9950e01",
            });

            const icon = document.createElement("div");
            icon.innerHTML = '<i class="fa fa-truck fa-2x"></i>';
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
                title: deviceName,
            });

            const infoWindow = new InfoWindow();
            marker.addListener("click", ({domEvent, latLng}) => {
                const {target} = domEvent;
                infoWindow.close();
                infoWindow.setContent(marker.title);
                infoWindow.open(marker.map, marker);
            });
        }

        window.initMap = initMap;
    </script>
@endsection
