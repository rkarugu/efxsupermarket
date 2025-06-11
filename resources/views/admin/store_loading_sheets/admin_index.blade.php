@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp


    <section class="content" id="store-loading-sheets">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Store Loading Sheets </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container"></div>

                <div class="table-responsive">
                    <table class="table list-table" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th> Shift Date</th>
                            <th> Branch</th>
                            <th> Route</th>
                            <th> Salesman</th>
                            <th> Loading Sheets</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($shifts as $index => $shift)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ $shift->date }}</td>
                                <td> {{ $shift->branch }}</td>
                                <td>{{ $shift->salesman_route?->route_name }}</td>
                                <td>{{ $shift->salesman->name }}</td>
                                <td>{{ count($shift->dispatches) }}</td>
                                <td style="width: 10%;">
                                    <div class="action-button-div">
                                        <a href="{{ route('salesman-shifts.loading-sheets', $shift->id)  }}" title="View Loading Sheets"> <i class="fa fa-eye fa-lg text-primary"></i></a>
{{--                                        <a href="{{ route('salesman-shifts.loading-sheets', $shift->id)  }}"> <i class="fa fa-file-pdf fa-lg text-primary"></i></a>--}}
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
@endsection
