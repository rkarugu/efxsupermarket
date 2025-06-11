@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
            <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Delivery Schedules </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'delivery-schedules.index', 'method' => 'get']) !!}
                <div class="row">
                    @if ($logged_user_info->role_id == 1 ||  $logged_user_info->role_id == 147)

                        <div class="col-md-3 form-group">
                            <select name="branch" id="branch" class="mlselect form-control">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ $branch->id == request()->branch ? 'selected' : '' }}>{{$branch->name}}</option>

                                @endforeach
                            </select>

                        </div>
                    @endif
                    <div class="col-md-3 form-group">
                        <select name="route" id="route" class="mlselect form-control">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ $route->id == request()->route ? 'selected' : '' }}>{{$route->route_name}}</option>

                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="from" id="from" class="form-control" value="{{ request()->from ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="to" id="to" class="form-control" value="{{ request()->to ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <a class="btn btn-success ml-12" href="{!! route('delivery-schedules.index') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Delivery Date</th>
                            <th>Shift Date</th>
                            <th>Delivery No.</th>
                            <th>Route</th>
                            <th>Tonnage</th>
                            <th>Status</th>
                            <th>Delivery man</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($schedules as $data)
                            <tr>

                                <td>{{   $loop->index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->expected_delivery_date)->format('Y-m-d')}}</td>
                                <td>{{ \Carbon\Carbon::parse($data->shift_created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->delivery_number }}</td>
                                <td>{{ $data->route_name }}</td>
                                <td>{{ number_format($data->shift_tonnage,  2) }}</td>
                                <td>{{ $data->delivery_status }}</td>
                                <td>{{ $data->name }} {{ $data->license_plate_number ? "($data->license_plate_number)" : '' }}</td>
                                <td>
                                    <div class="action-button-div">


                                        <a href="{{route('delivery-schedules.show', $data->schedule_id)}}" title="View Details">
                                            <i class="fa fa-eye text-primary fa-lg"></i>
                                        </a>
                                        <!--<a href="{{route('route.split-schedules', $data->schedule_id)}}" title="View Details">
                                            <i class="fa fa-columns text-primary fa-lg"></i>
                                        </a>-->

                                        @if ((!$data->vehicle_id))
                                            @if (($logged_user_info->role_id == 1 || isset($my_permissions['delivery-schedule___assign-vehicles'])) )
                                                <button class="assign-vehicle-btn" id="showVehicles" data-schedule-id="{{ $data->schedule_id }}" title="Assign Vehicle"
                                                    style="background: transparent; border:none; ">

                                                <i class="fa fa-truck text-primary fa-lg"></i>
                                                </button>
                                            @endif
                                        @endif

                                        @if (in_array($data->delivery_status, ['consolidating', 'consolidated']) && $data->vehicle_id)
                                            @if (($logged_user_info->role_id == 1 || isset($my_permissions['delivery-schedule___assign-vehicles'])) )
                                                <a href="{{ route('delivery-schedules.unassignvehicles', $data->schedule_id)}}" title="unassign vehicle">
                                                    <i class="fa fa-truck text-danger fa-lg"></i>
                                                </a>
                                            @endif
                                        @endif

                                        @if ($data->delivery_status === 'loaded')
                                            @if (($logged_user_info->role_id == 1 || isset($my_permissions['delivery-schedule___issue-gate-pass'])) && ($data->gate_pass_status == 'pending'))
                                                <a href="#" data-schedule-id="{{ $data->schedule_id }}" title="Create Gate Pass" class="initiate-gate-pass">
                                                    <i class="fa fa-ticket fa-lg text-success"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if (($user->role_id == 1 || isset($my_permissions['delivery-schedule___end-schedule'])) && $data->delivery_status != 'finished')
                                            <a href="#" data-schedule-id="{{ $data->schedule_id }}" title="End Schedule" class="end-schedule">
                                                <i class="fas fa-hourglass-end"></i>                                            </a>
                                        @endif

                                    </div>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- vehicle selection --}}
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
                            <select name="selected_vehicle" id="selected_vehicle"
                                    class="form-control mlselect">
                                <option value="" selected disabled> Select vehicle</option>
                                @foreach ($vehicles as $vehicle)
                                    @if ($vehicle->isAvailable  == 1)
                                        <option value="{{ $vehicle->id }}">
                                            {{$vehicle->name }} {{ $vehicle->license_plate_number }} ( {{ $vehicle->driver->name }} )
                                        </option>

                                    @endif
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="assign-btn" class="btn btn-primary">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="initiateGatePassModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Initiate Gate Pass</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        Are you sure you want to create a gate pass for this delivery? This will act as an acknowledgement that you have confirmed that everything has been loaded correctly.
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <form action="{{ route('delivery-schedules.create-gate-pass') }}" method="post">
                                {{ @csrf_field() }}

                                <input type="hidden" name="delivery_id" id="gatepass_delivery_id">

                                <input type="submit" value="Yes, confirm" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="endSchedule" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> End Delivery Schedule</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        Are you sure you want to end this delivery? 
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <form action="{{ route('delivery-schedules.end-schedule') }}" method="post">
                                {{ @csrf_field() }}

                                <input type="hidden" name="delivery_id" id="end-schedule-id">

                                <input type="submit" value="Yes, confirm" class="btn btn-primary">
                            </form>
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

    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
                function promptVehicleAssignment(scheduleId) {
                    console.log("Prompting vehicle assignment for schedule ID: " + scheduleId);
                    $('#vehicle-assignment-modal').data('schedule-id', scheduleId);
                    $('#selected_vehicle').val('').trigger('change');
                    $('#vehicle-assignment-modal').modal('show');
                }

                function assignVehicle() {
                    var scheduleId = $('#vehicle-assignment-modal').data('schedule-id');
                    var vehicleId = $('#selected_vehicle').val();
                    let form = new Form();

                    $.ajax({
                        url: '/api/delivery-schedules/assign-vehicle',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  
                        },
                        data: {
                            schedule_id: scheduleId,
                            vehicle_id: vehicleId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#vehicle-assignment-modal').modal('hide');
                            form.successMessage('Vehicle assigned successfully.');
                            // alert('Vehicle assigned successfully');
                            window.location.reload();

                        },
                        error: function (xhr, status, error) {
                            form.errorMessage('An error was encountered. Please try again.')
                        }
                    });
                }

                $(document).on('click', '.assign-vehicle-btn', function () {
                    var scheduleId = $(this).data('schedule-id');
                    promptVehicleAssignment(scheduleId);
                });

                $(document).on('click', '#assign-btn', function () {
                    assignVehicle();
                });
                $('.initiate-gate-pass').on('click', function (event) {
                    event.preventDefault();
                    let scheduleId = $(this).data('schedule-id');
                    $("#gatepass_delivery_id").val(scheduleId)
                    $('#initiateGatePassModal').modal('show');
                });
                $('.end-schedule').on('click', function (event) {
                    event.preventDefault();
                    let scheduleId = $(this).data('schedule-id');
                    $("#end-schedule-id").val(scheduleId)
                    $('#endSchedule').modal('show');
                });
            }
        );

    </script>
@endsection
