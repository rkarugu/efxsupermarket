@extends('layouts.admin.admin')

@section('content')

    <section class="content" >
        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="modal-title" id="confirmationModalLabel">Confirm Action</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <h4>Are you sure you want to <span id="modalAction"></span> <span id="deviceNameInModal"></span>?</h4>
                        <form action="" id="switch-off-form">
                            {{-- <div>
                                <label for="time">Switch off Period In Seconds</label>
                                <input type="number" name="time" id="time" class="form-control">
                            </div> --}}
                            <div>
                                <label for="speed">Switch Off Speed In Kmph - max 8 kmph</label>
                                <input type="number" name="speed" id="speed" class="form-control" max="8">
                            </div>
                            <div>
                                <label for="reason">Switch Off Reason</label>
                                <textarea name="reason" id="reason" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                         

                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmActionButton">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
                <div id="filter-control">
                        <select name="status-filter" id="status-filter" class=" form-control mlselect">
                            <option value="all" >All</option>
                            <option value="moving">Moving</option>
                            <option value="overspeeding">Overspeeding</option>
                            <option value="idling">Idling</option>
                            <option value="stationery">Stationery</option>
                            <option value="offline">Offline</option>
                        </select>
                </div>
                <div id="search-control">
                        <select name="search-vehicle" id="search-vehicle" class=" form-control mlselect">
                            <option value="">Search Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->device_number }}">{{ $vehicle->device_number }}</option>
                            @endforeach
                        </select>                  
                    {{-- <input type="text" id="search-input" placeholder="Search for vehicle"> --}}
                    {{-- <button id="search-button" class="btn btn-success">Search</button> --}}
                </div>

                <div class="row" style="margin: 0; padding: 0;">
                    <div class="col-md-7" id="map-view">
                                       
                    </div>
                </div>
                <div id="custom-control" class="box-primary box">
                    <h4>KEY</h4>
                    <p>
                        <span><i class="fas fa-circle moving" ></i></span> Moving - <span name="moving-vehicles-count" id="moving-vehicles-count"></span>
                    </p>
                    <p>
                        <span><i class="fas fa-circle overspeeding" ></i></span> Overspeeding - <span name="overspeeding-vehicles-count" id="overspeeding-vehicles-count"></span>
                    </p>
                    <p>
                        <span><i class="fas fa-circle idling" ></i></span> Idling - <span name="idling-vehicles-count" id="idling-vehicles-count"></span>
                    </p>
                    <p>
                        <span><i class="fas fa-circle stationery" ></i></span> Stationery - <span name="stationery-vehicles-count" id="stationery-vehicles-count"></span>
                    </p>
                    <p>
                        <span><i class="fas fa-circle offline" ></i></span> Offline -  <span name="offline-vehicles-count" id="offline-vehicles-count"></span>
                    </p>
                    
                </div>      
        </div>
    </section>
@endsection

@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        #map-view, #details-view {
            position: relative;
            /* height: 800px; */
            height: 80vh;
            width: 100%;
        }
        #custom-control {
            position: absolute;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 200;
            right: 55px;
            bottom: 0;
            width: 200px;
            min-height: 150px;
            background-color: white;
           
           
        }
        .vehicle-info-window{
            padding: 0px;
            margin: 1px;
            z-index: 10 !important;
            width: auto !important;


        }
        #filter-control {
            position: absolute;
            top: 10px;
            right: 300px;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 201;
            width: 230px;
        }
        #search-control {
            position: absolute;
            top: 10px;
            right: 60px;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 201;
            width: 230px;
        }

        #search-input {
            margin-right: 5px;
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }
        .moving{
            margin-right: 2px;
            color: green;
        }
        .overspeeding{
            margin-right: 2px;
            color: red;
        }
        .idling{
            margin-right: 2px;
            color: olive;
        }
        .stationery{
            margin-right: 2px;
            color: brown;
        }
        .offline{
            margin-right: 2px;
            color: black;
        }

        #details-view {
            overflow-y: auto;
            padding: 15px;
        }

        #vehicle-name {
            margin: 0;
            font-weight: 700;
            font-size: 22px;
        }

        #vehicle-address {
            font-size: 15px;
            font-weight: 500;
        }

        .major-detail {
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: 15px;
            padding: 10px 15px;
            height: 80px;
            min-width: 200px;
        }

        .major-detail-icon {
            font-size: 20px;
        }

        .major-detail-title {
            font-size: 18px;
            font-weight: 500;
            margin-left: 12px;
            margin-top: -5px;
        }

        .major-detail-value {
            font-size: 20px;
            font-weight: 600;
        }

        .price-tag {
            border-radius: 50%;
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
      

        .price-tag i {
            color: #fff;
        }
        .siteNotice {
            font-size: 4px;
        }

        .truck-icon {
            font-size: 20px;
            /* color: black !important; */
        }
        .device-name{
            max-height: 10px;
            padding: 1px !important;
            margin: 1px !important;
            cursor:  pointer !important;
        }
        .device-name{
            font-weight: bold !important;
            color: white !important;
            text-decoration: none !important;

        }
        .device-name a{
            text-decoration: none !important;
        }
       
        .green-vehicle i {
            color: green !important;
        }
        .black-vehicle i {
            color: black !important;
        }
        .red-vehicle i {
            color: red !important;
        }
        .olive-vehicle i{
            color: olive !important;
        }
        .brown-vehicle i{
            color: brown !important;
        }
        .popup-bubble {
        position: absolute;
        top: 0;
        left: 0;
        transform: translate(-50%, -100%);
        background-color: white;
        padding: 3px 3px 5px 3px;
        white-space: nowrap;
        border-radius: 5px;
        font-family: sans-serif;
        /* overflow-y: auto; */
        max-height: 60px;
        max-width: 250px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.5);
        z-index: 2 !important;

        }
        .popup-bubble-anchor {
        position: relative;
        width: auto !important;
        }
        .gm-style-iw button{
            /* display: none !important; */
            height: 1px !important;
            padding-bottom: 0px !important;
            margin-bottom: 0px !important;
        }
        .parent-z-index, .gmnoprint, .gm-control-active{ 
            z-index: 200 !important;
        }
        .gm-ui-hover-effect{
            z-index: 205 !important;
        }
      
         .gm-style-mtc, { 
            z-index: 5 !important;
        }
       
       
    </style>
@endsection

@section('uniquepagescript')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>




    <script>
        (g => {
            let h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window;
            b = b[c] || (b[c] = {});
            let d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("callback", c + ".maps." + q);
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                d[q] = f;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a)
            }));
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })({
            key: "{{ $googleMapsApiKey }}",
            v: "weekly",
        });
    </script>
     <script
     src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&v=weekly"
     defer
   ></script>
  
    <script type="text/javascript">
   
    $(function(){
        $(".mlselect").select2();

        $('body').addClass('sidebar-collapse');
        var baseUrl = "{{ route('live-vehicle-movement', ':deviceName') }}";

        let map;
        let markers = [];
        let popups = [];
        let currentInfoWindow = null; 
        class Popup extends google.maps.OverlayView {
            constructor(position, content, backgroundColor) {
                super();
                this.position = position;
                this.containerDiv = document.createElement("div");
                this.containerDiv.classList.add("popup-bubble");
                this.containerDiv.style.backgroundColor = backgroundColor; 

                const bubbleAnchor = document.createElement("div");
                bubbleAnchor.classList.add("popup-bubble-anchor");
                bubbleAnchor.appendChild(content);

                this.containerDiv.appendChild(bubbleAnchor);

                Popup.preventMapHitsAndGesturesFrom(this.containerDiv);
            }

            onAdd() {
                this.getPanes().floatPane.appendChild(this.containerDiv);
            }

            onRemove() {
                if (this.containerDiv.parentElement) {
                    this.containerDiv.parentElement.removeChild(this.containerDiv);
                }
            }

            draw() {
                const divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
                const display = Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ? "block" : "none";

                if (display === "block") {
                    this.containerDiv.style.left = (divPosition.x)+ "px";
                    // this.containerDiv.style.left = (divPosition.x + (this.containerDiv.offsetWidth / 2 )) + "px"; 
                    this.containerDiv.style.top = (divPosition.y - this.containerDiv.offsetHeight - 15) + "px";
                }

                if (this.containerDiv.style.display !== display) {
                    this.containerDiv.style.display = display;
                }
            }

            open(map) {
                this.setMap(map);
            }

            close() {
                this.setMap(null);
            }
        }

        function fetchLocations() {
            return $.ajax({
                url: '/admin/get-vehicle-locations',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    console.log(response);

                    const movingVehiclesCount = response.moving_vehicle_count;
                    const  stationeryVehiclesCount = response.stationery_vehicle_count;
                    const overspeedingVehiclesCount = response.overspeeding_vehicle_count;
                    const idlingVehiclesCount = response.idling_vehicle_count;
                    const offlineVehicleCount = response.offline_vehicle_count;
                    $('#moving-vehicles-count').text(movingVehiclesCount);
                    $('#overspeeding-vehicles-count').text(overspeedingVehiclesCount);
                    $('#idling-vehicles-count').text(idlingVehiclesCount);
                    $('#stationery-vehicles-count').text(stationeryVehiclesCount);
                    $('#offline-vehicles-count').text(offlineVehicleCount);

                    const filteredStatus = $('#status-filter').val().trim();

                    markers.forEach(marker => marker.setMap(null));
                    markers = [];
                    popups.forEach(popup => popup.close());
                    popups = [];
                    response.results.forEach(location => {
                            let vehicle_name = location.device;
                            let url = baseUrl.replace(':deviceName', vehicle_name);
                            const icon = document.createElement("div");
                            icon.className = "price-tag";
                            // icon.innerHTML = `<i class="fas fa-location-arrow truck-icon"></i>`;
                            // icon.innerHTML = `<img src="{{ asset('assets/admin/images/car-removebg-preview.png') }}" height="35px" width="30px" />`;
                            icon.innerHTML = `<img src="{{ asset('assets/admin/images/lorry.png') }}" height="37px" width="20px" />`;


                            let backgroundColor, status;
                            if(location.is_offline){
                                icon.classList.add("black-vehicle");
                                backgroundColor = "black";
                                status = "offline";

                            }else if(location.movement && location.speed > 8 && location.speed <= 65){
                                icon.classList.add("green-vehicle");
                                backgroundColor = "green";
                                status = "moving";
  
                            }else if(location.movement && location.speed > 65){
                                icon.classList.add("red-vehicle");  
                                backgroundColor = "red";
                                status = "overspeeding";
    

                            }else if( location.speed <= 8 && location.ignition_status === 'ON' ){
                                icon.classList.add("olive-vehicle");  
                                backgroundColor = "olive";
                                status = "idling";
 
                            }else if (location.ignition_status === 'OFF'){
                                icon.classList.add("brown-vehicle");  
                                backgroundColor = "brown";
                                status = "stationery";
 
                            }
                            // const rotation = location.direction - 90;
                            const rotation = location.direction
                            icon.style.transform = `rotate(${rotation}deg)`;

                            const content = document.createElement("div");
                            // content.innerHTML = `<a href="${url}"><div class="device-name">${location.device}</div></a>`;
                            content.innerHTML = `<div class="device-name">${location.device}</div>`;
                                const popup = new Popup(
                                new google.maps.LatLng(location.latitude, location.longitude),
                                content,
                                backgroundColor
                            );
                            

                            const marker = new google.maps.marker.AdvancedMarkerElement({
                            map: map,
                            position: {
                                lat: location.latitude,
                                lng: location.longitude,
                            },
                            title: location.device,
                            content: icon,
                        });
                            marker.addListener("click", ()=>{
                                fetchVehicleInfoWindowDetails(location.device, marker);
                            });

                            popup.containerDiv.addEventListener("click", () => {
                                fetchVehicleInfoWindowDetails(location.device, marker);
                            });

                            marker.vehicle_name = location.device;
                            marker.status = status; 
                            marker.popup = popup;
                            markers.push(marker);
                            // popup.open(map);
                            if (filteredStatus === 'all' || filteredStatus === status) {
                            marker.setMap(map);
                            popup.open(map);
                            } else {
                                marker.setMap(null);
                                popup.close();
                            }
                            popups.push(popup);
                          
                    });
                }
            });
        }
        function fetchVehicleInfoWindowDetails(deviceName, marker){
            return $.ajax({
                url: `/admin/vehicle-details/info-window-detals/${deviceName}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    let url = baseUrl.replace(':deviceName', response.device_name);
                    let switchButtonHtml = '';
                    let viewHistoryButtonHtml = '';
                    let canswitchIgnitionStatus = {!! json_encode($canSwitchVehicleOff) !!};
                    let canViewHistory = {!! json_encode($canViewHistory) !!};
                    if(canswitchIgnitionStatus){
                        if(response.switch_off_status === 'off') {
                            switchButtonHtml = `<a href="#" class="btn btn-sm switch-button" data-action="switchOn" data-url="${url}" data-device-name="${response.device_name}" style="margin-top:10px; color:white; background-color:green; border-color:green;">Switch On</a>`;
                        } else {
                            switchButtonHtml = `<a href="#" class="btn btn-primary btn-sm switch-button" data-action="switchOff" data-url="${url}" data-device-name="${response.device_name}" style="margin-top:10px;">Switch Off</a>`;
                        }

                    } 
                    if(canViewHistory){
                        viewHistoryButtonHtml = `<a href="${url}" target="_blank" class="btn btn-info btn-sm" style="margin-top:10px;">View History</a>`;
                    }  
                    const infoWindowContent = document.createElement("div");
                            infoWindowContent.classList.add("vehicle-info-window");
                            infoWindowContent.innerHTML = `<div>

                                <h4>${response.device_name}</h4>
                                <hr style="margin:0px; padding:0px;">
                                <h5>${response.model}</h5>
                                <p>
                                    <span><i class="fas fa-user driver" title="Driver"  style="color: #4e86b1; padding-right:2px;"></i> ${response.driver} </span>
                                </p>
                                <p>
                                    <span><i class="fas fa-map-marker location" title="Location" style="color: #4e86b1; padding-right:2px;"></i></span> ${response.location} 
                                </p>
                                <p>
                                    <span><i class="far fa-calendar-alt shift" title="Shift" style="color: #4e86b1; padding-right:2px;"></i></span> ${response.route} : ${response.schedule} 
                                </p>
                                 <p>
                                    <span><i class="fas fa-tachometer-alt purpose" title="Primary Purpose" style="color: #4e86b1; padding-right:2px;"></i></span>  ${response.mileage} kms | ${response.fuel_level} lts | ${response.speed} kms/hr  
                                </p>
                                <hr style="margin:0px; padding:0px;">
                                <div style="display: flex; justify-content: center; gap: 10px;">
                                    ${viewHistoryButtonHtml}
                                    ${switchButtonHtml}
                                </div>

                                </div>`;

                            const infowindow = new google.maps.InfoWindow({
                                content: infoWindowContent,
                                ariaLabel: "",

                            });
                            if (currentInfoWindow) {
                                currentInfoWindow.close(); 
                            }
                            infowindow.open({
                                    anchor: marker,
                                    map,
                                });
                                currentInfoWindow = infowindow;
                                infoWindowContent.querySelectorAll('.switch-button').forEach(button => {
                                button.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const action = this.getAttribute('data-action');
                                    const url = this.getAttribute('data-url');
                                    const deviceName = this.getAttribute('data-device-name');
                                    $('#deviceNameInModal').text(deviceName);
                                    $('#modalAction').text(action);
                                    if(action == 'switchOn'){
                                        $('#switch-off-form').hide();
                                    }
                                    $('#confirmationModal').modal('show');

                                    $('#confirmActionButton').off('click').on('click', function() {
                                        if(action === 'switchOff') {
                                            // $('#time').attr('required', true);
                                            $('#speed').attr('required', true);
                                            $('#reason').attr('required', true);
                                        } else {
                                            // $('#time').removeAttr('required');
                                            $('#speed').removeAttr('required');
                                            $('#reason').removeAttr('required');
                                        }
                                        if(action === 'switchOn' || (action === 'switchOff' && $('#switch-off-form')[0].checkValidity())){
                                            $.ajax({
                                            url: `/admin/vehicles/toggle-ignition`,
                                            type: 'POST',
                                            dataType: 'json',
                                            data : {
                                                'action': action,
                                                'deviceName': deviceName,
                                                // 'time': $('#time').val(),
                                                'speed': $('#speed').val(),
                                                'reason':$('#reason').val(),
                                                '_token':'{{csrf_token()}}',
                                            },
                                            success: function (response) {
                                                toaster = new Form();
                                                console.log(response);
                                                toaster.successMessage('Vehicle' + deviceName + action + ' successfull');
                                                
                                            },
                                            error: function (error) {
                                                toaster = new Form();
                                                toaster.errorMessage(error);
                                            }
                                        });
                                        $('#confirmationModal').modal('hide');

                                        }else{
                                            $('#switch-off-form')[0].reportValidity();

                                        }
                                       
                                    });
                                });
                            });
                                google.maps.event.addListener(infowindow, 'domready', function() {
                                $('div').each(function() {
                                    if ($(this).find('.vehicle-info-window').length > 0) {
                                        $(this).addClass('parent-z-index');
                                    }
                                });
                            });
                }
            });

        }
        async function initMap() {
            const position = { lat:  -1.034444, lng: 37.076805 };
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

            map = new Map(document.getElementById("map-view"), {
                zoom: 9,
                center: position,
                // mapId: "DEMO_MAP_ID",
                mapId: "7c9bd9e078617725",
                gestureHandling: "greedy",

            });
            const markers = new AdvancedMarkerElement({
                map: map,
                position: position,
                title: "HQ",
            });
            fetchLocations();
            setInterval(fetchLocations, 120000);
            }

            initMap();

            function searchMarker(query) {
                console.log(markers)
                const marker = markers.find(m => m.vehicle_name === query);
                if (marker) {
                    map.setCenter(marker.position);
                    map.setZoom(12);
                    google.maps.event.trigger(marker, 'click');
                } else {
                    alert('Vehicle not found!');
                }
            }
            function filterMarkers(status) {
            markers.forEach(marker => {
                if (status === 'all' || marker.status === status) {
                    marker.setMap(map);
                    marker.popup.open(map);

                } else {
                    marker.setMap(null);
                    marker.popup.close();

                }
            });
        }
        $('#status-filter').on('change', function() {
            const selectedStatus = $(this).val();
            filterMarkers(selectedStatus);
        });


            $('#search-vehicle').on('change', function() {
                const query = $('#search-vehicle').val().trim();
                if (query) {
                    searchMarker(query);
                }
            });

            $('#search-input').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#search-button').click();
                }
            });
    
    });

    </script>
@endsection