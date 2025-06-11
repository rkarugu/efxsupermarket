@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
        window.routes = {!! $routeList !!}
    </script>

    <section class="content" id="routes">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Routes </h3>

                    <div>
                        <a href="{{ route("$base_route.export") }}" role="button" class="btn btn-primary" target="_blank"> Export to Excel </a>
                        <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-primary" style="margin-left: 12px;"> Add Route </a>
                    </div>
                </div>

                <div class="session-message-container">
                    @include('message')
                </div>
            </div>

            <div class="box-body" style="padding: 0;">
                <div style="position:relative; height: calc(100vh - 260px);">
{{--                    <div class="custom-loader" id="general-loader"></div>--}}
                    <div id="routes-map"></div>

                    <div id="routes-list">
{{--                        <div class="custom-loader" id="routes-loader"></div>--}}
                        <div style="position:relative; width: 100%; height: 100%; overflow-y: auto;" class="box box-primary">
                            <div class="box-header with-border">
                                <div class="box-header-flex">
                                    <h3 class="box-title"> Click a route to view it </h3>
                                    <button class="btn btn-primary btn-sm" @click="resetRoutes"> Reset</button>
                                </div>
                            </div>

                            <div class="box-body" v-cloak>
                                <div id="search-routes" class="d-flex">
                                    <input type="text" class="form-control" placeholder="Search routes" v-model="searchQuery">
                                </div>

                                <span style="display: inline-block; margin-top: 10px;" v-cloak> @{{ displayRoutes.length }} routes found. </span>

                                <ul style="padding: 0; margin: 0; list-style-type: none;" v-cloak>
                                    <li v-for="route in displayRoutes" :key="route.id" class="route" :class="{active: (route.id === activeRoute?.id)}">
                                        <div style="display: flex; flex-direction: column; cursor:pointer; flex-grow: 1;" @click="viewRoute(route)">
                                            <span class="route-name" style="font-weight: 600;"> @{{ route.route_name }} </span>
                                            <span class="route-data" v-if="route.is_physical_route === 1">
                                                @{{ route.branch }} | @{{ route.centers_count }} Centers | @{{ route.shops_count }} Customers | @{{ route.total_distance ?? 0 }} Km
                                            </span>
                                        </div>

                                        <div class="action-button-div">
                                            <a :href="`/admin/manage-routes/${route.id}/edit`" title="Edit Route"> <i class='fa fa-edit text-primary fa-lg'></i> </a>
                                            <a :href="`/api/route-customer-export-all/?route_id=${route.id}`" title="Download"> <i class='fas fa-download text-primary'></i> </a>
                                            <!-- <a :href="`/admin/manage-routes/${route.id}`" title="Manage Route Sections" v-if="route.is_physical_route === 1">
                                                <i class='fa fa-eye text-primary fa-lg'></i>
                                            </a> -->
                                            <a :href="`/admin/route-linked-centers-list/${route.id}`" title="Manage Delivery Centers" v-if="route.is_physical_route === 1">
                                                <i class='fa fa-truck text-info fa-lg'></i>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="stats">
{{--                        <div class="custom-loader" id="stats-loader"></div>--}}

                        <div style="position:relative; width: 100%; height: 100%; overflow-y: auto;" class="box box-primary" v-cloak>
                            <div class="box-header with-border">
                                <div class="box-header-flex">
                                    <h3 class="box-title"> Totals </h3>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="stats-item">
                                    <span> Total Routes </span>
                                    <span> @{{ stats.total_routes }} </span>
                                </div>

                                <div class="stats-item">
                                    <span> Total Customers </span>
                                    <span> @{{ stats.total_customers }} </span>
                                </div>

                                <div class="stats-item">
                                    <span> Total Target (Sales) </span>
                                    <span> @{{ stats.total_sales_target }} </span>
                                </div>

                                <div class="stats-item">
                                    <span> Total Target (Tonnage) </span>
                                    <span> @{{ stats.total_tonnage_target }}T </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <style>
        #routes-map {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #routes-list {
            position: absolute;
            left: 10px;
            bottom: 10px;
            top: 10px;
            width: 30%;
            background-color: white;
            border-radius: 15px;
        }

        #stats {
            position: absolute;
            right: 55px;
            bottom: 0;
            width: 300px;
            background-color: white;
            border-top-right-radius: 15px;
            border-top-left-radius: 15px;
            min-height: 150px;
        }

        .route {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            padding: 8px 0;
        }

        .route.active {
            color: darkblue;
            font-weight: 600;
            border-bottom: 2px solid darkblue;
        }

        .stats-item {
            display: flex;
            justify-content: space-between;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
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
                    stats: [],
                    searchQuery: null,
                    activeRoute: null,
                    displayedRoutes: [],
                    displayedMarkers: [],
                    displayedCenters: [],
                }
            },

            created() {
                this.getRouteStats()
            },

            mounted() {
                $('body').addClass('sidebar-collapse');
                this.initMap();
                this.initPusher();
            },

            computed: {
                currentUser() {
                    return window.user
                },

                routes() {
                    return window.routes
                },

                displayRoutes() {
                    let displayRoutes = this.routes
                    if (this.searchQuery) {
                        displayRoutes = displayRoutes.filter(_route => {
                            return (_route.route_name.toLowerCase().includes(this.searchQuery.toLowerCase())) || (_route.branch.toLowerCase().includes(this.searchQuery.toLowerCase()))
                        })
                    }

                    return displayRoutes
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                async initMap() {
                    const {Map} = await google.maps.importLibrary("maps");
                    await google.maps.importLibrary("geometry");

                    this.map = new Map(document.getElementById("routes-map"), {
                        center: {lat: -1.28333, lng: 36.81667},
                        zoom: 8,
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
                    channel.bind('data.received', function(data) {
                        console.log(data)
                    });
                },

                getRouteStats() {
                    $("#stats-loader").css("display", "block");
                    axios.get('/api/routes/map-view-stats', {
                        params: {
                            user_role_id: this.currentUser.role_id,
                            user_id: this.currentUser.id,
                            user_restaurant_id: this.currentUser.restaurant_id
                        }
                    }).then(res => {
                        $("#stats-loader").css("display", "none");
                        this.stats = res.data.data
                    }).catch(error => {
                        $("#stats-loader").css("display", "none");
                    })
                },

                resetRoutes() {
                    this.resetMap()
                    this.activeRoute = null
                    this.searchQuery = null
                },

                resetMap() {
                    $("#routes-map").css("display", "none");
                    setTimeout(async () => {
                        $("#routes-map").css("display", "block");
                        await this.initMap();
                    }, 100)
                },

                async viewRoute(route) {
                    const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary("marker");

                    this.activeRoute = route

                    $("#routes-map").css("display", "none");
                    setTimeout(async () => {
                        $("#routes-map").css("display", "block");
                        await this.initMap().then(async () => {
                            this.activeRoute.centers.forEach(center => {
                                if (center.has_valid_location) {
                                    this.drawDeliveryCenter(center.lat, center.lng, center.preferred_center_radius)
                                }
                            })

                            this.activeRoute.wa_route_customer.forEach(shop => {
                                if (shop.has_valid_location) {
                                    this.drawMarker(shop.lat, shop.lng, shop.bussiness_name)
                                }
                            })

                            if (this.activeRoute.has_valid_location) {
                                this.map.setCenter({lat: this.activeRoute.start_lat, lng: this.activeRoute.start_lng})
                                this.map.setZoom(12)

                                const icon = document.createElement("div");
                                icon.innerHTML = '<i class="fa fa-home fa-2x"></i>';
                                const pinScaled = new PinElement({
                                    scale: 2.0,
                                    background: "#FBBC04",
                                    borderColor: "#137333",
                                    glyph: icon,
                                });

                                const marker = new AdvancedMarkerElement({
                                    position: new google.maps.LatLng(this.activeRoute.start_lat, this.activeRoute.start_lng),
                                    content: pinScaled.element,
                                    title: this.activeRoute.starting_location_name ?? 'Loading Location',
                                });

                                marker.map = this.map;
                                for (const polyline of this.activeRoute.polylines) {
                                    const decodedPolyline = google.maps.geometry.encoding.decodePath(polyline.polyline);
                                    await this.renderRoute(decodedPolyline)
                                }
                            } else {
                                this.toaster.errorMessage('The selected route does not have a valid starting location.')
                            }
                        });
                    }, 100)

                },

                getRoutePolyLine(routeId) {
                    $("#general-loader").css("display", "block");

                    axios.get('/api/routes/get-polylines', {
                        params: {
                            route_id: routeId
                        }
                    }).then(async res => {
                        let response = res.data.data

                        for (const leg of response) {
                            let polyline = leg.routes[0].polyline.encodedPolyline
                            const decodedPolyline = google.maps.geometry.encoding.decodePath(polyline);
                            await this.renderRoute(decodedPolyline)
                        }

                        $("#general-loader").css("display", "none");
                    }).catch(error => {
                        $("#general-loader").css("display", "none");
                        if (error.response && error.response.status === 422) {
                            this.toaster.errorMessage('The selected route does not have a valid starting location.')
                        }

                        console.log(error)
                        this.toaster.errorMessage('An error was encountered while loading route. Please try again.')
                    })
                },

                drawDeliveryCenter(lat, lng, radius) {
                    const drawnCenter = new google.maps.Circle({
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.35,
                        center: {lat: lat, lng: lng},
                        radius: radius
                    });

                    drawnCenter.setMap(this.map)
                },

                renderRoute(path) {
                    let route = new google.maps.Polyline({
                        path: path,
                        geodesic: true,
                        strokeColor: "#0000FF",
                        strokeOpacity: 1.0,
                        strokeWeight: 8,
                    });

                    route.setMap(this.map);
                },

                async drawMarker(lat, lng, title) {
                    const {InfoWindow} = await google.maps.importLibrary("maps");

                    // const image = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
                    let marker = new google.maps.Marker({
                        position: new google.maps.LatLng(lat, lng),
                        title: title
                    });

                    marker.setMap(this.map);

                    const infoWindow = new InfoWindow();
                    marker.addListener("click", ({domEvent, latLng}) => {
                        const {target} = domEvent;
                        infoWindow.close();
                        infoWindow.setContent(marker.title);
                        infoWindow.open(marker.map, marker);
                    });
                },
            },
        })

        app.mount('#routes')
    </script>
@endsection