@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp


    <section class="content" id="store-loading-sheets">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Store Loading Sheet Items - {{ $shift->salesman_route->route_name }} {{ $shift->date }} </h3>
                    <a href="{{ route('salesman-shifts.loading-sheets', $shift->id) }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container"></div>

                <div class="table-responsive">
                    <table class="table list-table" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th> Item Code</th>
                            <th> Item Description</th>
                            <th> Dispatch Quantity</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($dispatch->dispatch_items as $index => $item)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ $item->inventory_item->stock_id_code }}</td>
                                <td>{{ $item->inventory_item->title }}</td>
                                <td>{{ $item->total_quantity }}</td>
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
