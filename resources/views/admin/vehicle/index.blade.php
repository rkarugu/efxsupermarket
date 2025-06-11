@extends('layouts.admin.admin')

@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> My Fleet </h3>

                    <div style="display: flex; align-items: center;">
                        <label class="toggle-text" style="margin-right: 12px;">Map View</label>
                        <label class="switch" style="margin-right: 12px;">
                            <input type="checkbox" id="toggle" onclick="toggleMapView()">
                            <span class="slider round"></span>
                        </label>

                        @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <a href="{!! route($model.'.create')!!}" class="btn btn-success">Add Vehicle</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive" id="table-view">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Vehicle Name</th>
                            <th>License Plate</th>
                            <th>Acquisition Date</th>
                            <th>VIN</th>
                            <th>Maximum Load Capacity</th>
                            <th>Status</th>
                            <th>Driver</th>
                            <th>Telematics Device</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($vehicles as $index => $vehicle)
                            <tr>
                                <th scope="row"> {{ $index + 1 }} </th>
                                <td> {{ $vehicle->name }} </td>
                                <td> {{ $vehicle->license_plate }} </td>
                                <td> {{ $vehicle->acquisition_date }} </td>
                                <td> {{ $vehicle->vin_sn }} </td>
                                <td> {{ $vehicle->load_capacity }}T</td>
                                <td> {{ ucfirst($vehicle->status) }} </td>
                                <td> {{ $vehicle->driver ? $vehicle->driver->name : '-' }} </td>
                                <td> {{ $vehicle->device ? $vehicle->device->device_name : '-' }} </td>
                                <td>
                                    <div class="action-button-div">
                                        @if(!$vehicle->device)
                                            <a href="{{ route("vehicles.attach-device-form", $vehicle->id) }}" title="Attach Device">
                                                <i class="fas fa-map-marked-alt text-primary fa-lg" aria-hidden="true"></i>
                                            </a>
                                        @endif

                                        @if(!$vehicle->driver)
                                            <a href="{{ route("vehicles.assign-driver-form", $vehicle->id) }}" title="Assign Driver">
                                                <i class="fas fa-user-tie text-primary fa-lg" aria-hidden="true"></i>
                                            </a>
                                        @endif

                                        @if($vehicle->device)
                                            <button title="Detach Device" data-toggle="modal" data-target="#confirm-detach-device-modal" data-backdrop="static"
                                                    data-id="{{ $vehicle->id }}">
                                                <i class="fas fa-unlink text-danger"></i>
                                                <form action="{{ route("vehicles.detach-device", $vehicle->id) }}" method="post"
                                                      id="detach-form-{{ $vehicle->id }}">
                                                    {{ csrf_field() }}
                                                </form>
                                            </button>
                                        @endif

                                        @if($vehicle->driver)
                                            <button title="Remove Driver" data-toggle="modal" data-target="#confirm-detach-device-modal" data-backdrop="static"
                                                    data-id="{{ $vehicle->id }}">
                                                <i class="fas fa-user-slash text-danger"></i>
                                                <form action="{{ route("vehicles.detach-device", $vehicle->id) }}" method="post"
                                                      id="detach-form-{{ $vehicle->id }}">
                                                    {{ csrf_field() }}
                                                </form>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="map-view" style="display: none;">
                    <div id="map" style="height: 700px;"></div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirm-detach-device-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Detach Device </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <p style="font-size: 16px;"> Are you sure you want to detach device this from vehicle this? </p>
                    <input type="hidden" id="subject-vehicle">
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-primary" onclick="detachDevice();">Yes, Detach</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagestyle')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute !important;
            content: "" !important;
            height: 26px !important;
            width: 26px !important;
            left: 4px !important;
            bottom: 4px !important;
            background-color: white !important;
            -webkit-transition: .4s !important;
            transition: .4s !important;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script async src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap"></script>
    <script type="text/javascript">
        function toggleMapView() {
            let checkBox = document.getElementById("toggle");
            if (checkBox.checked === true) {
                $("#table-view").css('display', 'none');
                $("#map-view").css('display', 'block');
            } else {
                $("#map-view").css('display', 'none');
                $("#table-view").css('display', 'block');
            }
        }

        $('#confirm-detach-device-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');

            $("#subject-vehicle").val(dataValue);
        })

        function detachDevice() {
            let subjectVehicleId = $("#subject-vehicle").val();
            $(`#detach-form-${subjectVehicleId}`).submit();
        }
    </script>

    <script type="text/javascript">
        let map;

        async function initMap() {
            const {Map, InfoWindow} = await google.maps.importLibrary("maps");
            const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary("marker");
            const {LatLng} = await google.maps.importLibrary("core");

            // Set map center to route starting point
            let mapCenter = {lat: -1.0346724932864964, lng: 37.07750398629753}

            // Init Map
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 9,
                center: mapCenter,
                mapId: "8a023462a9950e01",
            });

            $.ajax({
                url: "/api/vehicles/live", success: function (result) {
                    if (result.success === 1) {
                        let vehicles = result.vehicles;
                        vehicles.forEach((vehicle) => {
                            // const icon = document.createElement("div");
                            // icon.innerHTML = '<i class="fa fa-truck fa-2x"></i>';
                            // const pinScaled = new PinElement({
                            //     scale: 2.0,
                            //     background: "#FBBC04",
                            //     borderColor: "#137333",
                            //     glyph: icon,
                            // });

                            const beachFlagImg = document.createElement("img");
                            beachFlagImg.src = "/assets/truck.png";
                            // beachFlagImg.src = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";

                            //pinScaled.element
                            const marker = new AdvancedMarkerElement({
                                map,
                                position: new google.maps.LatLng(vehicle.device_data.lat, vehicle.device_data.lng),
                                content: beachFlagImg,
                                title: `${vehicle.device.device_name} | Fuel: ${vehicle.device_data.fuel} | Speed: ${vehicle.device_data.speed} km/h`,
                            });

                            const infoWindow = new InfoWindow();
                            let content = `
                                        <div>
                                            <strong> ${vehicle.device.device_name} </strong/>
                                            <br>
                                            <span> <strong> Driver </strong>: ${vehicle.driver?.name ?? '-'} </span>
                                            <br>
                                            <span> <strong> Fuel </strong>: ${vehicle.device_data.fuel} </span>
                                             <br>
                                            <span> <strong> Speed </strong>: ${vehicle.device_data.speed} km/h </span>
                                        </div>
                                        `;
                            infoWindow.setContent(content);
                            // infoWindow.setContent(marker.title);
                            infoWindow.open(marker.map, marker);

                            marker.addListener("click", ({domEvent, latLng}) => {
                                const {target} = domEvent;
                                infoWindow.close();
                                infoWindow.setContent(content);
                                // infoWindow.setContent(marker.title);
                                infoWindow.open(marker.map, marker);
                            });
                        })
                    }
                }
            });
        }

        window.initMap = initMap;
    </script>
@endsection

