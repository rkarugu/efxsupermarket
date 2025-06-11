@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title"> {{ $route->route_name }} {{ strtoupper(request()->ctns_dzns) }} SHOPS REPORT </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="filterForm" action="" method="get">

                    <input type="hidden" name="route_id" id="route_id" value="{{ $route->id }}">
                    <input type="hidden" name="frequency_filter" id="frequency_filter" value="{{ request()->frequency_filter }}">
                    <input type="hidden" name="ctns_dzns" id="ctns_dzns" value="{{ request()->ctns_dzns }}">
                    
                    <div class="row">
{{--                        <div class="form-group col-md-2">--}}
{{--                            <label for="" class="control-label"> Start Date </label>--}}
{{--                            <input type="date" name="start_date" value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control" readonly/>--}}
{{--                        </div>--}}

{{--                        <div class="form-group col-md-2">--}}
{{--                            <label for="" class="control-label"> End Date </label>--}}
{{--                            <input type="date" name="end_date" value="{{ request()->end_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control" readonly/>--}}
{{--                        </div>--}}

                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="EXCEL" class="btn btn-primary ml-12"/>
                        </div>
                    </div>
                </form>

                <hr>


                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>BUSINESS NAME</th>
                            <th>SHOP OWNER NAME</th>
                            <th>SHOP OWNER PHONE</th>
                            <th>DELIVERY CENTER</th>
                            <th>LAST ORDER DATE</th>
                            <th>SHOPS IN SAME CENTER</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = 0;
                            @endphp
                        @foreach($allCustomers as $record)
                        @if (!in_array($record->id, $data))
                        <tr>
                            <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                            <td>{{ $record->bussiness_name }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->phone }}</td>
                            <td>{{ $record->center?->name }}</td>
                            <td>{{ $record->lastOrder?->created_at->format('Y-m-d') }}</td>
                            <td>{{ $record->center ->wa_route_customers_count}}</td>
                        </tr>
                        @php
                            $index += 1;
                        @endphp
                            
                        @endif
                           
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection

