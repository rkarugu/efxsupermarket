@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Attach Device </h3>

                    <a role="button" href="{{ route("vehicle.index") }}" class="btn btn-outline-primary"> << Back to Vehicle List </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('vehicles.attach-device', $vehicle->id) }}" method="post" class="form-horizontal">
                    {{ @csrf_field() }}

                    <div class="form-group">
                        <label for="vehicle" class="control-label col-md-2"> Vehicle </label>
                        <div class="col-md-10">
                            <input type="text" name="vehicle" id="vehicle" disabled value="{{ $vehicle->name . ' ' . $vehicle->license_plate }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="control-label col-md-2"> Telematics Device </label>
                        <div class="col-md-10">
                            <select name="device_id" id="device_id"  class="form-control" required>
                                <option value="" disabled selected> Select device </option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}"> {{ $device->device_name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="form-group">
                            <input type="submit" value="Submit" class="btn btn-primary">
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
        $("#device_id").select2();
    </script>
@endsection
