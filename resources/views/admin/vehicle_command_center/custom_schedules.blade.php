@extends('layouts.admin.admin')

@section('content')
    <section class="content">
       
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Vehicle Custom Schedules</h3>
                    <div>
                        <a href="{{route('custom-schedules.create')}}" class="btn btn-success">+ Create New</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'custom-schedules', 'method' => 'get']) !!}
                <div class="row">

                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <a class="btn btn-success" href="{!! route('custom-schedules') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Command</th>
                            <th>Vehicles</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($customSchedules as $schedule)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{$schedule->time}}</td>
                                <td>{{ $schedule->action == 0 ? 'Switch On':'Switch Off' }}</td>
                                <td>
                                    @if($schedule->vehicles->isNotEmpty())
                                        {{ $schedule->vehicles->pluck('license_plate_number')->implode(', ') }}
                                    @else
                                        No vehicles
                                    @endif
                                </td>
                                <td>{{$schedule->createdBy?->name}}</td>   
                                <td>{{$schedule->status}}</td>   
                                <td>
                                    <div>
                                        @if ($schedule->status == 'pending')
                                            <a href="{{route('custom-schedules.edit', $schedule->id)}}"><i class="fas fa-edit"></i></a>                           
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
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
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
@endsection
