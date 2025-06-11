@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border  no-padding-h-b">
                <h3 class="box-title"> {!! $title !!} </h3>
                @include('message')


                <div>&nbsp;</div>
                <div class="card tabbable">
                    {!! Form::model($vehicleAssignment, ['method' => 'PATCH','route' => [$model.'.update', $vehicleAssignment],'class'=>'validate','enctype'=>'multipart/form-data']) !!}

                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}

                        <div class="col-md-12">


                            <div class="col-sm-12 form-div  tab-pane active" style="" id="details">
                                <div style="margin-top:30px;">&nbsp;</div>

                                <div class="col-sm-12 form-div">

                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Vehicle
                                            {{ $vehicleAssignment->vehicle_list_id }}</label>
                                        <select name="vehicle_list_id" name="vehicle_list_id" class="form-control m-bot15"
                                            name="type" required="true">
                                            <option value="" disabled>Select Vehicle</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option @if ($vehicle->id == $vehicleAssignment->vehicle_list_id) selected @endif
                                                    value="{{ $vehicle->id }}">{{ $vehicle->vehicle->title }}
                                                    {{ $vehicle->license_plate }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Driver</label>
                                        <select class="form-control m-bot15" name="user_id" name="user_id" required="true">
                                            <option value="" disabled>Select Driver</option>
                                            @foreach ($users as $user)
                                                <option @if ($user->id == $vehicleAssignment->user_id) selected @endif
                                                    value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>

                            </div>

                            <div class="btn-block">
                                <div class="btn-group">
                                    <br>
                                    <button type="submit" class="btn btn-primary">Assign Vehicle</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        .same-btn {
            margin-right: 10px !important;
            border-radius: 3px !important;
            border: 1px solid #c7c7c7;
            color: #000;
        }

        .btn-block {
            display: flex;
            justify-content: end;
        }

        .main-box-ul {
            border-radius: 4px;
            background-color: #c7c7c7;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 10%)
        }

        .form-div .same-form {
            background-color: #fff !important;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 10%) !important;
            padding: 10px 12px !important;
            margin: 10px 0;
        }

        .btn-group .green-btn {
            background-color: #44ace9 !important;
        }
    </style>
@endsection
