@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Geomapping Schedules</h3>
                    <div>
                        {{-- <a href="#" class="btn btn-primary"> Download Report </a> --}}
                        <a href="{{ route('geomapping-schedules.create') }}" class="btn btn-success"
                            style="margin-left: 12px;"> Create Schedule </a>
                    </div>
                </div>
            </div>


            <div class="box-body">
                {!! Form::open(['route' => 'geomapping-schedules.index', 'method' => 'get']) !!}
                <div class="row">
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___geomapping-schedules']))
                        <div class="col-md-4 form-group">
                            <select name="branch" id="branch" class="mlselect form-control">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $branch->id == (request()->branch ?? 2) ? 'selected' : '' }}>{{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    @endif

                    <div class="col-md-4 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        {{-- <input type="submit" class="btn btn-success" name="type" value="Download"> --}}
                        <a class="btn btn-success ml-12" href="{!! route('geomapping-schedules.index') !!}">Clear </a>
                    </div>
                    <div class="col-md-4 d-flex">
                        <p style="margin-left:4px;">
                            <span><i class="fas fa-circle not-started" ></i></span> Not Started 
                        </p>
                        <p style="margin-left:4px;">
                            <span><i class="fas fa-circle incomplete" ></i></span> Incomplete 
                        </p>
                        <p style="margin-left:4px;">
                            <span><i class="fas fa-circle complete" ></i></span> Complete 
                        </p>
                        <p style="margin-left:4px;">
                            <span><i class="fas fa-circle hq-approved" ></i></span> HQ approved 
                        </p>

                    </div>
                </div>

                {!! Form::close() !!}

                <hr>

                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="13" style="text-align: center; font-size: 22px;">
                                    KANINI HARAKA GEOMAPPING SCHEDULES
                                </th>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Sales Rep</th>
                                <th>Sales Rep Contact</th>
                                <th>Supervisor</th>
                                <th>Supervisor Contact</th>
                                <th>Route Manager</th>
                                <th>Route Manager Contact</th>
                                <th>Bizwiz Rep</th>
                                <th>Bizwiz Rep Contact</th>
                                <th>GA Rep</th>
                                <th>GA Rep Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($dates as $index => $date)
                                <tr>
                                    <th scope="row" colspan="13" style="font-size: 18px;">{{ $date }}</th>
                                </tr>
                                @foreach ($list as $item)
                                    @php
                                        $dayOfTheWeek = \Carbon\Carbon::parse($item->date)->dayOfWeek;
                                    @endphp
                                    @if ($dayOfTheWeek == $index)
                                    @if (\Carbon\Carbon::parse($item->date)->isToday() || \Carbon\Carbon::parse($item->date)->isPast())
                                    <tr style="color: white;
                                     @if ($item->status == 'incomplete')
                                            background-color:rgb(101, 85, 95);
                                        @elseif ($item->status == 'completed')
                                            background-color:rgb(0, 166, 255);
                                        @elseif ($item->status == 'HQ-approved')
                                            background-color:green;
                                        @endif
                                    ">
                                        <td>{{ \Carbon\Carbon::parse($item->date)->toDateString() }}</td>
                                        <td>{{ $item->route?->route_name }}</td>
                                        <td>{{ $item->route->salesman() ? $item->route->salesman()->name : 'Not Assigned' }}</td>
                                        <td>{{ $item->route->salesman() ? $item->route->salesman()->phone_number : '-' }}</td>
                                        <td>{{ $item->route_manager }}</td>
                                        <td>{{ $item->route_manager_contact }}</td>
                                        <td>{{ $item->supervisor }}</td>
                                        <td>{{ $item->supervisor_contact }}</td>
                                        <td>{{ $item->bizwiz_rep }}</td>
                                        <td>{{ $item->bizwiz_rep_contact }}</td>
                                        <td>{{ $item->golden_africa_rep }}</td>
                                        <td>{{ $item->golden_africa_rep_contact }}</td>
                                        <td>
                                            <div class="action-button-div">
                                                <a href="{{ route('geomapping-schedules.show', $item->id) }}" title="Detailed Report"><i class="fas fa-eye fa-lg " style="color: white;"></i></a>
                                                @if (isset($my_permissions['route-customers___edit-schedule']))
                                                    <a href="{{ route('geomapping-schedules.edit', $item->id) }}" title="Edit"><i class="fa fa-pen" style="color: white;"></i></a>
                                                @endif
                                                @if (isset($my_permissions['route-customers___shedule-delete']))
                                                <form action="{{route('geomapping-schedules.destroy', $item->id)}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background:transparent; border:none;"><i class="fas fa-trash fa-lg text-primary" style="color:red;"></i></buttton>
    
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->date)->toDateString() }}</td>
                                        <td>{{ $item->route?->route_name }}</td>
                                        <td>{{ $item->route->salesman() ? $item->route->salesman()->name : 'Not Assigned' }}</td>
                                        <td>{{ $item->route->salesman() ? $item->route->salesman()->phone_number : '-' }}</td>
                                        <td>{{ $item->route_manager }}</td>
                                        <td>{{ $item->route_manager_contact }}</td>
                                        <td>{{ $item->supervisor }}</td>
                                        <td>{{ $item->supervisor_contact }}</td>
                                        <td>{{ $item->bizwiz_rep }}</td>
                                        <td>{{ $item->bizwiz_rep_contact }}</td>
                                        <td>{{ $item->golden_africa_rep }}</td>
                                        <td>{{ $item->golden_africa_rep_contact }}</td>
                                        <td>
                                            <div class="action-button-div">
                                                <a href="{{ route('geomapping-schedules.show', $item->id) }}" title="Detailed Report"><i class="fas fa-eye fa-lg text-primary"></i></a>
                                                @if (isset($my_permissions['route-customers___edit-schedule']))
                                                    <a href="{{ route('geomapping-schedules.edit', $item->id) }}" title="Edit"><i class="fa fa-pen" style="color: white;"></i></a>
                                                @endif
                                            
                                            @if (isset($my_permissions['route-customers___shedule-delete']))
                                            <form action="{{route('geomapping-schedules.destroy', $item->id)}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:transparent; border:none;"><i class="fas fa-trash fa-lg text-primary" style="color:red;"></i></buttton>

                                            </form>
                                            @endif
                                            </div>

                                        </td>
                                    </tr>
                                        
                                    @endif
                                     
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .not-started{
            border: 1px solid black;
            border-radius: 50%;
            color: white;
        }
        .hq-approved{
            color: green;
        }
        .complete{
            color: rgb(0, 166, 255);
        }
        .incomplete{
            color:  rgb(101, 85, 95);
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
     $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
        });
        $(function() {

            $(".mlselect").select2();
        });
    </script>
@endsection
