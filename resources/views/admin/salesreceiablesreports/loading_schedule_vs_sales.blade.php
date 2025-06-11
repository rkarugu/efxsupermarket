@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Loading Schedule vs Stocks Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            {{-- <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Loading Schedule vs Stocks Report </h3>
                </div>
            </div> --}}
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="route" class="control-label">Select branch</label>
                            <select name="branch_id" id="branch_id" class="form-control" required>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if ($branch->id == $user->restaurant_id) selected @endif>
                                        {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="route" class="control-label">Select route</label>
                            <select name="route_id" id="route_id" class="form-control" required>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}" @if ($route->id == request()->route_id) selected @endif>
                                        {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="date" class="control-label">Shift Date</label>
                            <input type="date" name="date" id="date" class="form-control" required
                                value="{{ request()->date }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="date" class="control-label"
                                style="color: white !important; display: block;">Actions</label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary">
                            <input type="submit" name="intent" value="PDF" class="btn btn-primary ml-12">
                        </div>
                    </div>
                </form>

                <hr>

                @if (request()->route_id && request()->date)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Document Number</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandTotal = 0;
                                @endphp

                                @foreach ($invoices as $index => $invoice)
                                    <tr>
                                        <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                        <td> {{ $invoice['document_number'] }} </td>
                                        <td> {{ $invoice['route'] }} </td>
                                        <td> {{ $invoice['date'] }} </td>
                                        <td> {{ number_format($invoice['total'], 2) }} </td>
                                    </tr>

                                    @php
                                        $grandTotal += $invoice['total'];
                                    @endphp
                                @endforeach
                                <tr>
                                    <th colspan="4"
                                        style="text-align: right; border-top: 2px solid black !important; border-bottom: 2px solid black !important;">
                                        Grand Total</th>
                                    <th
                                        style="border-top: 2px solid black !important; border-bottom: 2px solid black !important;">
                                        {{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p> Select a route and a date to continue. </p>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#branch_id").select2();
            $("#route_id").select2();
        });
    </script>
@endsection
