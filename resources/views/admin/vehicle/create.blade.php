@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {!! $title !!} </h3>

                    <a role="button" href="{{ route("$model.index") }}" class="btn btn-outline-primary"> << Back to vehicle List </a>
                </div>
            </div>

            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    @include('message')
                </div>

                <form class="form-horizontal" role="form" method="POST" action="{{ route($model.'.store') }}"
                      enctype="multipart/form-data" id="save-vehicle-form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="name" class="control-label col-md-2">Vehicle Name</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="name" placeholder="Vehicle name" name="name"
                                   required value="{{ old('name') }}">
                            <div class="form-text">E.g. Mitsubishi Canter FH</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="license_plate" class="control-label col-md-2">License Plate</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="license_plate" placeholder="License Plate" name="license_plate"
                                   required value="{{ old('license_plate') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="branch_id" class="control-label col-md-2">Branch</label>
                        <div class="col-md-10">
                            <select name="branch_id" id="branch_id" required class="form-control">
                                <option value="" selected disabled> Select branch </option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="acquisition_date" class="control-label col-md-2">Acquisition Date</label>
                        <div class="col-md-10">
                            <input type="date" class="form-control" id="acquisition_date" placeholder="Select a date" name="acquisition_date"
                                   value="{{ old('acquisition_date') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vin_sn" class="control-label col-md-2">VIN</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="vin_sn" placeholder="VIN" name="vin_sn" value="{{ old('vin_sn') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vin_sn" class="control-label col-md-2">Maximum Load Capacity (T)</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="load_capacity" placeholder="Maximum load capacity" name="load_capacity"
                                   required value="{{ old('load_capacity') }}">
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="form-group">
                            <input type="submit" value="Save Vehicle" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $("#branch_id").select2();
    </script>
@endsection

                {{--                <div class="card tabbable">--}}
                {{--                    <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model.'.store') }}"--}}
                {{--                          enctype="multipart/form-data" id="save-vehicle-form">--}}
                {{--                        {{ csrf_field() }}--}}

                {{--                        <div class="col-md-4" style="padding:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">--}}
                {{--                            <ul class="nav nav-pills nav-stacked">--}}
                {{--                                <li class="active"><a href="#details" data-bs-target="#details" data-toggle="tab">--}}
                {{--                                        <i class="fa fa-truck"></i> Vehicle Details</a>--}}
                {{--                                </li>--}}

                {{--                                <li><a href="#telematics" data-toggle="tab"><i class="fa fa-location-arrow"></i> Telematics</a></li>--}}
                {{--                            </ul>--}}
                {{--                        </div>--}}
                {{--                        <div class="col-md-8 tab-content">--}}
                {{--                            <div class="col-sm-12 form-div  tab-pane active"--}}
                {{--                                 style="padding:0 50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"--}}
                {{--                                 id="details">--}}

                {{--                                <h3><b>Vehicle Details</b></h3>--}}

                {{--                                <div class="col-sm-12 form-div">--}}
                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="license_plate">License Plate</label>--}}
                {{--                                        <input type="text" class="form-control" id="license_plate" placeholder="License Plate" name="license_plate"--}}
                {{--                                               required value="{{ old('license_plate') }}">--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="exampleInputPassword1">Vehicle Type</label>--}}
                {{--                                        <select class="form-control m-bot15" name="type" required="true">--}}
                {{--                                            <option value="" selected disabled>Select Type</option>--}}
                {{--                                            @foreach($vehicles as $vehicle)--}}
                {{--                                                <option value="{{ $vehicle->id }}">{{ $vehicle->title }}</option>--}}
                {{--                                            @endforeach--}}
                {{--                                        </select>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="year">Year of Manufacture</label>--}}
                {{--                                        <input type="number" class="form-control" id="year" placeholder="Year" name="year">--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="make">Vehicle Make</label>--}}
                {{--                                        <select class="form-control m-bot15" name="make" required="true" id="make">--}}
                {{--                                            <option value="" selected disabled>Please Select</option>--}}
                {{--                                            @foreach($makes as $make)--}}
                {{--                                                <option value="{{ $make->id }}">{{ $make->title }}</option>--}}
                {{--                                            @endforeach--}}
                {{--                                        </select>--}}
                {{--                                        <small id="emailHelp" class="form-text text-muted">--}}
                {{--                                            e.g. Toyota, GMC, Chevrolet, etc.</small>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="model">Model</label>--}}
                {{--                                        <select class="form-control m-bot15" name="model" required="true" id="model">--}}
                {{--                                            <option value="" selected disabled>Please Select</option>--}}
                {{--                                            @foreach($vehicleModels as $vehicleModel)--}}
                {{--                                                <option value="{{ $vehicleModel->id }}">{{ $vehicleModel->title }}</option>--}}
                {{--                                            @endforeach--}}
                {{--                                        </select>--}}
                {{--                                        <small id="emailHelp" class="form-text text-muted">e.g. 4Runner, Yukon, Silverado, etc.</small>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="trim">Trim</label>--}}
                {{--                                        <input type="text" class="form-control" id="trim" name="trim">--}}
                {{--                                        <small id="emailHelp" class="form-text text-muted">e.g. SE, LE, XLE, etc.</small>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="exampleInputPassword1">Color</label>--}}
                {{--                                        <input type="text" class="form-control" id="exampleInputPassword1" name="color">--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="exampleInputPassword1">Body Type</label>--}}
                {{--                                        <select class="form-control m-bot15" name="body_type" required="true">--}}
                {{--                                            <option value="" selected disabled>Please Select</option>--}}
                {{--                                            @foreach($bodytypes as $bodytype)--}}
                {{--                                                <option value="{{ $bodytype->id }}">{{ $bodytype->title }}</option>--}}
                {{--                                            @endforeach--}}
                {{--                                        </select>--}}
                {{--                                        <small id="emailHelp" class="form-text text-muted">--}}
                {{--                                            e.g. Convertible, Coupe, Pickup, Sedan, etc.</small>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="exampleInputPassword1">MSRP</label>--}}
                {{--                                        <input type="text" class="form-control" id="exampleInputPassword1" name="msrp">--}}
                {{--                                    </div>--}}
                {{--                                </div>--}}
                {{--                            </div>--}}

                {{--                            <div class="col-sm-12 form-div tab-pane"--}}
                {{--                                 style="padding:0 50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"--}}
                {{--                                 id="telematics">--}}

                {{--                                <h3><b>Telematics</b></h3>--}}

                {{--                                <div class="col-sm-12 form-div">--}}
                {{--                                    <div class="form-group">--}}
                {{--                                        <label for="device">Choose a Device</label>--}}
                {{--                                        <select class="form-control m-bot15" name="device_id" required v-model="selectedDeviceId">--}}
                {{--                                            <option value="" selected disabled>Please Select</option>--}}
                {{--                                            <option v-for="device in telematicsDevices" :key="device.id" :value="device.id">--}}
                {{--                                                @{{ device.name }}--}}
                {{--                                            </option>--}}
                {{--                                        </select>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group" v-if="selectedDeviceId">--}}
                {{--                                        <label for="fuel" class="control-label"> Current Fuel Level </label>--}}
                {{--                                        <input type="text" class="form-control" name="initial_fuel_level" id="fuel" readonly required :value="getFuelLevel">--}}
                {{--                                    </div>--}}

                {{--                                    <div class="form-group" v-if="selectedDeviceId">--}}
                {{--                                        <label for="odometer" class="control-label"> Current Odometer Reading </label>--}}
                {{--                                        <input type="text" class="form-control" name="initial_odometer_reading" id="odometer" required>--}}
                {{--                                    </div>--}}
                {{--                                </div>--}}
                {{--                            </div>--}}

                {{--                            <div class="btn-block">--}}
                {{--                                <div class="btn-group">--}}
                {{--                                    <br>--}}
                {{--                                    <button type="submit" class="btn btn-primary">Save Vehicle</button>--}}
                {{--                                </div>--}}
                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    </form>--}}
                {{--                </div>--}}

    {{--    <style>--}}


    {{--        .same-btn {--}}
    {{--            margin-right: 10px !important;--}}
    {{--            border-radius: 3px !important;--}}
    {{--            border: 1px solid #c7c7c7;--}}
    {{--            color: #000;--}}
    {{--        }--}}

    {{--        .btn-block {--}}
    {{--            display: flex;--}}
    {{--            justify-content: end;--}}
    {{--        }--}}

    {{--        .main-box-ul {--}}
    {{--            border-radius: 4px;--}}
    {{--            background-color: #c7c7c7;--}}
    {{--            padding: 10px;--}}
    {{--            background-color: #fff;--}}
    {{--            box-shadow: 0 1px 3px rgba(0, 0, 0, 10%)--}}
    {{--        }--}}

    {{--        .form-div .same-form {--}}
    {{--            background-color: #fff !important;--}}
    {{--            box-shadow: 0 5px 10px rgba(0, 0, 0, 10%) !important;--}}
    {{--            padding: 10px 12px !important;--}}
    {{--            margin: 10px 0;--}}
    {{--        }--}}

    {{--        .btn-group .green-btn {--}}
    {{--            background-color: #44ace9 !important;--}}
    {{--        }--}}
    {{--    </style>--}}

{{--@section('uniquepagescript')--}}
{{--    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>--}}
{{--    <script type="module">--}}
{{--        import {createApp} from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'--}}

{{--        const app = createApp({--}}
{{--            data() {--}}
{{--                return {--}}
{{--                    telematicsDevices: [],--}}
{{--                    selectedDeviceId: null,--}}
{{--                }--}}
{{--            },--}}

{{--            computed: {--}}
{{--                getFuelLevel() {--}}
{{--                    if (this.selectedDeviceId) {--}}
{{--                        let device = this.telematicsDevices.find(_device => _device.id === this.selectedDeviceId)--}}
{{--                        if (device) {--}}
{{--                            return device.sensors[0].val--}}
{{--                        }--}}
{{--                    }--}}

{{--                    return 0--}}
{{--                },--}}
{{--            },--}}

{{--            created() {--}}
{{--                this.fetchTelematicsDevices()--}}
{{--            },--}}

{{--            methods: {--}}
{{--                fetchTelematicsDevices() {--}}
{{--                    axios.get('/api/telematics/devices').then(response => {--}}
{{--                        this.telematicsDevices = response.data.data--}}
{{--                    }).catch(error => {--}}
{{--                        // pass for now--}}
{{--                    })--}}
{{--                },--}}

{{--                saveVehicle(e) {--}}
{{--                    e.preventDefault();--}}
{{--                    $("#save-vehicle-form").submit()--}}
{{--                }--}}
{{--            },--}}
{{--        })--}}

{{--        app.mount('#telematics')--}}
{{--    </script>--}}
{{--@endsection--}}


{{--<li><a href="#maintenance" data-bs-target="#v-pills-home1" data-toggle="tab"><i--}}
{{--                class="fa fa-wrench"></i> Maintenance</a></li>--}}
{{--<li><a href="#Lifecycle" data-toggle="tab"><i class="fa fa-life-ring"></i> Lifecycle</a></li>--}}
{{--<li><a href="#financial" data-toggle="tab"><i class="fa fa-bar-chart"></i> Financial</a></li>--}}
{{--<li><a href="#specification" data-toggle="tab"><i class="fa fa-calendar"></i> Specifications</a></li>--}}
{{--<li><a href="#settings" data-toggle="tab"><i class="fa fa-cog"></i> Setting</a></li>--}}

{{--<div class="col-sm-12 form-div tab-pane " style="padding:30px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);" id="maintenance">--}}
{{--    <h3>Checking maintenance</h3>--}}
{{--    <hr>--}}
{{--    <div class="form-group">--}}
{{--        <label class="col-sm-12">Choose a Service Program </label>--}}
{{--        <label class="col-sm-2 checkbox-inline">--}}
{{--            <input id="genMale" type="radio" name="service_programe" value="none"> None--}}
{{--        </label>--}}
{{--        <label class="col-sm-5 checkbox-inline">--}}
{{--            <input id="genFemale" type="radio" name="service_programe" value="select_service"> Choose an--}}
{{--            existing Service Program--}}
{{--        </label>--}}
{{--    </div>--}}


{{--</div>--}}

{{--<div class="col-sm-12 form-div tab-pane "--}}
{{--     style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"--}}
{{--     id="Lifecycle">--}}
{{--    <h3>Life cycle</h3>--}}
{{--</div>--}}

{{--<div class="col-sm-12 form-div tab-pane "--}}
{{--     style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"--}}
{{--     id="financial">--}}
{{--    <h3>Financial</h3>--}}
{{--</div>--}}

{{--<div class="col-sm-12 form-div tab-pane "--}}
{{--     style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"--}}
{{--     id="specification">--}}
{{--    <h3>Specification</h3>--}}
{{--</div>--}}

{{--<div class="col-sm-12 form-div tab-pane "--}}
{{--     style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"--}}
{{--     id="settings">--}}
{{--    <h3>Settings</h3>--}}
{{--</div>--}}


{{--                                    <div class="form-group">--}}
{{--                                        <label for="exampleInputEmail1">Vehicle Name</label>--}}
{{--                                        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"--}}
{{--                                               placeholder="Vehicle Name" name="vehicle_name" required>--}}
{{--                                    </div>--}}

{{--                                    <div class="form-group">--}}
{{--                                        <label for="exampleInputEmail1">Acquisition Date</label>--}}
{{--                                        <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"--}}
{{--                                               placeholder="Vehicle Name" name="acquisition_date" required>--}}
{{--                                        <small id="emailHelp" class="form-text text-muted">--}}
{{--                                            Enter a nickname to distinguish this Acquisition Date in Fleetio.</small>--}}
{{--                                    </div>--}}

{{--                                    <div class="form-group">--}}
{{--                                        <label for="exampleInputEmail1">VIN/SN</label>--}}
{{--                                        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"--}}
{{--                                               placeholder="VIN/SN" required name="vin_sn">--}}
{{--                                        <small id="emailHelp" class="form-text text-muted">--}}
{{--                                            Vehicle Identification Number or Serial Number.</small>--}}
{{--                                    </div>--}}

{{--<div class="form-group">--}}
{{--    <label for="exampleInputPassword1">Registration State/Province</label>--}}
{{--    <input type="text" class="form-control" id="exampleInputPassword1"--}}
{{--           name="registration_state_provine">--}}
{{--</div>--}}

{{--<div class="form-group">--}}
{{--    <label for="exampleInputPassword1">Photo</label>--}}
{{--    <input type="file" class="form-control" id="exampleInputPassword1" name="photo">--}}
{{--</div>--}}


{{--<div style="margin-top:30px;">&nbsp;</div>--}}
{{--<h3><b>Classification</b></h3>--}}
{{--<div class="col-sm-12 form-div">--}}
{{--    <div class="form-group">--}}
{{--        <label for="exampleInputPassword1">Status</label>--}}
{{--        <select class="form-control m-bot15" name="status" required="true">--}}
{{--            <option value="" selected disabled>Please Select</option>--}}
{{--            <option value="active">Active</option>--}}
{{--            <option value="inactive">Inactive</option>--}}
{{--        </select>--}}

{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label for="exampleInputPassword1">Group</label>--}}
{{--        <select class="form-control m-bot15" name="group" required="true">--}}
{{--            <option value="" selected disabled>Please Select</option>--}}
{{--            <option value="active">active</option>--}}
{{--            <option value="inactive">Inactive</option>--}}


{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label for="exampleInputPassword1">Operator</label>--}}
{{--        <select class="form-control m-bot15" name="operator" required="true">--}}
{{--            <option value="" selected disabled>Please Select</option>--}}
{{--            <option value="active">active</option>--}}
{{--            <option value="inactive">Inactive</option>--}}

{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label for="exampleInputPassword1">Ownership</label>--}}
{{--        <select class="form-control m-bot15" name="ownership" required="true">--}}
{{--            <option value="" selected disabled>Please Select</option>--}}
{{--            <option value="active">active</option>--}}
{{--            <option value="inactive">Inactive</option>--}}


{{--        </select>--}}
{{--    </div>--}}
{{--</div>--}}


{{--<div style="margin-top:30px;">&nbsp;</div>--}}
{{--<h3><b>Additional Details</b></h3>--}}
{{--<div class="col-sm-12 form-div">--}}

{{--    <div class="form-group">--}}
{{--        <label for="exampleInputPassword1">Linked Vehicles</label>--}}
{{--        <select class="form-control m-bot15" name="linked_devices" required="true">--}}
{{--            <option value="" selected disabled>Please Select</option>--}}
{{--            <option value="active">active</option>--}}
{{--            <option value="inactive">Inactive</option>--}}
{{--        </select>--}}
{{--    </div>--}}
{{--</div>--}}
