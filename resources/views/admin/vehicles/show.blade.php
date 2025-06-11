@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
            window.vehicle = {!! $vehicle !!}
    </script>
    <section class="content" id="vehicle-details">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Vehicle Details </h3>

                    <a href="{{ route("$base_route.index") }}" role="button" class="btn btn-primary"> Go Back </a>
                </div>

                <div class="session-message-container">
                    @include('message')
                </div>
            </div>

            <div class="box-body" style="padding: 0;">
                <div class="row" style="margin: 0; padding: 0;">
                    <div class="col-md-5" id="details-view">
                        <div style="position:relative; height: 100%; width: 100%;" v-if="loading"> Loading vehicle details...</div>

                        <div v-else>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 id="vehicle-name" v-cloak> @{{ vehicle.model?.name }} @{{ vehicle.license_plate_number }} </h5>
                                <div class="labels">
                                    <span class="label" :class="[vehicleDetails.ignition === 'On' ? 'label-success' : 'label-default']">
                                        Ignition: @{{ vehicleDetails.ignition }}
                                    </span>

                                    <span class="label"
                                          :class="[vehicleDetails.movement === 'Moving' ? 'label-primary' : 'label-warning']"
                                          style="margin-left: 6px;" v-if="vehicleDetails.ignition === 'On'"> @{{ vehicleDetails.movement }}</span>
                                </div>
                            </div>

                            <span id="vehicle-address" v-cloak> @{{ vehicle.vin }} </span>

                            <div class="d-flex justify-content-between align-items-center" style="margin-top: 20px;">
                                <div class="major-detail d-flex flex-column justify-content-between">
                                    <div class="d-flex">
                                        <i class="fa fa-user-circle major-detail-icon"></i>
                                        <span class="major-detail-title"> Driver </span>
                                    </div>

                                    <span class="major-detail-value" v-cloak> @{{ vehicle.driver?.name ?? '-' }} </span>
                                </div>

                                <div class="major-detail d-flex flex-column justify-content-between">
                                    <div class="d-flex">
                                        <i class="fas fa-gas-pump major-detail-icon"></i>
                                        <span class="major-detail-title"> Fuel Level </span>
                                    </div>

                                    <span class="major-detail-value"> @{{ vehicleDetails.fuel_level }}L </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center" style="margin-top: 20px;">
                                <div class="major-detail d-flex flex-column justify-content-between">
                                    <div class="d-flex">
                                        <i class="fa fa-road major-detail-icon"></i>
                                        <span class="major-detail-title"> Mileage </span>
                                    </div>

                                    <span class="major-detail-value"> @{{ vehicleDetails.mileage }} Km </span>
                                </div>

                                <div class="major-detail d-flex flex-column justify-content-between">
                                    <div class="d-flex">
                                        <i class="fa fa-dashboard major-detail-icon"></i>
                                        <span class="major-detail-title"> Current Speed </span>
                                    </div>

                                    <span class="major-detail-value"> @{{ vehicleDetails.speed }} km/h </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center" style="margin-top: 20px;">
                                <div class="major-detail d-flex flex-column justify-content-between" style="width: 100%; min-height: 80px; height: auto;">
                                    <div class="d-flex">
                                        <i class="fa fa-map-marker major-detail-icon"></i>
                                        <span class="major-detail-title"> Current Location </span>
                                    </div>

                                    <span class="major-detail-value" style="font-size: 18px;"> @{{ vehicleDetails.address }} </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center" style="margin-top: 20px;" v-if="vehicle.current_schedule">
                                <div class="major-detail d-flex flex-column justify-content-between" style="width: 100%; min-height: 80px; height: auto;">
                                    <div class="d-flex">
                                        <i class="fa fa-route major-detail-icon"></i>
                                        <span class="major-detail-title"> Current Schedule </span>
                                    </div>

                                    <span class="major-detail-value" style="font-size: 18px;"> @{{ vehicle.current_schedule.shift.shift_id }} </span>
                                </div>
                            </div>

                            <div class="table-responsive" style="margin-top: 20px;">
                                <div class="box-header with-border"><h3 class="box-title"> Vehicle Details </h3></div>
                                <table class="table">
                                    <tr>
                                        <th>Type</th>
                                        <td v-cloak> @{{ vehicle.typeName }}</td>
                                    </tr>

                                    <tr>
                                        <th>Color</th>
                                        <td v-cloak> @{{ vehicle.color }}</td>
                                    </tr>

                                    <tr>
                                        <th>Acquisition Date</th>
                                        <td v-cloak> @{{ vehicle.acquisition_date }}</td>
                                    </tr>

                                    <tr>
                                        <th>Telematics Device</th>
                                        <td v-cloak> @{{ vehicle.device_name }}</td>
                                    </tr>

                                    <tr>
                                        <th> Fuel Tank Capacity</th>
                                        <td v-cloak> @{{ vehicle.model?.fuel_tank_capacity }} <span v-if="vehicle.fuel_tank_capacity">L</span></td>
                                    </tr>

                                    <tr>
                                        <th> Unladen Weight</th>
                                        <td v-cloak> @{{ vehicle.model?.unladed_weight }} <span v-if="vehicle.unladen_weight">T</span></td>
                                    </tr>

                                    <tr>
                                        <th> Max Load Capacity</th>
                                        <td v-cloak> @{{ vehicle.model?.ma_load_capacity }} <span v-if="vehicle.max_load_capacity">T</span></td>
                                    </tr>

                                    <tr>
                                        <th>Axles</th>
                                        <td v-cloak> @{{ vehicle.model?.axle_count }}</td>
                                    </tr>

                                    <tr>
                                        <th>Tyres</th>
                                        <td v-cloak> @{{ vehicle.model?.tyre_count }}</td>
                                    </tr>

                                    <tr>
                                        <th>Travel Expense</th>
                                        <td v-cloak> @{{ vehicle.model?.travel_expense }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7" id="map-view"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <style>
        #map-view, #details-view {
            height: 700px;
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
            background-color: darkblue;
            border-radius: 50%;
            /*color: #FFFFFF;*/
            /*padding: 15px;*/
            position: relative;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .price-tag i {
            color: #fff;
        }

        /*.price-tag::after {*/
        /*    content: "";*/
        /*    position: absolute;*/
        /*    left: 50%;*/
        /*    top: 100%;*/
        /*    transform: translate(-50%, 0);*/
        /*    width: 0;*/
        /*    height: 0;*/
        /*    border-left: 8px solid transparent;*/
        /*    border-right: 8px solid transparent;*/
        /*    border-top: 8px solid darkblue;*/
        /*}*/

        .truck-icon {
            font-size: 20px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
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
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="importmap">
        {
          "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
          }
        }
    </script>
    <script type="module">
        import {createApp} from 'vue';

        const app = createApp({
            data() {
                return {
                    loading: true,
                    vehicleDetails: {}
                }
            },

            async mounted() {
                $('body').addClass('sidebar-collapse');
                this.initMap();
                // this.initPusher();

                if (this.vehicle.device_name) {
                    await this.getInitialVehicleData().then(() => {
                        setInterval(() => {
                            this.getInitialVehicleData()
                        }, 5000);
                    })
                } else {
                    this.loading = false
                }
            },

            computed: {
                currentUser() {
                    return window.user
                },

                vehicle() {
                    return window.vehicle
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                async initMap() {
                    const {Map} = await google.maps.importLibrary("maps");
                    await google.maps.importLibrary("geometry");

                    this.map = new Map(document.getElementById("map-view"), {
                        center: {lat: -1.28333, lng: 36.81667},
                        zoom: 13,
                        mapId: "7c9bd9e078617725",
                    });
                },

                initPusher() {
                    // Enable pusher logging - don't include this in production
                    // Pusher.logToConsole = true;

                    let pusher = new Pusher('b2012a3c72f2a36ad705', {
                        cluster: 'ap2'
                    });

                    let channel = pusher.subscribe('telematics');
                    channel.bind('data.received', function (data) {
                        console.log(data)
                    });
                },

                resetMap() {
                    $("#map-view").css("display", "none");
                    setTimeout(async () => {
                        $("#map-view").css("display", "block");
                        await this.initMap();
                    }, 100)
                },

                async drawVehicleOnMap(lat, lng) {
                    const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary("marker");

                    const icon = document.createElement("div");
                    // icon.innerHTML = '<i class="fas fa-truck-moving fa-2x"></i>';
                    // const pinScaled = new PinElement({
                    //     scale: 2.0,
                    //     background: "#FBBC04",
                    //     borderColor: "#137333",
                    //     glyph: icon,
                    // });
                    icon.className = "price-tag";
                    icon.innerHTML = '<i class="fas fa-truck-moving truck-icon"></i>';

                    if (!this.marker) {
                        this.marker = new AdvancedMarkerElement({
                            position: new google.maps.LatLng(lat, lng),
                            content: icon,
                            title: `${this.vehicle.name} ${this.vehicle.license_plate_number}`,
                        });

                        this.marker.map = this.map;
                    } else {
                        this.marker.position = new google.maps.LatLng(lat, lng)
                    }
                },

                async getInitialVehicleData() {
                    // axios.get('http://telematics.test/api/devices/latest', {
                    axios.get('https://telematics.bizwizrp.com/api/devices/latest', {
                        params: {
                            device_name: this.vehicle.device_name
                        }
                    }).then(async res => {
                        this.loading = false
                        this.vehicleDetails = res.data.data
                        this.vehicleDetails.mileage = this.vehicleDetails.mileage - this.vehicle.odometer_adjustment

                        await this.drawVehicleOnMap(this.vehicleDetails.lat, this.vehicleDetails.lng)
                        this.map.setCenter({lat: this.vehicleDetails.lat, lng: this.vehicleDetails.lng})
                        this.map.setZoom(15)
                    }).catch(error => {
                        this.loading = false
                        this.toaster.errorMessage(error.response?.message)
                    })
                },
            },
        })

        app.mount('#vehicle-details')
    </script>
@endsection