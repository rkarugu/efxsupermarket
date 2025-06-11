@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title"> {{ $route?->route_name }} {{ strtoupper(request()->ctns_dzns) }} ROUTE SHOPS Report </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="filterForm" action="" method="get">
{{--                    <input type="hidden" name="route_id" id="route_id" value="{{ $route->id }}">--}}
{{--                    <input type="hidden" name="frequency_filter" id="frequency_filter" value="{{ request()->frequency_filter }}">--}}
{{--                    <input type="hidden" name="ctns_dzns" id="ctns_dzns" value="{{ request()->ctns_dzns }}">--}}
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label"> Start Date </label>
                            <input type="date" name="start_date" value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control" />
                        </div>

                        <div class="form-group col-md-2">
                            <label for="" class="control-label"> End Date </label>
                            <input type="date" name="end_date" value="{{ request()->end_date ?? \Carbon\Carbon::now()->toDateString() }}" class="form-control" />
                        </div>
                        <div class="form-group col-md-2">
                            <label for="route" class="control-label"> Select Route </label>
                            <select name="route_id" id="route" class="form-control">
                                <option value="{{ request()->route_id ?? null }}" selected > Select a route</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}" @if (request()->route == $route->id) selected @endif>
                                        {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary ml-12"/>
                        </div>
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

                            <th>ITEM</th>
                            <th>PACK SIZE</th>
                            <th>SELLING_PRICE</th>
                            <th>QTY SOLD</th>
                            <th>QTY RETURNED</th>
                            <th>TOTAL SALES</th>
                            <th>TONNAGE</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($records))
                            @php
                                $totalSales = 0;
                            @endphp
                            @foreach($records as $record)
                                <tr>
                                    <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                    <td>{{ $record->stock_id_code }} - {{ $record->title }}</td>
                                    <td>{{ $record->pack_size }}</td>
                                    <td>{{ manageAmountFormat($record->selling_price) }}</td>
                                    <td>{{ $record->quantity }}</td>
                                    <td>{{ $record->returned_quantity ?? 0 }}</td>
                                    <td>{{ manageAmountFormat(($record->quantity - $record->returned_quantity) * $record->selling_price) }}</td>
                                    <td>{{ number_format($record->tonnage, 4) }}</td>
                                </tr>

                                @php
                                    $totalSales += ($record->quantity - $record->returned_quantity) * $record->selling_price;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th scope="row" style="text-align: center;" colspan="4">TOTALS</th>
                            <th scope="row">{{ $records->sum('quantity') }}</th>
                            <th scope="row">{{ $records->sum('returned_quantity') }}</th>
                            <th scope="row">{{ manageAmountFormat($totalSales) }}</th>
                            <td></td>
                        </tr>
                        </tfoot>
                        @endif

                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script type="text/javascript">
        $(function() {

            $("#route").select2();
            $("#filter").select2();
            $(".new_filters").select2();
            $("#frequency_filter").select2();
            $("#group").select2();
            $("#frequency_filter").val("1").trigger("change");
            $("#filter").val("sales").trigger("change");
        });
    </script>

@endsection


