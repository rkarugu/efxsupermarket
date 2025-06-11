@extends('layouts.admin.admin')

@section('content')
    <section class="content">


        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Actions </h3>
                    <div class="col-md-2" >
                        <label for="date">Start</label>
                        <input type="datetime-local" class="form-control" name="date" id="date" value="{{\Carbon\Carbon::now()->startOfDay()}}">
                    </div>
                 
                    <div class="col-md-2" >
                        <label for="date">End</label>
                        <input type="datetime-local" class="form-control" name="to_date" id="to_date" value="{{\Carbon\Carbon::now()->endOfDay()}}">
                    </div>
                  

                    <div class="col-md-4" id="controls">
                        <button id="download-report-btn" class="btn btn-success">Download Report</button>
                        <a href="" id="download-report" class=""></a>
                        <button id="play" class="btn btn-success">Play</button>
                        <button id="pause" class="btn btn-success">Pause</button>
                        <button id="resume" class="btn btn-success">Resume</button>
                        <a href="{{route('vehicle-overview-all')}}" class="btn btn-success">Back</a>
                    </div>
                </div>
            </div>


            <div class="row" style="margin: 0; padding: 0;">
                <div class="col-md-7" id="map-view">

                </div>
            </div>
            <div id="custom-control" class="box-primary box">
                <h4 name="vehicle-name" id="vehicle-name"></h4>
                <p>
                    <span><i class="fas fa-user driver"></i></span> Driver - <span name="driver" id="driver">{{$vehicleDetails->driver ?? ''}}</span>
                </p>

                <p>
                    <span><i class="fas fa-key ignition"></i></span> Ignition - <span name="ignition" id="ignition"></span>
                </p>
                <p>
                    <span><i class="fas fa-tachometer-alt speed"></i></span> Speed - <span name="speed"
                        id="speed"></span> Km/hr
                </p>
                <p>
                    <span><i class="fas fa-road mileage"></i></span> Mileage - <span name="mileage"
                        id="mileage"></span> Kms
                </p>
                <p>
                    <span><i class="fas fa-gas-pump fuel"></i></span> Fuel - <span name="fuel" id="fuel"></span> lts
                </p>
                <p>
                    <span><i class="fas fa-clock time"></i></span> Time - <span name="time" id="time"></span>
                </p>
                {{-- <p>
                        <span><i class="fas fa-circle task" ></i></span> Schedule - <span name="stationery-vehicles-count" id="stationery-vehicles-count"></span>
                    </p> --}}


            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <style>
        #map-view,
        #details-view {
            position: relative;
            /* height: 800px; */
            height: 80vh;
            width: 100%;
        }

        #custom-control {
            position: absolute;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            z-index: 1;
            right: 55px;
            bottom: 0;
            width: 200px;
            min-height: 150px;
            background-color: white;


        }

        .moving {
            margin-right: 2px;
            color: blue;
        }

        .stationery {
            margin-right: 2px;
            color: red;
        }

        .blue-vehicle i {
            color: blue !important;
        }

        .red-vehicle i {
            color: red !important;
        }

        .olive-vehicle i {
            color: olive !important;
        }

        .green-vehicle i {
            color: green !important;
        }

        #details-view {
            overflow-y: auto;
            padding: 15px;
        }

        /* #vehicle-name {
                    margin: 0;
                    font-weight: 700;
                    font-size: 22px;
                } */

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
            /* border-radius: 50%;
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center; */

        }

        .price-tag i {
            color: #fff;
        }

        .siteNotice {
            font-size: 4px;
        }

        .truck-icon {
            font-size: 20px;
            color: black !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script>
        (g => {
            let h, a, k, p = "The Google Maps JavaScript API",
                c = "google",
                l = "importLibrary",
                q = "__ib__",
                m = document,
                b = window;
            b = b[c] || (b[c] = {});
            let d = b.maps || (b.maps = {}),
                r = new Set,
                e = new URLSearchParams,
                u = () => h || (h = new Promise(async (f, n) => {
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
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() =>
                d[l](f, ...n))
        })({
            key: "{{ $googleMapsApiKey }}",
            v: "weekly",
        });
    </script>
    <script type="text/javascript">
        $(function() {
            $('body').addClass('sidebar-collapse');
            let map;
            let markers = [];
            let polylinePath = [];
            let polyline;

            //attempt playback implementation
            let playbackInterval;
            let playbackIndex = 0;
            let isPlaying = false;
            let vehicleMarker;


            function startPlayback() {
                
                console.log("startPlayback");
                if (!isPlaying) {
                    isPlaying = true;
                    //set  map center
                    map.setCenter({
                        lat: polylinePath[polylinePath.length-1].lat,
                        lng: polylinePath[polylinePath.length-1].lng,
                    });

                    playbackIndex = polylinePath.length - 1;
                    playbackInterval = setInterval(moveVehicle, 200); // Adjust interval as needed
                }
            }

            function pausePlayback() {
                clearInterval(playbackInterval);
                isPlaying = false;
            }

            function resumePlayback() {
                startPlayback();
            }

            function moveVehicle() {

                if (playbackIndex > 0) {
                    const newPosition = polylinePath[playbackIndex];
                    const nextPosition = polylinePath[playbackIndex - 1];

                    // moveMarker(newPosition);
                    // playbackIndex++;
                    if (nextPosition) {
                            const heading = computeHeading(newPosition, nextPosition);
                            moveMarker(newPosition, heading);
                        } else {
                            moveMarker(newPosition);
                        }

                        playbackIndex--;
                } else {
                    pausePlayback();
                }
            }

            function moveMarker(position, heading =  0) {
                console.log(position);
                if (vehicleMarker) {
                        vehicleMarker.position = new google.maps.LatLng(position.lat, position.lng);
                        vehicleMarker.content.style.transform = `rotate(${heading}deg)`;

                    }
            }
            function computeHeading(from, to) {
                    const fromLatLng = new google.maps.LatLng(from.lat, from.lng);
                    const toLatLng = new google.maps.LatLng(to.lat, to.lng);
                    return google.maps.geometry.spherical.computeHeading(fromLatLng, toLatLng);
                }

            // Event listeners for playback controls
            $('#play').on('click', startPlayback);
            $('#pause').on('click', pausePlayback);
            $('#resume').on('click', resumePlayback);




            function fetchLocations() {
                let date = $('#date').val();
                let to_date = $('#to_date').val();
                console.log(date);
                return $.ajax({
                    url: `/admin/vehicle-movement/get-movement/{{ $deviceName }}`,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        date: date,
                        to_date: to_date,
                    },
                    success: function(response) {

                        markers.forEach(marker => marker.setMap(null));
                        markers = [];
                        console.log(markers);
                        polylinePath = [];
                        if (polyline) {
                            polyline.setMap(null);
                        }
                        response.vehicleMovement.forEach((location, index) => {
                            let vehicle_name = location.device;

                            polylinePath.push({
                                lat: location.latitude,
                                lng: location.longitude,
                            });

                            if (index === 0) {
                                map.setCenter({
                                    lat: location.latitude,
                                    lng:location.longitude,
                                });
                            }
                            // if (index === response.vehicleMovement.length - 1) {
                                if (index === 0) {
                                    const vehicleName = location.device;
                                    const speed = location.speed;
                                    const ignition = location.ignition_status;
                                    const time = location.time;
                                    const fuel_level = location.fuel_level;
                                    const mileage = location.mileage;
                                    $('#vehicle-name').text(vehicleName);
                                    $('#speed').text(speed);
                                    $('#ignition').text(ignition);
                                    $('#time').text(time);
                                    $('#fuel').text(fuel_level);
                                    $('#mileage').text(mileage);

                                const icon = document.createElement("div");
                                icon.className = "price-tag";
                                // icon.innerHTML = `<img src="{{ asset('assets/admin/images/car-removebg-preview.png') }}" height="35px" width="30px "/>`;
                                icon.innerHTML = `<img src="{{ asset('assets/admin/images/lorry.png') }}" height="37px" width="20px" />`;
                                // icon.style.position = "absolute"; 
                                // icon.style.transform = "translate(-50%, -50%)"; 
                                

                                // const rotation = response.currentVehicleStatus.direction - 90;
                                const rotation = location.direction;

                                icon.style.transform = `rotate(${rotation}deg)`;


                                if (location.movement && location.speed > 8 && location.speed <=
                                    65) {
                                    icon.classList.add("blue-vehicle");
                                } else if (location.movement && location.speed > 65) {
                                    icon.classList.add("red-vehicle");
                                } else if (location.speed <= 8 && location.ignition_status ===
                                    'ON') {
                                    icon.classList.add("olive-vehicle");
                                } else if (location.ignition_status ===
                                    'OFF') {
                                    icon.classList.add("green-vehicle");
                                }
                                
                                vehicleMarker = new google.maps.marker.AdvancedMarkerElement({
                                    map: map,
                                    position: {
                                        lat: location.latitude,
                                        lng: location.longitude,
                                    },
                                    title: location.device,
                                    content: icon,
                                });
                                markers.push(vehicleMarker);

                            }
                            if (index === response.vehicleMovement.length - 1) {
                                const pin = new google.maps.marker.PinElement({
                                    background: "#027148",
                                    glyphColor: "white",
                                    borderColor: "#000000",

                                    });
                                const startMarker = new google.maps.marker.AdvancedMarkerElement({
                                    map: map,
                                    position: {
                                        lat: location.latitude,
                                        lng: location.longitude,
                                    },
                                    title: 'START',
                                    content: pin.element    ,
                                });
                                markers.push(startMarker);
                            }
                            drawCircle(location, map);


                        });
                        polyline = new google.maps.Polyline({
                            path: polylinePath,
                            geodesic: true,
                            strokeColor: '#FF0000',
                            strokeOpacity: 1.0,
                            strokeWeight: 2,
                        });

                        polyline.setMap(map);
                    }
                });
            }

            function drawCircle(location, map) {
                if (location.movement && location.speed > 8 && location.speed <=
                    65) {
                        //moving
                        strokeColor = '#0000ff';
                        fillColor = '#0000ff';
                } else if (location.movement && location.speed > 65) {
                    //overspeeding
                        strokeColor = '#ff0000';
                        fillColor = '#ff0000';
                } else if (location.speed <= 8 && location.ignition_status ===
                    'ON') {
                        //idling
                        strokeColor = '#808000';
                        fillColor = '#808000';
                } else if (location.ignition_status === 'OFF') {
                    //stationery
                        strokeColor = '#006400';
                        fillColor = '#006400';     
                }
                const icon = document.createElement("div");
                    icon.className = "price-tag";
                    icon.innerHTML = `<i class="fas fa-circle" style="color:${strokeColor}; font-size:10px;"></i>`;
                    icon.style.position = "absolute"; 
                    icon.style.transform = "translate(-50%, -50%)"; 

                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        map: map,
                        position: {
                            lat: location.latitude,
                            lng: location.longitude,
                        },
                        title: `time:${location.time} speed:${location.speed}km/hr fuel:${location.fuel_level}lts mileage:${location.mileage}`,
                        content: icon,
                    });
                    // show tooltip on hover
                    google.maps.event.addListener(marker, 'click', function() {
                        console.log(marker)
                        });
                    marker.content.addEventListener('mouseout', function() {
                        console.log('removed');
                    });

                    markers.push(marker);   
                    

                // const drawncircle = new google.maps.Circle({
                //     strokeColor: strokeColor,
                //     strokeOpacity: 0.8,
                //     strokeWeight: 2,
                //     fillColor: fillColor,
                //     fillOpacity: 0.35,
                //     map,
                //     center: {
                //         lat: location.latitude,
                //         lng: location.longitude
                //     },
                //     radius: 10,
                // });
                // //show details on hovering over the circle
                // google.maps.event.addListener( drawncircle, 'mouseover', function(event) { 

                //   } );

            }

        // function showTooltip(position, title) {
        //     const tooltip = document.getElementById('tooltip');
        //     tooltip.style.display = 'block';
        //     tooltip.style.left = position.latLng.lng() + 'px';
        //     tooltip.style.top = position.latLng.lat() + 'px';
        //     tooltip.innerHTML = title;
        // }

        // function hideTooltip() {
        //     const tooltip = document.getElementById('tooltip');
        //     tooltip.style.display = 'none';
        // }
        function downloadReport() {
                let date = $('#date').val();
                let to_date = $('#to_date').val();
                console.log(date);
                return $.ajax({
                    url: `/admin/vehicle-movement/download-report/{{ $deviceName }}`,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        date: date,
                        to_date: to_date,
                    },
                    success: function(response) {
                        toaster = new Form();
                        console.log(response);
                        window.location.href = response.file; 
                        toaster.successMessage('Report Downloaded Successfully');
                    }
                });
            }

        $('#date').change(function() {  
            pausePlayback()
            fetchLocations();
         });
        $('#to_date').change(function() {  
            pausePlayback()
            fetchLocations();
        });
        $('#download-report-btn').click(function() {  
            event.preventDefault();
            let date = $('#date').val();
            let to_date = $('#to_date').val();
            link = `/admin/vehicle-movement/download-report/{{$deviceName}}/${date}/${to_date}`
            // console.log(link);
            $('#download-report').attr('href', link);
            console.log($('#download-report').attr('href'));
            setTimeout(function(){
                $('#download-report')[0].click();
            },1);

            console.log('clicked');
        });

            async function initMap() {
                const position = {
                    lat: -1.034444,
                    lng: 37.076805
                };
                const {
                    Map
                } = await google.maps.importLibrary("maps");
                const {
                    AdvancedMarkerElement, PinElement
                } = await google.maps.importLibrary("marker");

                map = new Map(document.getElementById("map-view"), {
                    zoom: 15,
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
                setInterval(fetchLocations, 50000);
            }

            initMap();

        });
       
    </script>
@endsection
