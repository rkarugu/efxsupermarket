@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp


    <section class="content" id="store-loading-sheets">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Store Loading Sheets - {{ $shift->salesman_route->route_name }} {{ $shift->date }} </h3>
                    <a href="{{ route('store-loading-sheets.index') }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container"></div>

                <div class="table-responsive">
                    <table class="table list-table" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th> Bin Location</th>
                            <th> Bin Location Manager</th>
                            <th> Item Count</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($shift->dispatches as $index => $dispatch)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ $dispatch->bin }}</td>
                                <td> {{ $dispatch->bin_manager }}</td>
                                <td>{{ count($dispatch->items) }}</td>
                                <td style="width: 10%;">
                                    <div class="action-button-div">
                                        <a href="{{ route('store-loading-sheets.items', $dispatch->id)  }}" title="View Items"> <i class="fa fa-eye fa-lg text-primary"></i></a>
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
