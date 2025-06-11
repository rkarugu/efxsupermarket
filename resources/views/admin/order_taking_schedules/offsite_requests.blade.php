@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Salesman Off-site Shift Requests </h3>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'salesman-shift.offsite-requests', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-3 form-group">
                        <select name="branch" id="branch" class="form-control mlselect" data-url="{{ route('admin.get-branch-routes') }}">
                            <option value="" selected disabled>Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}" 
                                    {{ request()->has('branch') ? ($branch->id == request()->branch ? 'selected' : '') : ($branch->id == $user->restaurant_id ? 'selected' : '') }}>
                                    {{$branch->name}}
                                </option>

                            @endforeach
                        </select>

                    </div>
                    {{-- <div class="col-md-2 form-group">
                        <select name="route" id="route" class="mlselect form-control">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ $route->id == request()->route ? 'selected' : '' }}>{{$route->route_name}}</option>

                            @endforeach
                        </select>

                    </div> --}}

                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <a class="btn btn-success ml-12" href="{!! route('salesman-shift.offsite-requests') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th> Date</th>
                            <th> Route</th>
                            <th> Salesman</th>
                            <th style="width: 30%;"> Reason</th>
                            <th> Status</th>
                            <th> Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($requests as $record)
                            <tr>
                                <th scope="row" style="width: 3%;"> {{ $loop->index + 1}}</th>
                                <td> {{ \Carbon\Carbon::parse($record->created_at)->format('Y-m-d H:i:s') }} </td>
                                <td>{{ $record->route }}</td>
                                <td> {{ $record->salesman_name }} ({{ $record->salesman_number }})</td>
                                <td style="width: 30%;">{{ $record->reason }}</td>
                                <td>{{ $record->status }}</td>
                                <td>
                                    <div class="action-button-div">
                                        @if($record->status == 'pending')
                                            <form action="{{ route('salesman-shift.offsite-requests.approve', $record->id) }}" method="post" title="Approve Request" style="display: inline-block;">
                                                {{ @csrf_field() }}

                                                <button type="submit" class="transparent-btn"><i class="fas fa-thumbs-up text-success fa-lg"></i></button>
                                            </form>

                                            <form action="{{ route('salesman-shift.offsite-requests.decline', $record->id) }}" method="post" title="Decline Request" style="display: inline-block;">
                                                {{ @csrf_field() }}

                                                <button type="submit" class="transparent-btn"><i class="fas fa-user-times text-danger fa-lg"></i></button>
                                            </form>
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
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection
@section('uniquepagescript')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $(".mlselect").select2();
    });
</script>
@endsection
