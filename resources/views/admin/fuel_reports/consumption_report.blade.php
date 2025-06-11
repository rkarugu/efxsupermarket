@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Fuel Consumption Report </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'fuel_consumption_reports.index', 'method' => 'get']) !!}
                <div class="row">
                        <div class="col-md-2 form-group">
                            <select name="branch" id="branch" class="form-control mlselect"  data-url="{{ route('admin.get-branch-routes') }}">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" 
                                        {{ request()->has('branch') ? ($branch->id == request()->branch ? 'selected' : '') : ($branch->id == $authuser->restaurant_id ? 'selected' : '') }}>
                                        {{$branch->name}}
                                    </option>

                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-2 form-group">
                            <select name="vehicle" id="vehicle" class="form-control mlselect">
                                <option value="" selected disabled>Select Vehicle</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}" 
                                        {{ request()->has('vehicle') ? ($vehicle->id == request()->vehicle ? 'selected' : '') : ($vehicle->id == 50 ? 'selected' : '') }}>
                                        {{$vehicle->license_plate_number}}
                                    </option>

                                @endforeach
                            </select>

                        </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                        <a class="btn btn-success btn-sm" href="{!! route('fuel_consumption_reports.index') !!}"><i class="fas fa-eraser"></i> Clear</a>
                    </div>
                </div>

                {!! Form::close(); !!}

               
            </div>
        </div>
        <div class="box box-primary">

            <div class="box-body">
                <canvas id="fuelChart" width="400" height="200"></canvas>


               
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
        });
        var fuelData = @json($fuelData);
        var labels = fuelData.map(function(record) { return record.timestamp; });
        var data = fuelData.map(function(record) { return record.fuel_level; });

        var chart = document.getElementById('fuelChart').getContext('2d');
        var fuelChart = new Chart(chart, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Fuel Level',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Fuel Level - lts'
                        }
                    }
                }
            }
        });
    </script>
@endsection
