@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp
    <script>
        window.user = {!! $user !!}
    </script>

    <section class="content" id="delivery-schedules">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Delivery Schedules </h3>
                <div style="height: 150px ! important;">

                    <br>

                    <form>

                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="date" class="datepicker form-control" v-model="start_date">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="date" class="datepicker form-control" v-model="end_date">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-1">
                                <button @click="filterSchedules" class="btn btn-success" type="button">Filter</button>
                            </div>

                            <div class="col-sm-1">
                                <a class="btn btn-info" href="{{ route("$base_route.index") }}">Clear </a>

                            </div>

                        </div>

                    </form>
                </div>
            </div>


            <div class="box-body">
                <div class="session-message-container"></div>


                <div class="table-responsive">
                    <table class="table" id="delivery-schedules-table">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th> Delivery Number</th>
                            <th> Route</th>
                            <th> Shift</th>
                            <th> Status</th>
                            <th> Delivery Man</th>
                            <th> Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(schedule, index) in schedules" :key="schedule.id" v-cloak>
                            <th scope="row" style="width: 3%;">@{{ index + 1 }}</th>
                            <td>@{{ schedule.delivery_number }}</td>
                            <td>@{{ schedule.route?.route_name }}</td>
                            <td>@{{ schedule.shift?.shift_id }}</td>
                            <td>@{{ schedule.display_status }}</td>
                            <td> @{{ schedule.delivery_man }}</td>
                            <td>
                                <div class="action-button-div">
                                    <a :href="`/admin/delivery-schedules/${schedule.id}`" title="View Details">
                                        <i class="fa fa-eye text-primary fa-lg"></i>
                                    </a>

                                    <a href="javascript:void(0);" @click="promptVehicleAssignment(schedule)"
                                       v-if="(schedule.status === 'consolidated') && (!schedule.vehicle_id)"
                                       title="Assign Vehicle">
                                        <i class="fa fa-truck text-primary fa-lg"></i>
                                    </a>

                                    <a :href="getDownloadPdfLink(schedule.id)"
                                       title="Delivery Note" v-if="schedule.status === 'consolidated'">
                                        <i class="fa fa-file-pdf text-danger fa-lg"></i>
                                    </a>

                                    <a :href="`/admin/delivery-schedules/${schedule.id}/delivery-report`" title="Delivery Report" v-if="schedule.status === 'finished'">
                                        <i class="fa fa-file-pdf text-primary fa-lg"></i>
                                    </a>

                                    <a :href="`/admin/gross-profit/route-profitibility-report?manage=pdf&schedule_id=${schedule.id}&shift_id=${schedule.shift_id}`"
                                       title="Profitability Report" v-if="schedule.status === 'finished'">
                                        <i class="fa fa-file-pdf text-success fa-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vehicle Assignment -->
        <div class="modal fade" id="vehicle-assignment-modal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Assign Vehicle </h3>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="vehicles" class="control-label"> Select Vehicle </label>
                            <select name="selected_vehicle" id="selected_vehicle" v-model="selectedVehicle"
                                    class="form-control">
                                <option value="" selected disabled> Select vehicle</option>
                                <option v-for="vehicle in availableVehicles" :key="vehicle.id" :value="vehicle.id">@{{
                                    vehicle.name }} @{{ vehicle.license_plate_number }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="assignVehicle">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
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
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="module">
        import {createApp} from 'vue';

        const app = createApp({
            data() {
                const today = new Date();
                const formattedToday = today.toISOString().split('T')[0];
                return {
                    schedules: [],
                    activeSchedule: null,
                    availableVehicles: [],
                    selectedVehicle: null,
                    start_date: null,
                    end_date: null,
                }
            },

            created() {
                this.fetchDeliverySchedules()
            },

            mounted() {

                $("#selected_vehicle").change(() => {
                    let vehicleId = parseInt($("#selected_vehicle").val());
                    this.selectedVehicle = vehicleId
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
                // async initMap() {
                //     const {Map} = await google.maps.importLibrary("maps");
                //     await google.maps.importLibrary("geometry");
                //
                //     this.map = new Map(document.getElementById("routes-map"), {
                //         center: {lat: -1.28333, lng: 36.81667},
                //         zoom: 8,
                //         mapId: "355bd45b5b2fb544",
                //     });
                // },

                getDownloadPdfLink(scheduleId) {
                    return `/admin/delivery-schedules-pdf/${scheduleId}`;
                },
                filterSchedules() {
                    axios.get('/api/delivery-schedules/filter-active', {
                        params: {
                            user_role_id: this.currentUser.role_id,
                            user_restaurant_id: this.currentUser.restaurant_id,
                            start_date: this.start_date,
                            end_date: this.end_date,
                        }
                    }).then(res => {
                        this.schedules = res.data.data;


                    });
                },

                fetchDeliverySchedules() {
                    axios.get('/api/delivery-schedules/active', {
                        params: {
                            user_role_id: this.currentUser.role_id,
                            user_restaurant_id: this.currentUser.restaurant_id,

                        }
                    }).then(res => {
                        this.schedules = res.data.data
                        setTimeout(() => {
                            if (this.table) {
                                this.table.destroy();
                            }

                            this.table = $('#delivery-schedules-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function (settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                    if (total_record < 11) {
                                        $('.dataTables_paginate').hide();
                                    }
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                                //"aaSorting": [ [0,'desc'] ]

                            });
                        }, 50)
                    }).catch(error => {
                        console.log(error)
                    })
                },

                promptVehicleAssignment(schedule) {
                    this.activeSchedule = schedule

                    this.fetchAvailableVehicles()
                    $("#selected_vehicle").select2()

                    $('#vehicle-assignment-modal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    $('#vehicle-assignment-modal').modal('show');
                },

                fetchAvailableVehicles() {
                    axios.get('/api/vehicles/available', {
                        params: {
                            user_role_id: this.currentUser.role_id,
                            user_restaurant_id: this.currentUser.restaurant_id
                        }
                    }).then(res => {
                        this.availableVehicles = res.data.data
                    }).catch(error => {
                    })
                },

                assignVehicle() {
                    if (!this.selectedVehicle) {
                        return this.toaster.errorMessage('Please select a vehicle.')
                    }

                    axios.post('/api/delivery-schedules/assign-vehicle', {
                        schedule_id: this.activeSchedule.id,
                        vehicle_id: this.selectedVehicle,
                    }).then(res => {
                        $('#vehicle-assignment-modal').modal('hide');
                        this.toaster.successMessage('Vehicle assigned successfully.')
                        this.fetchDeliverySchedules()
                    }).catch(error => {
                        this.toaster.errorMessage('An error was encountered. Please try again.')
                    })
                },
            },
        })

        app.mount('#delivery-schedules')
    </script>
@endsection
