@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Assign Driver </h3>

                    <a role="button" href="{{ route("vehicle.index") }}" class="btn btn-outline-primary"> << Back to Vehicle List </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('vehicles.assign-driver', $vehicle->id) }}" method="post" class="form-horizontal">
                    {{ @csrf_field() }}

                    <div class="form-group">
                        <label for="vehicle" class="control-label col-md-2"> Vehicle </label>
                        <div class="col-md-10">
                            <input type="text" name="vehicle" id="vehicle" disabled value="{{ $vehicle->name . ' ' . $vehicle->license_plate }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="driver_id" class="control-label col-md-2"> Driver </label>
                        <div class="col-md-10">
                            <select name="driver_id" id="driver_id"  class="form-control" required>
                                <option value="" disabled selected> Select driver </option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}"> {{ $driver->name }} </option>
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
        $("#driver_id").select2();
    </script>
@endsection
