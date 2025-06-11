@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
    </script>

    <section class="content" id="my-fleet-page">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> My Fleet </h3>
                    <a href="{{ route("$base_route.create") }}" class="btn btn-primary"> Add Vehicle </a>
                </div>
                <hr>

                <div class="box-body">
                    <div class="row">    
                            <div class="col-md-3 form-group">
                                <label for="">Select  Branch</label>
                                <select name="branch" id="branch" class="form-control mlselect" >
                                    <option value="" selected disabled>Select branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
    
                                    @endforeach
                                </select>
                            </div>                        
                    </div>

                <hr>
                @include('message')
                    <div class="table-responsive">
                        <table class="table" id="vehicles-table">
                            <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                {{-- <th> Branch</th> --}}
                                <th> Vehicle Model</th>
                                <th> License Plate Number</th>
                                <th>Primary Purpose</th>
                                <th>Color</th>
                                <th> Acquisition Date</th>
                                <th> Telematics Device</th>
                                <th> Driver</th>
                                <th>Turn Boy</th>
                                <th>Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(vehicle, index) in vehicles" :key="index" v-cloak>
                                <th scope="row" style="width: 3%;">@{{ index + 1 }}</th>
                                {{-- <td> @{{ vehicle.name }}</td> --}}
                                <td> @{{ vehicle.model?.name ?? '-' }}</td>
                                <td> @{{ vehicle.license_plate_number }}</td>
                                <td>@{{ vehicle.primary_responsibility ?? '-'}}</td>
                                <td> @{{vehicle.color }} </td>
                                <td> @{{ vehicle.acquisition_date }}</td>
                                <td> @{{ vehicle.device_name ?? '-' }}</td>
                                <td> @{{ vehicle.driver?.name ?? '-' }}</td>
                                <td> @{{ vehicle.turnboy?.name ?? '-' }}</td>

                                <td>
                                    <div class="action-button-div">
                                        {{-- <a :href="`/admin/vehicles/${vehicle.id}`" title="View Details">
                                            <i class="fa fa-eye text-primary fa-lg"></i>
                                        </a> --}}

                                        <a :href="`/admin/vehicles/${vehicle.id}/edit`" title="Edit Vehicle">
                                            <i class="fa fa-edit text-primary fa-lg"></i>
                                        </a>

                                        {{-- <a href="javascript:void(0);" title="Assign Driver" v-if="!vehicle.driver" @click="promptAssignDriver(vehicle)">
                                            <i class="fas fa-user-tie text-primary fa-lg"></i>
                                        </a>

                                        <a href="javascript:void(0);" title="Unassign Driver" v-if="vehicle.driver" @click="promptUnAssignDriver(vehicle)">
                                            <i class="fas fa-user-slash text-danger fa-lg"></i>
                                        </a>
                                        <a href="javascript:void(0);" title="Assign Turn Boy" v-if="!vehicle.turnboy" @click="promptAssignTurnboy(vehicle)">
                                            <i class="fas fa-user-tag text-primary fa-lg"></i>
                                        </a>

                                        <a href="javascript:void(0);" title="Unassign Turn Boy" v-if="vehicle.turnboy" @click="promptUnAssignTurnboy(vehicle)">
                                            <i class="fas fa-user-times text-danger fa-lg"></i>
                                        </a>

                                        <a href="javascript:void(0);" title="Switch Off" v-if="(vehicle.switch_off_status === 'on') && (currentUser.role_id === 1)" @click="promptSwitchOffVehicle(vehicle)">
                                            <i class="fas fa-power-off text-danger fa-lg"></i>
                                        </a>

                                        <a href="javascript:void(0);" title="Switch On" v-if="(vehicle.switch_off_status === 'off') && (currentUser.role_id === 1)" @click="promptSwitchOnVehicle(vehicle)">
                                            <i class="fas fa-power-off text-success fa-lg"></i>
                                        </a> --}}
                                        
                                            <a title="Vehicle centre" :href="`/admin/vehicles/vehicle-center/${vehicle.id}`"><i
                                                        class="fa fa-store"></i></a>
                                            
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Assignment -->
        <div class="modal fade" id="driver-assignment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Assign Driver </h3>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="driver_id" class="control-label"> Select Driver </label>
                            <select name="driver_id" id="driver_id" v-model="selectedDriverId" class="form-control">
                                <option value="" selected disabled> Select driver</option>
                                <option v-for="driver in availableDrivers" :key="driver.id" :value="driver.id">@{{ driver.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="assignDriver">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Unassignment -->
        <div class="modal fade" id="driver-unassignment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Un-assign Driver </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to unassign driver @{{ activeVehicle?.driver?.name }} from @{{ activeVehicle?.license_plate_number }}?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                            <button type="button" class="btn btn-primary" @click="unAssignDriver">Yes, Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
              <!-- Turn Boy Assignment -->
              <div class="modal fade" id="turnboy-assignment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"> Assign Turn Boy </h3>
                        </div>
    
                        <div class="box-body">
                            <div class="form-group">
                                <label for="turnboy_id" class="control-label"> Select Turn Boy </label>
                                <select name="turnboy_id" id="turnboy_id" v-model="selectedTurnboyId" class="form-control">
                                    <option value="" selected disabled> Select turn boy</option>
                                    <option v-for="turnboy in availableTurnboys" :key="turnboy.id" :value="turnboy.id">@{{ turnboy.name }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="box-footer">
                            <div class="box-header-flex">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" @click="assignTurnboy">Assign</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
             <!-- Turn Boy Unassignment -->
        <div class="modal fade" id="turnboy-unassignment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Un-assign Turn boy </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to unassign turn boy @{{ activeVehicle?.turnboy?.name }} from @{{ activeVehicle?.license_plate_number }}?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                            <button type="button" class="btn btn-primary" @click="unAssignTurnboy">Yes, Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Switch Off -->
        <div class="modal fade" id="vehicle-switch-off-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content box">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Confirm Vehicle Switch Off </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to switch off @{{ activeVehicle?.license_plate_number }}?
                        <br>
                        <br>
                        <strong>Please note that this is a critical operation and assumes you've considered all safety pre-cautions.</strong>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                            <button type="button" class="btn btn-primary" @click="switchOffVehicle">Yes, Switch Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Switch On -->
        <div class="modal fade" id="vehicle-switch-on-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content box">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Confirm Vehicle Switch On </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to switch on @{{ activeVehicle?.license_plate_number }}?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                            <button type="button" class="btn btn-primary" @click="switchOnVehicle">Yes, Switch On</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
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
                    vehicles: [],
                    activeVehicle: null,
                    availableDrivers: [],
                    availableTurnboys: [],

                    selectedDriverId: null,
                    selectedTurnboyId: null,
                    selectedBranchId: null

                }
            },

            created() {
                this.fetchVehicles()
            },

            mounted() {
                $(".mlselect").select2();
                $("#branch").change(() => {
                    this.selectedBranchId = $("#branch").val(); // Store the selected branch ID
                    this.fetchVehicles(); // Fetch vehicles based on selected branch
                });

                $('#driver_id').select2();
                $("#driver_id").change(() => {
                    this.selectedDriverId = parseInt($("#driver_id").val());
                });
                $('#turnboy_id').select2();
                $("#turnboy_id").change(() => {
                    this.selectedTurnboyId = parseInt($("#turnboy_id").val());
                });
            },

            computed: {
                currentUser() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                fetchVehicles() {
                    const branchId = this.selectedBranchId || this.currentUser.restaurant_id;

                    axios.get('/api/vehicles/all', {
                        params: {
                            branch_id: branchId
                        }
                    }).then(res => {
                        this.vehicles = res.data.data
                        if (this.table) {
                            this.table.destroy();
                        }
                        setTimeout(() => {
                           this.table = $('#vehicles-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 100,
                                'initComplete': function (settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                                //"aaSorting": [ [0,'desc'] ]

                            });
                        }, 100)
                    }).catch(() => {
                    })
                },

                promptAssignDriver(vehicle) {
                    this.fetchAvailableDrivers()

                    this.activeVehicle = vehicle
                    $('#driver-assignment-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#driver-assignment-modal').modal('show');
                },
                promptAssignTurnboy(vehicle) {
                    this.fetchAvailableTurnboys()

                    this.activeVehicle = vehicle
                    $('#turnboy-assignment-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#turnboy-assignment-modal').modal('show');
                },

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

                fetchAvailableTurnboys() {
                    axios.get('/api/vehicles/available-turnboys', {
                        params: {
                            branch_id: this.currentUser.restaurant_id
                        }
                    }).then(res => {
                        this.availableTurnboys = res.data.data
                    }).catch(err => {

                    })
                },

                assignDriver() {
                    if (!this.selectedDriverId) {
                        return this.toaster.errorMessage('Please select a driver.')
                    }

                    axios.post('/api/vehicles/assign-driver', {
                        driver_id: this.selectedDriverId,
                        vehicle_id: this.activeVehicle.id,
                    }).then(res => {
                        $('#driver-assignment-modal').modal('hide');
                        this.toaster.successMessage('Driver assigned successfully.')
                        this.fetchVehicles()
                    }).catch(error => {
                        this.toaster.errorMessage('An error was encountered. Please try again.')
                    })
                },
                assignTurnboy() {
                    if (!this.selectedTurnboyId) {
                        return this.toaster.errorMessage('Please select a turn boy.')
                    }

                    axios.post('/api/vehicles/assign-turnboy', {
                        driver_id: this.selectedTurnboyId,
                        vehicle_id: this.activeVehicle.id,
                    }).then(res => {
                        $('#turnboy-assignment-modal').modal('hide');
                        this.toaster.successMessage('Turn boy assigned successfully.')
                        this.fetchVehicles()
                    }).catch(error => {
                        this.toaster.errorMessage('An error was encountered. Please try again.')
                    })
                },

                promptUnAssignDriver(vehicle) {
                    this.activeVehicle = vehicle
                    $('#driver-unassignment-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#driver-unassignment-modal').modal('show');
                },
                promptUnAssignTurnboy(vehicle) {
                    this.activeVehicle = vehicle
                    $('#turnboy-unassignment-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#turnboy-unassignment-modal').modal('show');
                },

                unAssignDriver() {
                    axios.post('/api/vehicles/unassign-driver', {
                        vehicle_id: this.activeVehicle.id,
                    }).then(res => {
                        $('#driver-unassignment-modal').modal('hide');
                        this.toaster.successMessage('Driver unassigned successfully.')
                        this.fetchVehicles()
                    }).catch(error => {
                        this.toaster.errorMessage(error.response.data?.message)
                    })
                },
                unAssignTurnboy() {
                    axios.post('/api/vehicles/unassign-turnboy', {
                        vehicle_id: this.activeVehicle.id,
                    }).then(res => {
                        $('#turnboy-unassignment-modal').modal('hide');
                        this.toaster.successMessage('Turn boy unassigned successfully.')
                        this.fetchVehicles()
                    }).catch(error => {
                        this.toaster.errorMessage(error.response.data?.message)
                    })
                },

                promptSwitchOffVehicle(vehicle) {
                    this.activeVehicle = vehicle
                    $('#vehicle-switch-off-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#vehicle-switch-off-modal').modal('show');
                },

                switchOffVehicle() {
                    axios.post('/api/vehicles/switch-off', {
                        vehicle_id: this.activeVehicle.id,
                    }).then(res => {
                        $('#vehicle-switch-off-modal').modal('hide');
                        this.toaster.successMessage('Vehicle switch off successfully.')
                        this.fetchVehicles()
                    }).catch(error => {
                        this.toaster.errorMessage(error.response.data?.message)
                    })
                },

                promptSwitchOnVehicle(vehicle) {
                    this.activeVehicle = vehicle
                    $('#vehicle-switch-on-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#vehicle-switch-on-modal').modal('show');
                },

                switchOnVehicle() {
                    axios.post('/api/vehicles/switch-on', {
                        vehicle_id: this.activeVehicle.id,
                    }).then(res => {
                        $('#vehicle-switch-on-modal').modal('hide');
                        this.toaster.successMessage('Vehicle switch on successfully.')
                        this.fetchVehicles()
                    }).catch(error => {
                        this.toaster.errorMessage(error.response.data?.message)
                    })
                },
            },
        })

        app.mount('#my-fleet-page')
    </script>
@endsection