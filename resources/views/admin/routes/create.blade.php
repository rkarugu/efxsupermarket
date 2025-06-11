@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
            window.branches = {!! $branches !!}
    </script>

    <section class="content" id="add-route-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Route </h3>

                    <a href="{{ route("manage-routes.listing") }}" class="btn btn-outline-primary">
                        << Back to Route List </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div id="add-route-wizard">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#route-details">
                                <div class="num">1</div>
                                Route Details
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#targets">
                                <span class="num">2</span>
                                Targets & Estimates
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#centers">
                                <span class="num">3</span>
                                Delivery Centers
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="loader-overlay" id="loader">
                            <div class="custom-loader"></div>
                            <span id="loader-message" class="loading-message"></span>
                        </div>

                        <div id="route-details" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                            <form method="post" class="form-horizontal" novalidate @submit.prevent="saveRouteDetails">
                                <div class="form-group">
                                    <label for="route_name" class="control-label col-md-2"> Route Name </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="route_name" id="route_name"
                                               placeholder="Route name" v-model="routeDetails.route_name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="restaurant_id" class="control-label col-md-2"> Branch </label>
                                    <div class="col-md-10">
                                        <select name="branch_id" id="branch_id" class="form-control" required v-model="routeDetails.restaurant_id">
                                            <option v-for="branch in branches" :key="branch.id" :value="branch.id"> @{{ branch.name }}</option>
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
                                <div class="form-group">
                                    <label for="is_pos_route" class="control-label col-md-2"> POS Route </label>
                                    <div class="col-md-10">
                                        <input type="checkbox" id="is_pos_route" v-model="is_pos_route">
                                        <input type="hidden" name="is_pos_route" :value="is_pos_route ? 1 : 0">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="starting_location_name" class="control-label col-md-2"> Loading Location </label>
                                    <div class="col-md-10">
                                        <input type="text" name="starting_location_name" id="starting_location_name"
                                               class="form-control google_location" placeholder="Search location ..." v-model="routeDetails.starting_location_name">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="location-latitude" class="control-label col-md-2"> Loading Latitude </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="loading_latitude" id="location-latitude" placeholder="Latitude"
                                               v-model="routeDetails.loading_latitude">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="location-longitude" class="control-label col-md-2"> Loading Longitude </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="loading_longitude" id="location-longitude" placeholder="Longitude"
                                               v-model="routeDetails.loading_longitude">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="order_taking_days" class="control-label col-md-2"> Order Taking Days </label>
                                    <div class="col-md-10">
                                        <select name="order_taking_days" id="order_taking_days" class="form-control" multiple
                                                v-model="routeDetails.order_taking_days">
                                            <option v-for="(day, index) in weekDays" :key="index" :value="index">@{{ day }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="order_frequency" class="control-label col-md-2">Order Frequency (Weekly)</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="order_frequency" id="order_frequency"
                                               v-model="routeDetails.order_frequency">
                                    </div>
                                </div>
                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="monthly_order_frequency" class="control-label col-md-2">Order Frequency (Monthly)</label>
                                    <div class="col-md-10">
                                        <select v-model="routeDetails.monthly_order_frequency" id="monthly_order_frequency"
                                            name="monthly_order_frequency" class="form-control" multiple>
                                            <option value="7 DAYS">7 DAYS</option>
                                            <option value="14 DAYS">14 DAYS</option>
                                            <option value="21 DAYS">21 DAYS</option>
                                            <option value="28 to 31 DAYS">28 to 31 DAYS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="maximum_shifts" class="control-label col-md-2"> Maximum Shifts Per Day </label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="maximum_shifts" id="maximum_shifts" 
                                               v-model="routeDetails.maximum_shifts">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="salesman_proximity" class="control-label col-md-2"> Max Proximity</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="salesman_proximity" id="salesman_proximity"
                                               placeholder="100" min="0" step="0.01" v-model="routeDetails.salesman_proximity">
                                        <div class="form-text">
                                            The maximum distance (in meters) that a salesman or route manager should be from a shop to take an order or verify.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="group" class="control-label col-md-2"> Group</label>
                                    <div class="col-md-10">
                                        <select v-model="routeDetails.group" id="group" name="group" class="form-control">
                                            <option value="">Select Option</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-primary" @click="saveRouteDetails" v-if="!routeIsPhysical"> Add Route</button>
                                        <button class="btn btn-primary" @click="proceedToStep($event, 1)" v-else> Next</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div id="targets" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                            <form method="post" class="form-horizontal" novalidate @submit.prevent="saveRouteDetails" v-if="routeIsPhysical">
                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="sales_target" class="control-label col-md-2"> Total Sales</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="sales_target" id="sales_target"
                                               placeholder="0" min="0" step="0.01" v-model="routeDetails.sales_target">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="tonnage_target" class="control-label col-md-2"> Tonnage</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="tonnage_target" id="tonnage_target"
                                               placeholder="0" min="0" step="0.01" v-model="routeDetails.tonnage_target">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="ctn_target" class="control-label col-md-2"> CTNs</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="ctn_target" id="ctn_target"
                                               placeholder="0" min="0" step="0.01" v-model="routeDetails.ctn_target">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="dzn_target" class="control-label col-md-2"> DZNs</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="dzn_target" id="dzn_target"
                                               placeholder="0" min="0" step="0.01" v-model="routeDetails.dzn_target">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="manual_fuel_estimate" class="control-label col-md-2"> Fuel Estimate (L)</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="manual_fuel_estimate" id="manual_fuel_estimate"
                                               placeholder="0" min="0" step="0.01" v-model="routeDetails.manual_fuel_estimate">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="travel_expense" class="control-label col-md-2"> Onsite Travel Expense</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="travel_expense" id="travel_expense" v-model="routeDetails.travel_expense">
                                    </div>
                                </div>

                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="offsite_shift_allowance" class="control-label col-md-2"> Offsite Allowance</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="offsite_shift_allowance" id="offsite_shift_allowance"
                                               placeholder="0" min="0" step="0.01" v-model="routeDetails.offsite_shift_allowance">
                                    </div>
                                </div>
                                <div class="form-group" v-if="routeIsPhysical">
                                    <label for="offsite_shift_allowance" class="control-label col-md-2"> Standard Shift Time</label>
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" name="estimated_shift_time" id="estimated_shift_time"
                                               placeholder="0" min="1"  v-model="routeDetails.estimated_shift_time" required>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-primary" @click="proceedToStep($event, 0)"> Previous</button>
                                        <button class="btn btn-primary" @click="saveRouteDetails" v-if="!addedRoute"> Add Route</button>
                                        <button class="btn btn-primary" @click="proceedToStep($event, 2)" v-else> Next</button>
                                    </div>
                                </div>
                            </form>

                            <p v-else> Targets and estimates do not apply for a non-physical route. </p>
                        </div>

                        <div id="centers" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;">#</th>
                                        <th> Center Name</th>
                                        <th> Center Location</th>
                                        <th> Latitude</th>
                                        <th> Longitude</th>
                                        <th> Preferred Radius</th>
                                        <th style="width: 3%;"></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(center, index) in centers" :key="index" v-cloak>
                                        <th style="width: 3%;" scope="row"> @{{ index + 1 }}</th>
                                        <td>
                                            <input type="text" class="form-control" v-model="center.name" :id="`center-name-${index}`">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" v-model="center.center_location_name" :id="`center-location-${index}`">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" v-model="center.lat" :id="`center-lat-${index}`">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" v-model="center.lng" :id="`center-lng-${index}`">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" v-model="center.preferred_center_radius" :id="`center-radius-${index}`">
                                        </td>
                                        <td style="width: 3%;">
                                            <i class="fa fa-trash text-danger fa-2x" style="cursor:pointer;" @click="removeCenter(index)"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end" style="margin-top: 10px;">
                                    <button class="btn btn-primary" @click="addCenter"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="box-footer" style="margin-top: 10px;">
                                <button class="btn btn-primary" @click="saveCenters"> Save Centers</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .tab-title {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .control-label {
            text-align: left !important;
        }

        .tab-content {
            min-height: 600px !important;
            overflow-y: auto !important;
        }

        :root {
            --sw-anchor-active-primary-color: #ff0000;
            --sw-progress-color: #ff0000;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
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
                    routeIsPhysical: true,
                    is_pos_route: false,
                    routeDetails: {
                        salesman_proximity: 100,
                        offsite_allowance: 200
                    },
                    weekDays: ['Sundays', 'Mondays', 'Tuesdays', 'Wednesdays', 'Thursdays', 'Fridays', 'Saturdays'],
                    addedRoute: null,
                    centers: [],
                }
            },

            watch: {
                routeIsPhysical(newVal, oldVal) {
                    if (newVal) {
                        this.initSelect2()
                    }
                }
            },

            created() {

            },

            mounted() {
                $('#add-route-wizard').smartWizard({
                    theme: 'dots',
                    transition: {
                        animation: 'fade'
                    },
                    toolbar: {
                        position: 'none',
                    },
                    anchor: {
                        enableDoneState: true,
                        unDoneOnBackNavigation: true,
                    },
                    keyboard: {
                        keyNavigation: false,
                    },
                });
                this.initializeRouteLocationSearch();
                this.initSelect2();

                $("#branch_id").change(() => {
                    this.routeDetails.restaurant_id = $("#branch_id").val();

                    let branch = this.branches.find(_branch => _branch.id === parseInt(this.routeDetails.restaurant_id))
                    if (this.branchHasValidCoordinates(branch)) {
                        this.routeDetails.loading_latitude = branch.latitude
                        this.routeDetails.loading_longitude = branch.longitude
                        this.routeDetails.starting_location_name = `${branch.name} Loading Point`
                    }
                });

                $("#order_taking_days").change(() => {
                    this.routeDetails.order_taking_days = $("#order_taking_days").val();
                })

                $("#monthly_order_frequency").change(() => {
                    this.routeDetails.monthly_order_frequency = $("#monthly_order_frequency").val();
                })

            },

            computed: {
                currentUser() {
                    return window.user
                },

                branches() {
                    return window.branches
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                initializeRouteLocationSearch() {
                    let StartingLocationInput = document.getElementById('starting_location_name');
                    let locationoptions = {};

                    let autocomplete = new google.maps.places.Autocomplete(StartingLocationInput, locationoptions);

                    google.maps.event.addListener(autocomplete, 'place_changed', () => {
                        let location_place = autocomplete.getPlace();
                        this.routeDetails.starting_location_name = location_place.formatted_address

                        let location_lat = location_place.geometry.location.lat();
                        $("#location-latitude").val(location_lat);
                        this.routeDetails.loading_latitude = location_lat

                        let location_lng = location_place.geometry.location.lng();
                        $("#location-longitude").val(location_lng);
                        this.routeDetails.loading_longitude = location_lng
                    });
                },

                initializeCenterLocationSearch(index) {
                    // this.centers.forEach((center, index) => {
                    //
                    // })

                    let field = $('#centers').find(`#center-location-${index}`);
                    console.log(field[0]);
                    let options = {};

                    let autocomplete = new google.maps.places.Autocomplete(field[0], options);
                    google.maps.event.addListener(autocomplete, 'place_changed', function () {
                        let location_place = autocomplete.getPlace();
                        let location_lat = location_place.geometry.location.lat();
                        let location_lng = location_place.geometry.location.lng();
                        $(`#center-lat-${index}`).val(location_lat);
                        $(`#center-lng-${index}`).val(location_lng);
                    });
                },

                initSelect2() {
                    setTimeout(() => {
                        $("#branch_id").select2();
                        $("#order_taking_days").select2();
                        $("#monthly_order_frequency").select2();
                    }, 100)
                },

                proceedToStep(e, step) {
                    e?.preventDefault();
                    $('#add-route-wizard').smartWizard("goToStep", step, true);
                },

                saveRouteDetails(e) {
                    e.preventDefault();

                    if (!this.routeDetails.route_name) {
                        return this.toaster.errorMessage('Route name is required')
                    }

                    if (!this.routeDetails.restaurant_id) {
                        return this.toaster.errorMessage('Branch is required')
                    }

                    $("#loader-message").text('');
                    $("#loader").css('display', 'flex');

                    this.routeDetails.is_physical_route = this.routeIsPhysical ? 1 : 0
                    this.routeDetails.is_pos_route = this.is_pos_route ? 1 : 0
                    axios.post('/api/routes/store', this.routeDetails).then(res => {
                        $("#loader").css('display', 'none');
                        this.toaster.successMessage('Route added successfully')

                        if (!this.routeIsPhysical) {
                            return window.location.assign('/admin/routes-list')
                        }

                        this.addedRoute = res.data.data
                        this.addCenter()
                        this.proceedToStep(null, 2)
                    }).catch(error => {
                        $("#loader").css('display', 'none');
                        this.toaster.errorMessage(error.response?.data?.message)
                    })
                },

                branchHasValidCoordinates(branch) {
                    return (branch.latitude) && (branch.longitude) && (branch.latitude !== 0) && (branch.longitude !== 0)
                },

                addCenter() {
                    this.centers.push({
                        preferred_center_radius: 1000
                    })

                    setTimeout(() => {
                        let index = this.centers.length - 1
                        let field = $('#centers').find(`#center-location-${index}`);
                        let options = {};

                        let autocomplete = new google.maps.places.Autocomplete(field[0], options);
                        google.maps.event.addListener(autocomplete, 'place_changed', () => {
                            let location_place = autocomplete.getPlace();
                            this.centers[index].center_location_name = location_place.formatted_address

                            let location_lat = location_place.geometry.location.lat();
                            $(`#center-lat-${index}`).val(location_lat);
                            this.centers[index].lat = location_lat

                            let location_lng = location_place.geometry.location.lng();
                            $(`#center-lng-${index}`).val(location_lng);
                            this.centers[index].lng = location_lng
                        });
                    }, 1000)
                },

                removeCenter(index) {
                    this.centers.splice(index, 1)
                },

                saveCenters() {
                    let allCentersAreValid = true
                    this.centers.forEach(center => {
                        if (!center.name || !center.lat || !center.lng) {
                            allCentersAreValid = false
                            return;
                        }
                    })

                    if (!allCentersAreValid) {
                        return this.toaster.errorMessage('Provide name, latitude, and longitude for all centers')
                    }

                    $("#loader-message").text('');
                    $("#loader").css('display', 'flex');

                    let payload = {
                        route_id: this.addedRoute.id,
                        centers: this.centers
                    }
                    axios.post('/api/delivery-centers/store', {payload: JSON.stringify(payload)}).then(res => {
                        $("#loader").css('display', 'none');
                        this.toaster.successMessage('Centers added successfully')

                        return window.location.assign('/admin/routes-list')
                    }).catch(error => {
                        $("#loader").css('display', 'none');
                        this.toaster.errorMessage(error.response?.data?.message)
                    })
                }
            },
        })

        app.mount('#add-route-page')
    </script>
@endsection
