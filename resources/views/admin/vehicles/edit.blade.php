@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
        window.vehicle = {!! $vehicle !!}
    </script>

    <section class="content" id="edit-vehicle-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $vehicle->license_plate_number }} | Edit </h3>
                    <a href="{{ route("$base_route.index") }}" class="btn btn-primary"> << Back to My Fleet </a>
                </div>

                <div class="box-body">
                    <!-- SmartWizard html -->
                    <div id="edit-vehicle-wizard">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#vehicle-details">
                                    <div class="num">1</div>
                                    Vehicle Details
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#telematics">
                                    <span class="num">2</span>
                                    Telematics
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="vehicle-details" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                <form method="post" @submit.prevent="goToTelematics" id="vehicle-details-form" class="form-horizontal">
                                   

                                    <div class="form-group">
                                        <label for="license_plate_number" class="control-label col-md-2"> License Plate Number </label>
                                        <div class="col-md-10">
                                            <input type="text" name="license_plate_number" id="license_plate_number" class="form-control" required
                                                   v-model="vehicleDetails.license_plate_number" placeholder="Use format KBC 123A">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="primary_responsibility" class="control-label col-md-2"> Primary Purpose </label>
                                        <div class="col-md-10">
                                            <select name="primary_responsibility" id="primary_responsibility" class="form-control" required
                                                    v-model="vehicleDetails.primary_responsibility">
                                                <option v-for="(option, index) in primaryResponsibilities" :key="index" :value="option"> @{{ option }}</option>
                                            </select>
                                            {{-- <div class="invalid-feedback">
                                                Vehicle model is required.
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="vehicle_type_id" class="control-label col-md-2"> Vehicle Model </label>
                                        <div class="col-md-10">
                                            <select name="vehicle_type_id" id="vehicle_type_id" class="form-control" required
                                                    v-model="vehicleDetails.vehicle_type_id">
                                                <option v-for="type in vehicleTypes" :key="type.id" :value="type.id"> @{{ type.name }}</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Vehicle model is required.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="branch_id" class="control-label col-md-2"> Branch </label>
                                        <div class="col-md-10">
                                            <select name="branch_id" id="branch_id" class="form-control" required v-model="vehicleDetails.branch_id" >
                                                <option v-for="branch in branches" :key="branch.id" :value="branch.id"> @{{ branch.name }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="vin" class="control-label col-md-2"> VIN </label>
                                        <div class="col-md-10">
                                            <input type="text" name="vin" id="vin" class="form-control" required v-model="vehicleDetails.vin">
                                            <div class="invalid-feedback">
                                                VIN is required.
                                            </div>
                                        </div>
                                    </div>

                                   

                                 
                                    {{-- <div class="form-group">
                                        <label for="name" class="control-label col-md-2"> Vehicle Name </label>
                                        <div class="col-md-10">
                                            <input type="text" name="name" id="name" class="form-control" required v-model="vehicleDetails.name"
                                                   placeholder="ex. Mitsubishi Fuso FI">
                                            <div class="invalid-feedback">
                                                Vehicle name is required.
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="color" class="control-label col-md-2"> Color </label>
                                        <div class="col-md-10">
                                            <input type="text" name="color" id="color" class="form-control" v-model="vehicleDetails.color">
                                        </div>
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="color" class="control-label col-md-2"> Unladen Weight (T) </label>
                                        <div class="col-md-10">
                                            <input type="number" name="unladen_weight" id="unladen_weight" class="form-control"
                                                   v-model="vehicleDetails.unladen_weight">
                                        </div>
                                    </div> --}}

                                    {{-- <div class="form-group">
                                        <label for="color" class="control-label col-md-2"> Maximum Load Capacity (T) </label>
                                        <div class="col-md-10">
                                            <input type="number" name="max_load_capacity" id="max_load_capacity" class="form-control"
                                                   v-model="vehicleDetails.max_load_capacity">
                                        </div>
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="acquisition_date" class="control-label col-md-2"> Acquisition Date </label>
                                        <div class="col-md-10">
                                            <input type="date" name="acquisition_date" id="acquisition_date" class="form-control"
                                                   v-model="vehicleDetails.acquisition_date_string">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="acquisition_price" class="control-label col-md-2"> Acquisition Cost </label>
                                        <div class="col-md-10">
                                            <input type="number" name="acquisition_price" id="acquisition_price" class="form-control"
                                                   v-model="vehicleDetails.acquisition_price">
                                        </div>
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="axle_count" class="control-label col-md-2"> Axle Count </label>
                                        <div class="col-md-10">
                                            <input type="number" name="axle_count" id="axle_count" class="form-control"
                                                   v-model="vehicleDetails.axle_count">
                                        </div>
                                    </div> --}}

                                    {{-- <div class="form-group">
                                        <label for="tyre_count" class="control-label col-md-2"> Tyre Count </label>
                                        <div class="col-md-10">
                                            <input type="number" name="tyre_count" id="tyre_count" class="form-control"
                                                   v-model="vehicleDetails.tyre_count">
                                            <div class="form-text"> Including spare tyre(s)</div>
                                        </div>
                                    </div> --}}

                                    {{-- <div class="form-group">
                                        <label for="travel_expense" class="control-label col-md-2"> Travel Expense </label>
                                        <div class="col-md-10">
                                            <input type="number" name="travel_expense" id="travel_expense" class="form-control"
                                                   v-model="vehicleDetails.travel_expense">
                                        </div>
                                    </div> --}}
                                </form>

                                <div class="box-footer">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn-primary btn" @click="goToTelematics"> Next</button>
                                    </div>
                                </div>
                            </div>

                            <div id="telematics" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                                <div class="loader-overlay" id="vehicle-details-loader">
                                    <div class="custom-loader"></div>
                                    <span id="vehicle-details-loader-message" class="loading-message"></span>
                                </div>

                                <div v-if="vehicleDetails.license_plate_number">
                                    <form @submit.prevent="saveVehicleDetails" method="post" class="form-horizontal" novalidate>
                                        {{-- <p > Telematics details as at @{{ selectedVehicleDevice.timestamp }} </p>
                                        <p v-else>
                                            <span v-if="!loadingTelematics">This vehicle does not have a telematics device attached.</span>
                                        </p> --}}

                                        <div class="form-group" >
                                            <label for="device_name" class="control-label col-md-2"> Device Name </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="device_name" id="device_name"
                                                       v-model="vehicleDetails.device_name">
                                            </div>
                                        </div>

                                        <div class="form-group" >
                                            <label for="onboarding_fuel" class="control-label col-md-2"> System Fuel level (L) </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="onboarding_fuel" id="onboarding_fuel"
                                                       v-model="vehicleDetails.onboarding_fuel">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fuel_tank_capacity" class="control-label col-md-2"> Fuel Tank Capacity (L) </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="fuel_tank_capacity" id="fuel_tank_capacity"
                                                       v-model="vehicleDetails.fuel_tank_capacity">
                                            </div>
                                        </div>

                                        <div class="form-group" >
                                            <label for="system_mileage" class="control-label col-md-2"> System Mileage (Km) </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="system_mileage" id="system_mileage"
                                                       v-model="vehicleDetails.system_mileage" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="onboarding_mileage" class="control-label col-md-2"> Current Mileage (Km) </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="onboarding_mileage" id="onboarding_mileage"
                                                       v-model="vehicleDetails.onboarding_mileage">
                                            </div>
                                        </div>

                                        <div class="form-group" >
                                            <label for="sim_card_number" class="control-label col-md-2"> Device SIM Card Number </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="sim_card_number" id="sim_card_number"
                                                       v-model="vehicleDetails.sim_card_number">
                                                <div class="form-text"> To aid in vehicle immobilization.</div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <p v-else> Please fill vehicle license plate number to continue </p>

                                <div class="box-footer">
                                    <div class="box-header-flex">
                                        <button class="btn-primary btn" @click="proceedToStep($event, 0)"> Back</button>
                                        <button class="btn-primary btn" @click="saveVehicleDetails"> Save Changes</button>
                                    </div>
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
    <link href="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .tab-title {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .tab-content {
            min-height: 600px !important;
        }

        #create-vehicle-page .control-label {
            text-align: left !important;
        }

        #create-vehicle-page .tab-content {
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
                    branches: [],
                    vehicleTypes: [],
                    vehicleDetails: {
                    },
                    selectedVehicleDevice: null,
                    loadingTelematics: false,
                    primaryResponsibilities:['Route Deliveries', 'Carton Truck', 'Prime Mover'],

                }
            },

            created() {
                this.fetchBranches()
                this.fetchVehicleTypes()
            },

            mounted() {
                $('#edit-vehicle-wizard').smartWizard({
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

                this.vehicleDetails = this.vehicle
                this.vehicleDetails.vehicle_type_id = this.vehicle.vehicle_model_id;


                $("#branch_id").select2();
                $("#vehicle_type_id").select2();
                $("#primary_responsibility").select2();
                $("#primary_responsibility").select2().val(this.vehicleDetails.primary_responsibility).trigger('change');


                $("#vehicle_type_id").change(() => {
                    let vehicleTypeId = parseInt($("#vehicle_type_id").val());
                    this.vehicleDetails.vehicle_type_id = vehicleTypeId
                });
                $("#primary_responsibility").change(() => {
                    this.vehicleDetails.primary_responsibility = $("#primary_responsibility").val();
                });
                $("#branch_id").change(() => {
                    this.vehicleDetails.branch_id = $("#branch_id").val();
                });
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
                fetchBranches() {
                    axios.get('/api/branches').then(res => {
                        this.branches = res.data.data
                        this.fillBranch()
                    }).catch(() => {
                    })
                },

                fetchVehicleTypes() {
                    axios.get('/api/vehicle-models').then(res => {
                        this.vehicleTypes = res.data.data
                    }).catch(() => {
                    })
                },

                fillBranch() {
                    this.vehicleDetails.branch_id = this.currentUser.restaurant_id
                    $("#branch_id").val(this.vehicleDetails.branch_id);

                },

                proceedToStep(e, step) {
                    e?.preventDefault();
                    $('#edit-vehicle-wizard').smartWizard("goToStep", step, true);
                },

                goToTelematics(e) {
                    e?.preventDefault();

                    $("#license_plate_number").removeClass('is-invalid');
                    if (!this.vehicleDetails.license_plate_number) {
                        $("#license_plate_number").addClass('is-invalid');
                        return this.toaster.errorMessage('Enter vehicle license plate number to continue')
                    }

                    this.getVehicleDevice()
                    this.proceedToStep(null, 1)
                },

                saveVehicleDetails(e) {
                    e.preventDefault();

                    if (this.addedVehicle) {
                        return this.proceedToStep(null, 2)
                    }

                    if (!this.vehicleDetails.license_plate_number) {
                        return this.toaster.errorMessage('Vehicle license plate is required')
                    }

                    if (!this.vehicleDetails.vin) {
                        return this.toaster.errorMessage('Vehicle vin is required')
                    }
                    if (!this.vehicleDetails.vehicle_type_id) {
                        return this.toaster.errorMessage('Vehicle model is required')
                    }

                    // if (!this.vehicleDetails.name) {
                    //     return this.toaster.errorMessage('Vehicle name is required')
                    // }

                    // if (!this.vehicleDetails.onboarding_mileage) {
                    //     return this.toaster.errorMessage('Current odometer reading is required')
                    // }

                    $("#vehicle-details-loader-message").text('');
                    $("#vehicle-details-loader").css('display', 'flex');

                    axios.post('/api/vehicles/update', this.vehicleDetails).then(res => {
                        $("#vehicle-details-loader").css('display', 'none');
                        this.toaster.successMessage('Vehicle updated successfully')
                        
                        window.location.assign('/admin/vehicles')
                    }).catch(error => {
                        $("#vehicle-details-loader").css('display', 'none');
                        this.toaster.errorMessage(error.response?.data?.message)
                    })
                },

                getVehicleDevice() {
                    $("#vehicle-details-loader-message").text('Checking telematics information');
                    $("#vehicle-details-loader").css('display', 'flex');
                    this.loadingTelematics = true
                    axios.get('https://telematics.bizwizrp.com/api/devices/find-for-vehicle', {
                        // axios.get('http://telematics.test/api/devices/find-for-vehicle', {
                        params: {
                            vehicle: this.vehicleDetails.license_plate_number
                        }
                    }).then(res => {
                        $("#vehicle-details-loader").css('display', 'none');
                        this.loadingTelematics = false
                        this.selectedVehicleDevice = res.data.data
                        if (!this.selectedVehicleDevice) {
                            this.toaster.warningMessage('The added vehicle does not have a telematics device attached.')
                        } else {
                            this.vehicleDetails.device_name = this.selectedVehicleDevice.name
                            this.vehicleDetails.onboarding_fuel = this.selectedVehicleDevice.fuel_level
                            this.vehicleDetails.system_mileage = this.selectedVehicleDevice.mileage
                        }
                    }).catch(() => {
                        this.loadingTelematics = false
                        $("#vehicle-details-loader").css('display', 'none');
                        // this.toaster.errorMessage('An error occurred while loading telematics information')
                    })
                },
            },
        })

        app.mount('#edit-vehicle-page')
    </script>
@endsection