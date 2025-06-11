@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
    </script>

    <section class="content" id="create-vehicle-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Add Vehicles </h3>
                    <a href="{{ route("$base_route.index") }}" class="btn btn-primary"> Back to My Fleet </a>
                </div>

                <div class="box-body">
                    <!-- SmartWizard html -->
                    <div id="add-vehicle-wizard">
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

                            {{-- <li class="nav-item">
                                <a class="nav-link" href="#service-details">
                                    <span class="num">3</span>
                                    Servicing Details
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#insurance-details">
                                    <span class="num">4</span>
                                    Insurance Details
                                </a>
                            </li> --}}

                            <li class="nav-item">
                                <a class="nav-link" href="#driver-assignment">
                                    <span class="num">3</span>
                                    Assignments
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="vehicle-details" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                <h3 class="tab-title"> Vehicle Details </h3>

                                <form method="post" @submit.prevent="goToTelematics" id="vehicle-details-form" class="form-horizontal">
                                    <div class="form-group">
                                        <label for="license_plate_number" class="control-label col-md-2"> License Plate Number </label>
                                        <div class="col-md-10">
                                            <input type="text" name="license_plate_number" id="license_plate_number" class="form-control" required
                                                   v-model="vehicleDetails.license_plate_number" placeholder="Use format KBC 123A">
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
                                        <label for="primary_responsibility" class="control-label col-md-2"> Primary Purpose </label>
                                        <div class="col-md-10">
                                            <select name="primary_responsibility" id="primary_responsibility" class="form-control" required
                                                    v-model="vehicleDetails.primary_responsibility" >
                                                <option v-for="(option, index) in primaryResponsibilities" :key="index" :value="option"> @{{ option }}</option>
                                            </select>
                                            {{-- <div class="invalid-feedback">
                                                Vehicle model is required.
                                            </div> --}}
                                        </div>
                                    </div>

                                 

                                    <div class="form-group">
                                        <label for="branch_id" class="control-label col-md-2"> Branch </label>
                                        <div class="col-md-10">
                                            <select name="branch_id" id="branch_id" class="form-control" required v-model="vehicleDetails.branch_id">
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
                                                   v-model="vehicleDetails.acquisition_date">
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
                                <h3 class="tab-title"> Telematics Details </h3>

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
                                                       v-model="vehicleDetails.device_name" >
                                            </div>
                                        </div>

                                        <div class="form-group" >
                                            <label for="onboarding_fuel" class="control-label col-md-2"> System Fuel level (L) </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="onboarding_fuel" id="onboarding_fuel"
                                                       v-model="vehicleDetails.onboarding_fuel" >
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
                                                       v-model="vehicleDetails.system_mileage" >
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
                                        <button class="btn-primary btn" @click="proceedToStep($event, 4)" v-if="addedVehicle"> Next</button>
                                        <button class="btn-primary btn" @click="saveVehicleDetails" v-else> Add Vehicle</button>
                                    </div>
                                </div>
                            </div>

                            {{-- <div id="service-details" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                                <h3 class="tab-title"> Service Intervals </h3>
                                <div class="loader-overlay" id="service-details-loader">
                                    <div class="custom-loader"></div>
                                    <span id="service-details-loader-message" class="loading-message"></span>
                                </div>

                                <form method="post" @submit.prevent="saveServiceDetails" novalidate class="form-horizontal">
                                    <div v-for="(interval, index) in serviceIntervals" :key="index"
                                         style="border-bottom: 1px solid rgba(0, 0, 0, .125); padding: 12px 0;">
                                        <div class="form-group">
                                            <label class="control-label col-md-2"> Service Type </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" v-model="interval.name" placeholder="ex. General Service">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-2"> Mileage </label>
                                            <div class="col-md-10">
                                                <input type="number" class="form-control" v-model="interval.mileage" min="0">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end" v-if="serviceIntervals.length > 1">
                                            <button role="button" @click="removeServiceInterval($event, index)">
                                                <i class="fa fa-trash text-danger fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end" style="margin: 12px 0;">
                                        <button class="btn-outline-primary btn" @click="addServiceInterval($event)"> + Add Interval</button>
                                    </div>

                                    <div class="mt-2" v-if="requireLastMileageDate && lastServiceInterval">
                                        <h3 class="tab-title"> Last Service </h3>

                                        <div class="form-group">
                                            <label class="control-label col-md-2">
                                                Service Type
                                            </label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" :value="lastServiceInterval.name" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-2">
                                                Mileage
                                            </label>
                                            <div class="col-md-10">
                                                <input type="number" class="form-control" :value="lastServiceInterval.mileage" id="last-service-mileage">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-2">
                                                Date
                                            </label>
                                            <div class="col-md-10">
                                                <input type="date" class="form-control" v-model="lastService.date">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="box-footer">
                                    <div class="box-header-flex">
                                        <button class="btn-primary btn" @click="proceedToStep($event, 2)"> Back</button>
                                        <button class="btn-primary btn" @click="proceedToStep($event, 3)" v-if="serviceDetailsSaved"> Next</button>
                                        <button class="btn-primary btn" @click="saveServiceDetails" v-else> Save</button>
                                    </div>
                                </div>
                            </div> --}}
{{-- 
                            <div id="insurance-details" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
                                <h3 class="tab-title"> Insurance Details </h3>
                                <div class="loader-overlay" id="insurance-details-loader">
                                    <div class="custom-loader"></div>
                                    <span id="insurance-details-loader-message" class="loading-message"></span>
                                </div>

                                <form method="post" class="form-horizontal" @submit.prevent="saveInsuranceDetails" novalidate>
                                    <div class="form-group">
                                        <label for="insurer" class="control-label col-md-2">Insurer</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" v-model="insuranceDetails.insurer" id="insurer">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="insurance_type" class="control-label col-md-2">Insurance Type</label>
                                        <div class="col-md-10">
                                            <select name="type" id="insurance_type" class="form-control" v-model="insuranceDetails.type">
                                                <option v-for="(option, index) in insuranceTypes" :key="index" :value="option"> @{{ option }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="insurance_amount" class="control-label col-md-2">Cost</label>
                                        <div class="col-md-10">
                                            <input type="number" class="form-control" v-model="insuranceDetails.insurance_amount" id="insurance_amount" min="0">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="insurance_date" class="control-label col-md-2">Insurance Date</label>
                                        <div class="col-md-10">
                                            <input type="date" class="form-control" v-model="insuranceDetails.insurance_date" id="insurance_date">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="insurance_period" class="control-label col-md-2">Period (Months)</label>
                                        <div class="col-md-10">
                                            <input type="number" class="form-control" v-model="insuranceDetails.insurance_period" id="insurance_period" min="1">
                                        </div>
                                    </div>
                                </form>

                                <div class="box-footer">
                                    <div class="box-header-flex">
                                        <button class="btn-primary btn" @click="proceedToStep($event, 3)"> Back</button>
                                        <button class="btn-primary btn" @click="proceedToStep($event, 4)" v-if="insuranceDetailsSaved"> Next</button>
                                        <button class="btn-primary btn" @click="saveInsuranceDetails" v-else> Save</button>
                                    </div>
                                </div>
                            </div> --}}

                            <div id="driver-assignment" class="tab-pane" role="tabpanel" aria-labelledby="step-5">
                                <h3 class="tab-title"> Assign Driver </h3>
                                <p> Only available drivers are displayed </p>
                                <div class="loader-overlay" id="driver-details-loader">
                                    <div class="custom-loader"></div>
                                    <span id="driver-details-loader-message" class="loading-message"></span>
                                </div>

                                <form method="post" @submit.prevent="assignDriver" class="form-horizontal" novalidate>
                                    <div class="form-group">
                                        <label for="driver_id" class="control-label col-md-2"> Select Driver </label>
                                        <div class="col-md-10">
                                            <select name="driver_id" id="driver_id" v-model="selectedDriverId" class="form-control">
                                                <option value="" selected disabled> Select driver</option>
                                                <option v-for="driver in availableDrivers" :key="driver.id" :value="driver.id">@{{ driver.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                <div class="box-footer">
                                    <div class="box-header-flex">
                                        <button class="btn-primary btn" @click="proceedToStep($event, 2)"> Back</button>
                                        <button class="btn-primary btn" @click="assignDriver"> Finish</button>
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
                    // vehicleModels:[],
                    availableDrivers: [],
                    vehicleDetails: {},
                    selectedVehicleDevice: null,
                    loadingTelematics: false,
                    serviceIntervals: [
                        {
                            name: null,
                            mileage: 0
                        }
                    ],
                    lastService: {},
                    insuranceDetails: {},
                    addedVehicle: null,
                    serviceDetailsSaved: false,
                    insuranceDetailsSaved: false,
                    insuranceTypes: ['Third Party', 'Third Party (Fire & Theft)', 'Comprehensive'],
                    primaryResponsibilities:['Route Deliveries', 'Carton Truck', 'Prime Mover'],
                    selectedDriverId: null
                }
            },

            created() {
                this.fetchBranches()
                this.fetchVehicleTypes()
                // this.fetchVehicleModels()
                this.fetchAvailableDrivers()
            },

            mounted() {
                $('#add-vehicle-wizard').smartWizard({
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

                $("#branch_id").select2();
                $("#vehicle_type_id").select2();
                $("#insurance_type").select2();
                $("#driver_id").select2();
                $("#primary_responsibility").select2();

                $("#vehicle_type_id").change(() => {
                    let vehicleTypeId = parseInt($("#vehicle_type_id").val());
                    this.vehicleDetails.vehicle_type_id = vehicleTypeId
                });

                $("#insurance_type").change(() => {
                    this.insuranceDetails.type = $("#insurance_type").val();

                });
                $("#primary_responsibility").change(() => {
                    this.vehicleDetails.primary_responsibility = $("#primary_responsibility").val();
                });
                $("#branch_id").change(() => {
                    this.vehicleDetails.branch_id = $("#branch_id").val();
                });

                $("#driver_id").change(() => {
                    this.selectedDriverId = $("#driver_id").val();
                });
            },

            computed: {
                currentUser() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },

                sortedIntervals() {
                    let intervals = JSON.parse(JSON.stringify(this.serviceIntervals))
                    intervals.sort((a, b) => {
                        return parseFloat(a.mileage) - parseFloat(b.mileage)
                    });

                    return intervals
                },

                requireLastMileageDate() {
                    let intervals = JSON.parse(JSON.stringify(this.serviceIntervals))
                    intervals.sort((a, b) => {
                        return parseFloat(a.mileage) - parseFloat(b.mileage)
                    });

                    let currentMileage = parseFloat(this.vehicleDetails.onboarding_mileage)
                    let lastMileage = parseFloat(this.serviceIntervals[0].mileage)
                    return (lastMileage > 0) && (currentMileage > lastMileage)
                },
                isBranchRequired() {
                    return this.vehicleDetails.primary_responsibility === 'Route Deliveries';
                },

                lastServiceInterval() {
                    if (!this.requireLastMileageDate) {
                        return null
                    }

                    let intervals = this.sortedIntervals
                    let currentMileage = parseFloat(this.vehicleDetails.onboarding_mileage)
                    let preIntervals = intervals.filter(interval => {
                        return parseFloat(interval.mileage) < currentMileage
                    })

                    let lastInterval = preIntervals[(preIntervals.length - 1)]
                    let lastIntervalMileage = parseFloat(lastInterval.mileage)
                    if (currentMileage < lastIntervalMileage) {
                        return lastInterval
                    }

                    let newLastInterval = JSON.parse(JSON.stringify(lastInterval))
                    let cyclesCompleted = Math.floor(currentMileage / lastIntervalMileage)
                    for (let index in intervals) {
                        let interval = intervals[index]
                        let intervalMileage = parseFloat(interval.mileage)
                        let lastServiceMileage = (lastIntervalMileage * cyclesCompleted) + intervalMileage
                        if (lastServiceMileage < currentMileage) {
                            newLastInterval.name = interval.name
                            newLastInterval.mileage = lastServiceMileage
                        } else {
                            if (cyclesCompleted > 1) {
                                newLastInterval.mileage = lastIntervalMileage * cyclesCompleted
                            }

                            break;
                        }
                    }

                    return newLastInterval
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
                // fetchVehicleModels(){
                //     axios.get('/api/vehicle-models')then(res =>{
                //         this.vehicleModels = res.data.data
                //     }).catch(()=>{

                //     })
                // },

                fetchAvailableDrivers() {
                    axios.get('/api/vehicles/available-drivers', {
                        params: {
                            branch_id: this.currentUser.restaurant_id
                        }
                    }).then(res => {
                        this.availableDrivers = res.data.data
                    }).catch(err => {

                    })
                },

                fillBranch() {
                    this.vehicleDetails.branch_id = this.currentUser.restaurant_id
                    $("#branch_id").val(this.vehicleDetails.branch_id)
                    
                },

                proceedToStep(e, step) {
                    e?.preventDefault();
                    $('#add-vehicle-wizard').smartWizard("goToStep", step, true);
                },

                goToTelematics(e) {
                    e.preventDefault();

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

                    // if (!this.vehicleDetails.name) {
                    //     return this.toaster.errorMessage('Vehicle name is required')
                    // }

                    // if (!this.vehicleDetails.onboarding_mileage) {
                    //     return this.toaster.errorMessage('Current odometer reading is required')
                    // }

                    $("#vehicle-details-loader-message").text('');
                    $("#vehicle-details-loader").css('display', 'flex');
                    console.log(this.vehicleDetails);


                    axios.post('/api/vehicles/store', this.vehicleDetails).then(res => {  
                        $("#vehicle-details-loader").css('display', 'none');
                        this.toaster.successMessage('Vehicle added successfully')
                        this.addedVehicle = res.data.data

                        this.proceedToStep(null, 2)
                    }).catch(error => {
                        $("#vehicle-details-loader").css('display', 'none');
                        this.toaster.errorMessage(error.response?.message)
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
                            // this.toaster.warningMessage('The added vehicle does not have a telematics device attached.')
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

                saveServiceDetails(e) {
                    e.preventDefault();

                    if (this.serviceDetailsSaved) {
                        return this.proceedToStep(null, 3)
                    }

                    if (this.requireLastMileageDate) {
                        if (!this.lastService.date) {
                            return this.toaster.errorMessage('Provide last service date')
                        }
                    }

                    let payload = {
                        intervals: this.serviceIntervals,
                        vehicle_id: this.addedVehicle.id
                    }

                    if (this.requireLastMileageDate) {
                        payload.last_service = {
                            name: this.lastServiceInterval.name,
                            mileage: this.lastServiceInterval.mileage,
                            date: this.lastService.date,
                        }
                    }

                    $("#service-details-loader-message").text('Saving');
                    $("#service-details-loader").css('display', 'flex');
                    axios.post('/api/vehicles/save-service-details', {payload: JSON.stringify(payload)}).then(res => {
                        $("#service-details-loader").css('display', 'none');
                        this.toaster.successMessage('Service details saved successfully');
                        this.serviceDetailsSaved = true

                        this.proceedToStep(null, 3)
                    }).catch(error => {
                        $("#service-details-loader").css('display', 'none');
                        this.toaster.errorMessage(error.response?.message)
                    })
                },

                addServiceInterval(e) {
                    e?.preventDefault();
                    this.serviceIntervals.push({
                        name: null,
                        mileage: 0
                    })
                },

                removeServiceInterval(e, index) {
                    e?.preventDefault();
                    this.serviceIntervals.splice(index, 1)
                },

                saveInsuranceDetails(e) {
                    e.preventDefault();

                    if (this.insuranceDetailsSaved) {
                        return this.proceedToStep(null, 4)
                    }

                    if (this.requireLastMileageDate) {
                        if (!this.lastService.date) {
                            return this.toaster.errorMessage('Provide last service date')
                        }
                    }

                    this.insuranceDetails.vehicle_id = this.addedVehicle.id
                    $("#insurance-details-loader-message").text('Saving');
                    $("#insurance-details-loader").css('display', 'flex');
                    axios.post('/api/vehicles/save-insurance-details', this.insuranceDetails).then(res => {
                        $("#insurance-details-loader").css('display', 'none');
                        this.toaster.successMessage('Insurance details saved successfully');
                        this.insuranceDetailsSaved = true

                        this.proceedToStep(null, 4)
                    }).catch(error => {
                        $("#insurance-details-loader").css('display', 'none');
                        this.toaster.errorMessage(error.response?.message)
                    })
                },

                assignDriver(e) {
                    e.preventDefault();

                    if (!this.selectedDriverId) {
                        window.location.assign('/admin/vehicles')
                    }

                    axios.post('/api/vehicles/assign-driver', {
                        driver_id: this.selectedDriverId,
                        vehicle_id: this.addedVehicle.id,
                    }).then(res => {
                        this.toaster.successMessage('Driver assigned successfully.')
                        window.location.assign('/admin/vehicles')
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.message)
                    })
                }
            },
        })

        app.mount('#create-vehicle-page')
    </script>
@endsection