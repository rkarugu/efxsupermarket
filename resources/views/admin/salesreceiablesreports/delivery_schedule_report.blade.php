@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Delivery Schedule Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

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
                                    {{-- <option value="{{ $branch->id }}" @if ($branch->id == $user->restaurant_id) selected @endif> {{ $branch->name }} </option> --}}
                                    <option value="{{ $branch->id }}" @if ($branch->id == request()->branch_id) selected @endif>
                                        {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="form-group col-md-3">
                            <label for="route" class="control-label">Select route</label>
                            <select name="route_id" id="route_id" class="form-control" required>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}" @if ($route->id == request()->route_id) selected @endif> {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="form-group col-md-2">
                            <label for="date" class="control-label">Delivery Schedule Date</label>
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

                @if (request()->date)
                    <div class="table-responsive">

                        <table class="table table-bordered table-hover" id="create_datatable_50">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>ROUTE</th>
                                    <th>VAN</th>
                                    <th>DRIVER</th>
                                    <th>CTNS</th>
                                    <th>DZNS</th>
                                    <th>TONNAGE</th>
                                    <th>AMOUNT</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalTonnage = 0;
                                    $totalCtns = 0;
                                    $totalDzns = 0;
                                    $totalAmount = 0;
                                @endphp

                                @foreach ($deliverySchedules as $index => $schedule)
                                    <tr>
                                        <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                        <td> {{ $schedule->route?->route_name ?? '-' }} </td>
                                        <td>{{ $schedule->vehicle?->license_plate_number ?? '-' }}</td>
                                        <td>{{ $schedule->driver?->name ?? '-' }}</td>
                                        <td> {{ $schedule->shift->shift_ctns }} </td>
                                        <td> {{ $schedule->shift->shift_dzns }} </td>
                                        <td> {{ number_format($schedule->shift->shift_tonnage, 2) }} </td>
                                        <td style="text-align: right">
                                            {{ manageAmountFormat($schedule->shift->shift_total) }} </td>



                                    </tr>

                                    @php
                                        $totalTonnage += $schedule->shift->shift_tonnage;
                                        $totalCtns += $schedule->shift->shift_ctns;
                                        $totalDzns += $schedule->shift->shift_dzns;
                                        $totalAmount += $schedule->shift->shift_total;
                                    @endphp
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total</th>
                                    <th>{{ $totalCtns }}</th>
                                    <th>{{ $totalDzns }}</th>
                                    <th>{{ number_format($totalTonnage, 2) }}</th>
                                    <th style="text-align: right">{{ manageAmountFormat($totalAmount) }}</th>
                                </tr>

                            </tfoot>
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
