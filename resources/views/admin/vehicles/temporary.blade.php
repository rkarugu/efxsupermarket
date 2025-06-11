@section('uniquepagescript')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.2/dist/echo.iife.js"></script>
    <script>
        const PUSHER_APP_KEY = '{{ env("PUSHER_APP_KEY") }}';
        const PUSHER_APP_CLUSTER = '{{ env("PUSHER_APP_CLUSTER") }}';
        const pusher = new Pusher(PUSHER_APP_KEY, {
            cluster: PUSHER_APP_CLUSTER,
            encrypted: true
        });
        const echo = new Echo({
            broadcaster: 'pusher',
            key: PUSHER_APP_KEY,
            cluster: PUSHER_APP_CLUSTER,
            encrypted: true
        });
        
    </script>

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
                url: `/admin/vehicle-details/info-window-detials/${deviceName}`,
                method: 'GET',
                success: function(response){
                    const infoWindow = new google.maps.InfoWindow({
                    content: response
                });

                    if(currentInfoWindow !== null){
                        currentInfoWindow.close();
                    }

                    currentInfoWindow = infoWindow;
                    infoWindow.open({
                    anchor: marker,
                    map: map,
                    shouldFocus: false,
                    });
                },
                error: function(xhr){
                    console.log(xhr);
                }
            });
        }

        function initialize() {
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 6,
                center: {lat: 6.5244, lng: 3.3792}
            });

            const statusFilter = document.getElementById('status-filter');
            statusFilter.addEventListener('change', () => {
                const selectedStatus = statusFilter.value;
                markers.forEach(marker => {
                    if (selectedStatus === 'all' || marker.status === selectedStatus) {
                        marker.setMap(map);
                        marker.popup.open(map); 
                    } else {
                        marker.setMap(null);
                        marker.popup.close(); 
                    }
                });
            });

            fetchLocations(); // Initial fetch
            
            echo.channel('locations')
                .listen('VehicleLocationUpdated', (e) => {
                    const location = e.location;
                    console.log(location);
                    const existingMarker = markers.find(marker => marker.vehicle_name === location.device);

                    if (existingMarker) {
                        existingMarker.setPosition({
                            lat: location.latitude,
                            lng: location.longitude
                        });
                        existingMarker.popup.position = new google.maps.LatLng(location.latitude, location.longitude);
                        existingMarker.popup.draw();
                    } else {
                        let vehicle_name = location.device;
                        let url = baseUrl.replace(':deviceName', vehicle_name);
                        const icon = document.createElement("div");
                        icon.className = "price-tag";
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
                        const rotation = location.direction
                        icon.style.transform = `rotate(${rotation}deg)`;

                        const content = document.createElement("div");
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
                        marker.setMap(map);
                        popup.open(map);
                    }
                });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    });
    
</script>
@endsection

<script async src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap"></script>
@endsection
